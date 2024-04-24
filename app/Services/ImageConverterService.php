<?php

namespace App\Services;

use App\Events\ImageConverted;
use App\Jobs\ConvertMultipleImage;
use App\Jobs\ConvertSingleImage;
use App\Models\Format;
use App\Models\Imageconversion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageConverterService
{
    public function SingleImageConvert(Request $request)
    {
        $guid = Str::uuid();
        ImageConverted::dispatch($guid, 'pending');
        $formatId = $request->input('format');
        $image = $request->file('image')[0];
        $image->storeAs('image/' . $guid . '/', $image->getClientOriginalName());

        // Look up the format in the formats table
        $convertedFormat = Format::where('id', $formatId)->value('extension');
        $originalFormatId = Format::where('extension', strtolower($image->getClientOriginalExtension()))->value('id');

        if ($request->file('watermark')) {
            $request->file('watermark')->storeAs('image/' . $guid, $request->file('watermark')->getClientOriginalName());
        } 

        [$width, $height, $watermark] = $this->SetNullableVariables($request);

        $imageConversion = Imageconversion::create([
            'original_name' => $image->getClientOriginalName(),
            'original_format' => $originalFormatId,
            'converted_format' => $formatId,
            'converted_name' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'width' => $width,
            'height' => $height,
            'guid' => $guid,
            'watermark' => $watermark,
        ]);        
        ConvertSingleImage::dispatch($imageConversion);
   
        return $guid;
    }

    public function MultipleImageConvert(Request $request)
    {
        $guid = str::uuid();
        ImageConverted::dispatch($guid, 'processing');

        $images = $request->file('image');
        $formatId = $request->input('format');
        mkdir(storage_path('app/image/' . $guid));

        $convertedFormat = Format::where('id', $formatId)->value('extension');

        if($request->file('watermark')) {
            $request->file('watermark')->storeAs('image/' . $guid, $request->file('watermark')->getClientOriginalName());
        }

        [$width, $height, $watermark] = $this->SetNullableVariables($request);

        $imageConversions = [];
        foreach ($images as $image) {
            $originalFormatId = Format::where('extension', strtolower($image->getClientOriginalExtension()))->value('id');
            $image->storeAs('image/' . $guid, $image->getClientOriginalName());

            $imageConversions[] = [
                'original_name' => $image->getClientOriginalName(),
                'original_format' => $originalFormatId,
                'converted_format' => $formatId,
                'converted_name' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'width' => $width,
                'height' => $height,
                'watermark' => $watermark,
            ];
        }
        Imageconversion::insert($imageConversions);
        ConvertMultipleImage::dispatch($guid, $convertedFormat, $width, $height, $watermark);
        return $guid;
    }

    private function SetNullableVariables($request)
    {
        $width = $request->input('width', null);
        $height = $request->input('height', null);
        $watermark = $request->file('watermark') ? $request->file('watermark')->getClientOriginalName() : null;
        return [$width, $height, $watermark];
    }
}