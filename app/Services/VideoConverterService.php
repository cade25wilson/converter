<?php

namespace App\Services;

use App\Jobs\ConvertSingleVideo;
use App\Jobs\ConvertMultipleVideo;
use App\Models\VideoConversion;
use App\Models\VideoFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoConverterService
{

    public function SingleVideoConvert(Request $request)
    {
        $conversionService = new ConversionService();
        $data = $conversionService->SetVariables($request, 'video');

        // $originalFormat = VideoFormat::where('extension', $data['originalFormat'])->value('id');

        $format = VideoFormat::where('id', $request->input('format'))->first();
        $convertedFormat = $format->extension;

        $videoConversion = VideoConversion::create([
            'original_name' => $data['originalName'],
            // 'original_format' => $originalFormat,
            'converted_format' => $format->id,
            'converted_name' => pathinfo($data['originalName'], PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $data['guid'],
            'file_size' => $data['file_size'],
        ]);

        ConvertSingleVideo::dispatch($videoConversion);

        return $data['guid'];
    }

    public function MultipleVideoConvert(Request $request)
    {
        $guid = Str::uuid();
        $videoFiles = $request->file('video');
        $videoConversion = [];

        foreach ($videoFiles as $video) {
            $originalName = $video->getClientOriginalName();
            $video->storeAs('video/' . $guid . '/', $originalName);
            $fileSize = $video->getSize();
            // $originalFormat = VideoFormat::where('extension', $video->getClientOriginalExtension())->value('id');
            
            $format = VideoFormat::where('id', $request->input('format'))->first();
            $convertedFormat = $format->extension;

            $videoConversion[] = VideoConversion::create([
                'original_name' => $video->getClientOriginalName(),
                // 'original_format' => $originalFormat,
                'converted_format' => $format->id,
                'converted_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'file_size' => $fileSize,
            ]);
        }

        ConvertMultipleVideo::dispatch($guid, $convertedFormat);

        return $guid;
    }
}