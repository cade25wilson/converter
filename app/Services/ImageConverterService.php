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
        $image = $request->file('images');
        $formatName = $request->input('format');
    
        // Look up the format in the formats table
        $format = Format::where('extension', $formatName)->first();
        $origninalFormat = Format::where('extension', strtolower($image->getClientOriginalExtension()))->first();
        $path = $image->storeAs('images', $image->getClientOriginalName());
    
        $imageConversion = Imageconversion::create([
            'original_name' => $image->getClientOriginalName(),
            'original_format' => $origninalFormat->id,
            'converted_format' => $format->id,
            'converted_name' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $format->extension,
            'status' => 'pending',
            'converted_path' => 'images/' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $format->extension,
        ]);
        ConvertSingleImage::dispatch($imageConversion, $path);
    
        return $imageConversion;
    }

    public function MultipleImageConvert(Request $request)
    {
        $guid = $this->generateGuid();

        $images = $request->file('images');
        $format = Format::where('extension', $request->input('format'))->first();
        mkdir(storage_path('app/images/' . $guid));
        $imageConversions = [];
        foreach ($images as $image) {
            $origninalFormat = Format::where('extension', strtolower($image->getClientOriginalExtension()))->first();
            $image->storeAs('images/' . $guid, $image->getClientOriginalName());

            $imageConversions[] = [
                'original_name' => $image->getClientOriginalName(),
                'original_format' => $origninalFormat->id,
                'converted_format' => $format->id,
                'converted_name' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $format->extension,
                'status' => 'pending',
                'guid' => $guid,
                'converted_path' => 'images/' . $guid . '/' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $format->extension,
            ];
        }
        Imageconversion::insert($imageConversions);
        ConvertMultipleImage::dispatch($guid, $format);
    }
}