<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertImage;
use App\Models\Format;
use App\Models\Imageconversion;
use App\Services\ImageConverterService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function convert(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image',
            'format' => 'required|exists:formats,extension',
            'email' => 'email',
        ]);

        $imageService = new ImageConverterService();
        if(is_array($request->file('images'))) {
            $images = $imageService->MultipleImageConvert($request);
        } else {
            $image = $imageService->SingleImageConvert($request);
        }

        return response()->json(['message' => 'success']);     
    }
}
