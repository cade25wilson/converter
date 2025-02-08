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
        bool $reverse = false,
        string $type = 'video'
    ): string {
        $escapedSourceFile = escapeshellarg(storage_path("app/{$type}/{$guid}/{$originalName}"));
        $escapedDestinationFile = escapeshellarg(storage_path("app/{$type}/{$guid}/{$convertedName}"));

        $command = "ffmpeg -i {$escapedSourceFile}";

        if ($type === 'video') {
            $command .= $this->addVideoFilters($width, $height, $rotationAngle, $flip);
        }

        if ($frameRate) {
            $command .= " -r {$frameRate}";
        }

        $audioFilters = $this->buildAudioFilters($audio, $fadeIn, $fadeOut, $reverse, $escapedSourceFile);

        if ($audioFilters) {
            $command .= " -af \"{$audioFilters}\"";
        }

        $command .= " {$escapedDestinationFile}";

        return $command;
    }

    protected function addVideoFilters(?int $width, ?int $height, ?int $rotationAngle, ?string $flip): string
    {
        $videoFilters = [];

        if ($width && $height) {
            $videoFilters[] = "scale={$width}:{$height}";
        }

        if ($rotationAngle) {
            $videoFilters[] = "transpose=" . $this->getFFmpegTransposeValue($rotationAngle);
        }

        if ($flip) {
            $videoFilters[] = $this->getFlip($flip);
        }

        return !empty($videoFilters) ? " -vf \"" . implode(',', $videoFilters) . "\"" : '';

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

    protected function buildAudioFilters(?int $audio, bool $fadeIn, bool $fadeOut, bool $reverse, string $filePath): string
    {
        $filters = [];

        if ($audio && $audio != 1) {
            $filters[] = "volume={$audio}";
        }

        if ($fadeIn) {
            $fadeInDuration = $this->calculateFadeDuration($filePath, 0.1);
            $filters[] = "afade=t=in:st=0:d={$fadeInDuration}";
        }

        if ($fadeOut) {
            $fadeOutDuration = $this->calculateFadeDuration($filePath, 0.1);
            $fadeOutStart = $this->calculateFadeOutStart($filePath, 0.1);
            $filters[] = "afade=t=out:st={$fadeOutStart}:d={$fadeOutDuration}";
        }

        if ($reverse) {
            $filters[] = "areverse";
        }

        return implode(', ', $filters);
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
        return min($fadeDuration, 5);
    }

    private function calculateFadeOutStart(string $filePath, float $percentage): string
    {
        $duration = $this->getAudioDuration($filePath);
        $fadeOutDuration = $duration * $percentage;
        return max($duration - $fadeOutDuration, 0);
    }

    protected function updateAndComplete(): void
    {
        $this->videoConversion->update(['status' => 'completed']);
        ImageConverted::dispatch($this->videoConversion->guid, 'completed');
    }
}
