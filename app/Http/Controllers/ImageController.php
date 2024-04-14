<?php

namespace App\Http\Controllers;

use App\Services\ImageConverterService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function Convert(Request $request)
    {
        try {
            $request->validate([
                'images.*' => 'required',
                'format' => 'required|exists:formats,id',
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
            $imageService->MultipleImageConvert($request);
        } else {
            $imageService->SingleImageConvert($request);
        }

        return response()->json(['message' => 'success']);     
    }
}
