<?php

namespace App\Services;

use App\Events\ImageConverted;

trait FfmpegService
{
    protected function buildFFmpegCommand(
        string $guid,
        string $originalName,
        string $convertedName,
        ?int $width,
        ?int $height,
        ?int $rotationAngle,
        ?string $flip,
        ?int $frameRate,
        ?int $audio,
        bool $fadeIn = false,
        bool $fadeOut = false,
        ?bool $reverse = false,
        string $type = 'video'
    ): string {
        $escapedSourceFile = escapeshellarg(storage_path("app/{$type}/{$guid}/{$originalName}"));
        $escapedDestinationFile = escapeshellarg(storage_path("app/{$type}/{$guid}/{$convertedName}"));

        // Generate the initial FFmpeg command
        $command = "ffmpeg -i {$escapedSourceFile}";

        // Add video filters if applicable
        if ($type === 'video') {
            $command .= $this->addVideoFilters($width, $height, $rotationAngle, $flip);
        }

        // Add frame rate if specified
        if ($frameRate) {
            $command .= " -r {$frameRate}";
        }

        // Add audio volume filter if specified
        if ($audio) {
            $command .= " -af volume={$audio}";
        }

        // Add fade-in and fade-out effects if applicable
        if ($fadeIn || $fadeOut) {
            $command .= $this->addAudioFilters($escapedSourceFile, $fadeIn, $fadeOut);
        }

        // Finalize the command
        $command .= " {$escapedDestinationFile}";

        return $command;
    }

    protected function addVideoFilters(?int $width, ?int $height, ?int $rotationAngle, ?string $flip): string
    {
        $videoFilters = "";

        if ($width && $height) {
            $videoFilters .= $this->addScaleFilter($width, $height);
        }

        if ($rotationAngle) {
            $videoFilters .= $this->addTransposeFilter($rotationAngle);
        }

        if ($flip) {
            $videoFilters .= $this->addFlipFilter($flip);
        }

        return $videoFilters;
    }

    protected function addScaleFilter(int $width, int $height): string
    {
        return " -vf scale={$width}:{$height}";
    }

    protected function addTransposeFilter(int $rotationAngle): string
    {
        return " -vf transpose=" . $this->getFFmpegTransposeValue($rotationAngle);
    }

    protected function addFlipFilter(string $flip): string
    {
        return " -vf " . $this->getFlip($flip);
    }

    private function getFFmpegTransposeValue(int $rotationAngle): int
    {
        switch ($rotationAngle) {
            case 90:
                return 1;
            case 180:
                return 2;
            case 270:
                return 3;
            default:
                return 0;
        }
    }

    private function getFlip(string $flip): string
    {
        switch ($flip) {
            case "h":
                return "hflip";
            case "v":
                return "vflip";
            case "b":
                return "vflip,hflip";
            default:
                return "";
        }
    }

    protected function addAudioFilters(string $filePath, bool $fadeIn, bool $fadeOut): string
    {
        $audioFilters = "";

        if ($fadeIn) {
            $fadeInDuration = $this->calculateFadeDuration($filePath, 0.1); // 10% of the audio duration
            $audioFilters .= "afade=t=in:st=0:d={$fadeInDuration}, ";
        }

        if ($fadeOut) {
            $fadeOutDuration = $this->calculateFadeDuration($filePath, 0.1); // 10% of the audio duration
            $fadeOutStart = $this->calculateFadeOutStart($filePath, 0.1); // 10% of the audio duration
            $audioFilters .= "afade=t=out:st={$fadeOutStart}:d={$fadeOutDuration}, ";
        }

        return $audioFilters ? " -af " . rtrim($audioFilters, ', ') : '';
    }

    private function getAudioDuration(string $filePath): float
    {
        $command = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filePath);
        $duration = trim(shell_exec($command));
        return (float) $duration;
    }

    private function calculateFadeDuration(string $filePath, float $percentage): string
    {
        $duration = $this->getAudioDuration($filePath);
        $fadeDuration = $duration * $percentage;
        return min($fadeDuration, 5); // Cap at 5 seconds
    }

    private function calculateFadeOutStart(string $filePath, float $percentage): string
    {
        $duration = $this->getAudioDuration($filePath);
        $fadeOutDuration = $duration * $percentage;
        return max($duration - $fadeOutDuration, 0); // Ensure start time is not negative
    }

    protected function updateAndComplete(): void
    {
        $this->videoConversion->update(['status' => 'completed']);
        ImageConverted::dispatch($this->videoConversion->guid, 'completed');
    }
}
