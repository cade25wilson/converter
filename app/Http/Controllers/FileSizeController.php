<?php

namespace App\Http\Controllers;

use App\Services\FileSizeService;
use Illuminate\Http\JsonResponse;

class FileSizeController extends Controller
{
    protected $fileSizeService;

    public function __construct(FileSizeService $fileSizeService)
    {
        $this->fileSizeService = $fileSizeService;
    }

    public function totalTransferredSize(): JsonResponse
    {
        $totalSize = 0;
        $totalSize += $this->fileSizeService->totalAudioSize();
        $totalSize += $this->fileSizeService->totalImageSize();
        $totalSize += $this->fileSizeService->totalSpreadsheetSize();
        $totalSize += $this->fileSizeService->totalVideoSize();

        return $this->fileSizeService->createResponse($totalSize);
    }

    public function totalTransferredAudioSize(): JsonResponse
    {
        return $this->fileSizeService->createResponse($this->fileSizeService->totalAudioSize());
    }

    public function totalTransferredImageSize(): JsonResponse
    {
        return $this->fileSizeService->createResponse($this->fileSizeService->totalImageSize());
    }

    public function totalTransferredSpreadsheetSize(): JsonResponse
    {
        return $this->fileSizeService->createResponse($this->fileSizeService->totalSpreadsheetSize());
    }

    public function totalTransferredVideoSize(): JsonResponse
    {
        return $this->fileSizeService->createResponse($this->fileSizeService->totalVideoSize());
    }
}
