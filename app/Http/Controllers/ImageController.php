<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertImage;
use App\Models\Format;
use App\Models\Imageconversion;
use App\Services\ImageConverterService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function convert(Request $request)
    {
        // $request->validate([
        //     'images.*' => 'required|image',
        //     'format' => 'required|exists:formats,id',
        //     'email' => 'email',
        //     'width' => 'numeric|integer|required_with:height',
        //     'height' => 'numeric|integer|required_with:width',
        // ]);

        try {
            $request->validate([
                // 'images.*' => 'required|image',
                'images.*' => 'required',
                'format' => 'required|exists:formats,id',
                'email' => 'email',
                'width' => 'numeric|integer|required_with:height',
                'height' => 'numeric|integer|required_with:width',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        // return the images
        $imageService = new ImageConverterService();
        if(count($request->images) > 1) {
            $imageService->MultipleImageConvert($request);
        } else {
            $imageService->SingleImageConvert($request);
        }
        return response()->json(['message' => 'success']);     
    }
}
