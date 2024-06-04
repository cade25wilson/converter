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

    public function totalTransferredSize($type): JsonResponse
    {
        switch($type){
            case 'all':
                return $this->totalTranssferredSizeAll();
            case 'archive':
                return $this->totalTransferredArchiveSize();
            case 'audio':
                return $this->totalTransferredAudioSize();
            case 'ebook':
                return $this->totalTransferredEbookSize();
            case 'image':
                return $this->totalTransferredImageSize();
            case 'spreadsheet':
                return $this->totalTransferredSpreadsheetSize();
            case 'video':
                return $this->totalTransferredVideoSize();

            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }

    public function totalTranssferredSizeAll(): JsonResponse
    {
        $totalSize = 0;
        $totalSize += $this->fileSizeService->totalArchiveSize();
        $totalSize += $this->fileSizeService->totalAudioSize();
        $totalSize += $this->fileSizeService->totalEbookSize();
        $totalSize += $this->fileSizeService->totalImageSize();
        $totalSize += $this->fileSizeService->totalSpreadsheetSize();
        $totalSize += $this->fileSizeService->totalVideoSize();

        return $this->fileSizeService->createResponse($totalSize);
    }

    public function totalTransferredArchiveSize(): JsonResponse
    {
        return $this->fileSizeService->createResponse($this->fileSizeService->totalArchiveSize());
    }

    public function totalTransferredAudioSize(): JsonResponse
    {
        return $this->fileSizeService->createResponse($this->fileSizeService->totalAudioSize());
    }

    public function totalTransferredEbookSize(): JsonResponse
    {
        return $this->fileSizeService->createResponse($this->fileSizeService->totalEbookSize());
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
