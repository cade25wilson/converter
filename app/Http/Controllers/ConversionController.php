<?php

namespace App\Http\Controllers;

use App\Rules\Folder;
use App\Services\AudioConverterService;
use App\Services\ArchiveConverterService;
use App\Services\ImageConverterService;
use App\Services\SpreadsheetConverterService;
use App\Services\VideoConverterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConversionController extends Controller
{
    public function imageconvert(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image.*' => 'required',
                'format' => 'required|exists:image_formats,id',
                'email' => 'email',
                'width' => 'numeric|integer|required_with:height',
                'height' => 'numeric|integer|required_with:width',
                'watermark' => 'image'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        $imageService = new ImageConverterService();
        
        if(count($request->image) > 1) {
            $guid = $imageService->MultipleImageConvert($request);
        } else {
            $guid = $imageService->SingleImageConvert($request);
        }

        return response()->json(['message' => 'Conversion Started', 'guid' => $guid]);     
    }

    public function audioconvert(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'audio.*' => 'required|file',
                'format' => 'required|exists:audio_formats,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        $audioService = new AudioConverterService();
        if(count($request->audio) > 1) {
            $guid = $audioService->MultipleAudioConvert($request);
        } else {
            $guid = $audioService->SingleAudioConvert($request);
        }

        return response()->json(['message' => 'Conversion Started', 'guid' => $guid]);
    }

    public function videoconvert(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'video.*' => 'required|file',
                'format' => 'required|exists:video_formats,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $videoService = new VideoConverterService();
        if(count($request->video) > 1) {
            $guid = $videoService->MultipleVideoConvert($request);
        } else {
            $guid = $videoService->SingleVideoConvert($request);
        }

        return response()->json(['message' => 'Conversion Started', 'guid' => $guid]);
    }

    public function spreadsheetconvert(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'spreadsheet.*' => 'required|file',
                'format' => 'required|exists:spreadsheet_formats,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $spreadsheetService = new SpreadsheetConverterService($request);
        if(count($request->spreadsheet) > 1) {
            $guid = $spreadsheetService->MultipleSpreadsheetConvert();
        } else {
            $guid = $spreadsheetService->SingleSpreadsheetConvert();
        }

        return response()->json(['message' => 'Conversion Started', 'guid' => $guid]);
    }

    public function archiveconvert(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'archive.*' => ['required', new Folder],
                'format' => 'required|exists:archive_formats,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $archiveService = new ArchiveConverterService($request);
        if(count($request->archive) > 1) {
            $guid = $archiveService->MultipleArchiveConvert();
        } else {
            $guid = $archiveService->SingleArchiveConvert();
        }

        return response()->json(['message' => 'Conversion Started', 'guid' => $guid]);
    }

    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'guid' => 'required|string',
            'type' => 'required|in:archive,audio,image,spreadsheet,video'
        ]);
        
        $guid = $request->guid;
        $type = $request->type;

        $file = storage_path('app/' . $type . '/' . $guid . '.zip');

        if (!file_exists($file)) {
            return http_response_code(200);
        }

        // Delete the file
        unlink($file);

        return response()->json(['success' => 'File deleted'], 200);
    }
}
