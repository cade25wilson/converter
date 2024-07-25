<?php

namespace App\Services;

use App\Events\ImageConverted;
use App\Jobs\ConvertMultipleImage;
use App\Jobs\ConvertSingleImage;
use App\Models\Format;
use App\Models\Imageconversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageConverterService
{
    protected Request $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }   

    public function SingleConvert(): string
    {
        $guid = Str::uuid();
        ImageConverted::dispatch($guid, 'pending');
        $formatId = $this->request->input('format');
        $image = $this->request->file('image')[0];
        $sizeInBytes = $image->getSize();
        $image->storeAs('image/' . $guid . '/', $image->getClientOriginalName());

        $convertedFormat = Format::where('id', $formatId)->value('extension');

        if ($this->request->file('watermark')) {
            $this->request->file('watermark')->storeAs('image/' . $guid, $this->request->file('watermark')->getClientOriginalName());
        } 

        [$width, $height, $watermark, $stripMetaData, $quality] = $this->SetNullableVariables($this->request);

        $imageConversion = Imageconversion::create([
            'original_name' => $image->getClientOriginalName(),
            'converted_format' => $formatId,
            'converted_name' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'width' => $width,
            'height' => $height,
            'guid' => $guid,
            'watermark' => $watermark,
            'file_size' => $sizeInBytes,
            'strip_metadata' => $stripMetaData,
            'quality' => $quality,
        ]);        
        ConvertSingleImage::dispatch($imageConversion);
   
        return $guid;
    }

    public function MultipleConvert(): string
    {
        $guid = str::uuid();
        ImageConverted::dispatch($guid, 'processing');

        $images = $this->request->file('image');
        $formatId = $this->request->input('format');
        mkdir(storage_path('app/image/' . $guid));

        $convertedFormat = Format::where('id', $formatId)->value('extension');

        if($this->request->file('watermark')) {
            $this->request->file('watermark')->storeAs('image/' . $guid, $this->request->file('watermark')->getClientOriginalName());
        }

        [$width, $height, $watermark, $stripMetaData, $quality] = $this->SetNullableVariables($this->request);

        $imageConversions = [];
        foreach ($images as $image) {
            $image->storeAs('image/' . $guid, $image->getClientOriginalName());
            $sizeInBytes = $image->getSize();
            $imageConversions[] = [
                'original_name' => $image->getClientOriginalName(),
                'converted_format' => $formatId,
                'converted_name' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'width' => $width,
                'height' => $height,
                'watermark' => $watermark,
                'file_size' => $sizeInBytes,
                'strip_metadata' => $stripMetaData,
                'quality' => $quality,
            ];
        }
        Imageconversion::insert($imageConversions);
        ConvertMultipleImage::dispatch($guid, $convertedFormat, $width, $height, $watermark, $stripMetaData, $quality);
        return $guid;
    }

    private function SetNullableVariables(): array
    {
        $width = $this->request->input('width', null);
        $height = $this->request->input('height', null);
        $watermark = $this->request->file('watermark') ? $this->request->file('watermark')->getClientOriginalName() : null;
        $stripMetaData = $this->request->input('strip_metadata') ? 1 : 0;
        $quality = $this->request->input('quality', 100); // Default to 100 if not provided
        return [$width, $height, $watermark, $stripMetaData, $quality];
    }
}