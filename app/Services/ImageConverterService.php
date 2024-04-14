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
        $guid = uniqid();
        $formatId = $request->input('format');
        $image = $request->file('images')[0];
        $image->storeAs('images/' . $guid . '/', $image->getClientOriginalName());

        // Look up the format in the formats table
        $convertedFormat = Format::where('id', $formatId)->value('extension');
        $originalFormatId = Format::where('extension', strtolower($image->getClientOriginalExtension()))->value('id');

        if ($request->file('watermark')) {
            $request->file('watermark')->storeAs('images/' . $guid, $request->file('watermark')->getClientOriginalName());
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
        $guid = uniqid();

        $images = $request->file('images');
        $formatId = $request->input('format');
        mkdir(storage_path('app/images/' . $guid));

        $convertedFormat = Format::where('id', $formatId)->value('extension');

        if($request->file('watermark')) {
            $request->file('watermark')->storeAs('images/' . $guid, $request->file('watermark')->getClientOriginalName());
        }

        [$width, $height, $watermark] = $this->SetNullableVariables($request);

        $imageConversions = [];
        foreach ($images as $image) {
            $originalFormatId = Format::where('extension', strtolower($image->getClientOriginalExtension()))->value('id');
            $image->storeAs('images/' . $guid, $image->getClientOriginalName());

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

    public static function ZipImages($guid): void
    {
        $zip = new \ZipArchive();
        $zipFileName = storage_path('app/images/' . $guid . '.zip');

        if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            $options = array('add_path' => 'images/', 'remove_all_path' => TRUE);
            $zip->addGlob(storage_path('app/images/' . $guid . '/*'), GLOB_BRACE, $options);
            $zip->close();
        }
    }
    
    public static function deleteDirectory($dir): void
    {
        if (!file_exists($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }

    public static function updateStatus(string $status, string $guid): void
    {
        Imageconversion::where('guid', $guid)->update(['status' => $status]);
    }
}