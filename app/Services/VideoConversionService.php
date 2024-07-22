<?php

namespace App\Services;

use App\Models\VideoConversion;
use Illuminate\Support\Facades\Log;

trait VideoConversionService
{
    protected function buildFFmpegCommand(string $guid, string $originalName, string $convertedName, ?int $width, ?int $height, ?int $rotationAngle, ?string $flip, ?int $frameRate, ?int $audio): string
    {
        $escapedSourceFile = escapeshellarg(storage_path("app/video/{$guid}/{$originalName}"));
        $escapedDestinationFile = escapeshellarg(storage_path("app/video/{$guid}/{$convertedName}"));

        $command = "ffmpeg -i {$escapedSourceFile}";

        $command .= $this->addVideoFilters($width, $height, $rotationAngle, $flip);

        if ($frameRate) {
            $command .= " -r {$frameRate}";
        }

        if ($audio) {
            $command .= " -af volume={$audio}";
        }

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
}
