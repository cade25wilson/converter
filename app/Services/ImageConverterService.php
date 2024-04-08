<?php

namespace App\Services;

use App\Jobs\ConvertMultipleImage;
use App\Jobs\ConvertSingleImage;
use App\Models\Format;
use App\Models\Imageconversion;
use Illuminate\Http\Request;

class ImageConverterService
{
    public function SingleImageConvert(Request $request)
    {
        $images = $request->file('images');
        $formatId = $request->input('format');
        $image = $images[0];
        //save image to app/images
        $image->storeAs('images', $image->getClientOriginalName());

        // Look up the format in the formats table
        $convertedFormat = Format::where('id', $formatId)->value('extension');
        $originalFormatId = Format::where('extension', strtolower($image->getClientOriginalExtension()))->value('id');

        $width = $request->input('width', null);
        $height = $request->input('height', null);

        $imageConversion = Imageconversion::create([
            'original_name' => $image->getClientOriginalName(),
            'original_format' => $originalFormatId,
            'converted_format' => $formatId,
            'converted_name' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'width' => $width,
            'height' => $height,
        ]);

        ConvertSingleImage::dispatch($imageConversion);
   
        return $imageConversion;
    }

    public function MultipleImageConvert(Request $request)
    {
        $guid = uniqid();

        $images = $request->file('images');
        $formatId = $request->input('format');
        mkdir(storage_path('app/images/' . $guid));

        $convertedFormat = Format::where('id', $formatId)->value('extension');

        $width = $request->input('width', null);
        $height = $request->input('height', null);

        $imageConversions = [];
        foreach ($images as $image) {
            $originalFormatId = Format::where('extension', strtolower($image->getClientOriginalExtension()))->value('id');
            $image->storeAs('images/' . $guid, $image->getClientOriginalName());

            $imageConversions[] = [
                'original_name' => $image->getClientOriginalName(),
                'original_format' => $originalFormatId,
                'converted_format' => $convertedFormat,
                'converted_name' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'width' => $width,
                'height' => $height,
            ];
        }
        Imageconversion::insert($imageConversions);
        ConvertMultipleImage::dispatch($guid, $convertedFormat);
    }
}