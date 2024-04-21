<?php

namespace App\Services;

use App\Jobs\ConvertSingleVideo;
use App\Models\VideoConversion;
use App\Models\VideoFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoConverterService
{

    public function SingleVideoConvert(Request $request)
    {
        // convert to function in conversionservice
        $guid = Str::uuid();
        $video = $request->file('video')[0];
        $originalName = $video->getClientOriginalName();
        $video->storeas('video/' . $guid . '/', $originalName);

        $originalFormat = VideoFormat::where('extension', $video->getClientOriginalExtension())->value('id');

        $format = VideoFormat::where('id', $request->input('format'))->first();
        $convertedFormat = $format->extension;

        $videoConversion = VideoConversion::create([
            'original_name' => $video->getClientOriginalName(),
            'original_format' => $originalFormat,
            'converted_format' => $format->id,
            'converted_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $guid,
        ]);

        ConvertSingleVideo::dispatch($videoConversion);

        return $guid;
    }

    public function MultipleVideoConvert(Request $request)
    {

    }
}