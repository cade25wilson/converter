<?php

namespace App\Services;

use App\Models\ArchiveConversion;
use App\Models\Audioconversion;
use App\Models\Imageconversion;
use App\Models\SpreadsheetConversion;
use App\Models\VideoConversion;
use Illuminate\Http\JsonResponse;

class FileSizeService 
{
    private const CACHE_KEYS = [
        'archive' => 'total_archive_size',
        'audio' => 'total_audio_size',
        'image' => 'total_image_size',
        'spreadsheet' => 'total_spreadsheet_size',
        'video' => 'total_video_size',
        'transferred' => 'total_transferred_size',
        'transferred_audio' => 'total_transfered_audio_size',
    ];

    private function totalInGB(int $totalSize): float
    {
        return round($totalSize / 1073741824, 2);
    }

    public function totalArchiveSize(): array
    {
        return $this->getTotalSize(self::CACHE_KEYS['archive'], ArchiveConversion::class);
    }

    public function totalAudioSize(): array
    {
        return $this->getTotalSize(self::CACHE_KEYS['audio'], Audioconversion::class);
    }

    public function totalImageSize(): array
    {
        return $this->getTotalSize(self::CACHE_KEYS['image'], Imageconversion::class);
    }

    public function totalSpreadsheetSize(): array
    {
        return $this->getTotalSize(self::CACHE_KEYS['spreadsheet'], SpreadsheetConversion::class);
    }

    public function totalVideoSize(): array
    {
        return $this->getTotalSize(self::CACHE_KEYS['video'], VideoConversion::class);
    }
    
    private function getTotalSize(string $key, string $modelClass): array
    {
        if ($this->checkCache($key)) {
            return $this->getCache($key);
        } 

        $totalFiles = $modelClass::count();
        $totalSize = $modelClass::where('status', 'completed')->sum('file_size');
        $data = [
            'total_files' => $totalFiles,
            'total_size' => $totalSize,
        ];
        $this->setCache($key, $data);

        return $data;
    }

    public function createResponse(array $totalSize): JsonResponse
    {
        return response()->json(['total_size' => $this->totalInGB($totalSize['total_size']), 'total_files' => $totalSize['total_files']]);
    }

    private function checkCache(string $key): bool
    {
        return cache()->has($key);
    }

    private function getCache(string $key): array
    {
        return cache()->get($key);
    }

    private function setCache(string $key, array $value): void
    {
        cache()->put($key, $value, 30);
    }
}