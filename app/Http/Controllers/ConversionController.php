<?php

namespace App\Http\Controllers;

use App\Services\AudioConverterService;
use App\Services\ImageConverterService;
use App\Services\VideoConverterService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class ConversionController extends Controller
{
    public function imageconvert(Request $request)
    {
        try {
            $request->validate([
                'images.*' => 'required',
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
        
        if(count($request->images) > 1) {
            $guid = $imageService->MultipleImageConvert($request);
        } else {
            $guid = $imageService->SingleImageConvert($request);
        }

        return response()->json(['message' => 'Conversion Started', 'guid' => $guid]);     
    }

    public function audioconvert(Request $request)
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

    public function videoconvert(Request $request)
    {
        try {
            $request->validate([
                'video.*' => 'required|file',
                'format'
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
}
