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
    protected Request $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function SingleConvert(): string
    {
        $conversionService = new ConversionService();
        $data = $conversionService->SetVariables($this->request, 'video');
        [$width, $height, $frameRate] = $this->SetNullableVariables();
        $format = VideoFormat::where('id', $this->request->input('format'))->first();
        $convertedFormat = $format->extension;

        $videoConversion = VideoConversion::create([
            'original_name' => $data['originalName'],
            'converted_format' => $format->id,
            'converted_name' => pathinfo($data['originalName'], PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $data['guid'],
            'file_size' => $data['file_size'],
            'width' => $width,
            'height' => $height,
            'frame_rate' => $frameRate,
        ]);

        ConvertSingleVideo::dispatch($videoConversion);

        return $data['guid'];
    }

    public function MultipleConvert(): string
    {
        $guid = Str::uuid();
        $videoFiles = $this->request->file('video');
        $videoConversion = [];
        [$width, $height, $frameRate] = $this->SetNullableVariables();

        foreach ($videoFiles as $video) {
            $originalName = $video->getClientOriginalName();
            $video->storeAs('video/' . $guid . '/', $originalName);
            $fileSize = $video->getSize();
            
            $format = VideoFormat::where('id', $this->request->input('format'))->first();
            $convertedFormat = $format->extension;

            $videoConversion[] = VideoConversion::create([
                'original_name' => $video->getClientOriginalName(),
                'converted_format' => $format->id,
                'converted_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'file_size' => $fileSize,
                'width' => $width,
                'height' => $height,
                'frame_rate' => $frameRate,
            ]);
        }

        ConvertMultipleVideo::dispatch($guid, $convertedFormat, $width, $height, $frameRate);

        return $guid;
    }

    private function SetNullableVariables(): array
    {
        $width = $this->request->input('width', null);
        $height = $this->request->input('height', null);
        $frameRate = $this->request->input('frame_rate', null);
        // $watermark = $this->request->file('watermark') ? $this->request->file('watermark')->getClientOriginalName() : null;
        // $stripMetaData = $this->request->input('strip_metadata') ? 1 : 0;
        // $quality = $this->request->input('quality') ? $this->request->input('quality') : 100;
        return [$width, $height, $frameRate];
    }
}