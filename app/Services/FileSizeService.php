<?php

namespace App\Services;

use App\Models\Audioconversion;
use App\Models\Imageconversion;
use App\Models\SpreadsheetConversion;
use App\Models\VideoConversion;
use Illuminate\Http\JsonResponse;

class FileSizeService 
{
    private const CACHE_KEYS = [
        'audio' => 'total_audio_size',
        'image' => 'total_image_size',
        'spreadsheet' => 'total_spreadsheet_size',
        'video' => 'total_video_size',
        'transferred' => 'total_transferred_size',
        'transferred_audio' => 'total_transfered_audio_size',
    ];

    private function totalInGB(int $totalSize): float
    {
        return $totalSize / 1073741824;
    }

    public function totalAudioSize(): int
    {
        return $this->getTotalSize(self::CACHE_KEYS['audio'], Audioconversion::class);
    }

    public function totalImageSize(): int
    {
        return $this->getTotalSize(self::CACHE_KEYS['image'], Imageconversion::class);
    }

    public function totalSpreadsheetSize(): int
    {
        return $this->getTotalSize(self::CACHE_KEYS['spreadsheet'], SpreadsheetConversion::class);
    }

    public function totalVideoSize(): int
    {
        return $this->getTotalSize(self::CACHE_KEYS['video'], VideoConversion::class);
    }
    
    private function getTotalSize(string $key, string $modelClass): int
    {
        if ($this->checkCache($key)) {
            return $this->getCache($key);
        } 

        $totalSize = $modelClass::sum('file_size');
        $this->setCache($key, $totalSize);

        return $totalSize;
    }

    public function createResponse(int $totalSize): JsonResponse
    {
        return response()->json(['total_size' => $this->totalInGB($totalSize)]);
    }

    private function checkCache(string $key): bool
    {
        return cache()->has($key);
    }

    private function getCache(string $key): int
    {
        return cache()->get($key);
    }

    private function setCache(string $key, int $value): void
    {
        cache()->put($key, $value, 30);
    }
}