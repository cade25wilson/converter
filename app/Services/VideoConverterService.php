<?php

namespace App\Services;

use App\Events\ImageConverted;
use App\Jobs\ConvertSingleVideo;
use App\Jobs\ConvertMultipleVideo;
use App\Models\VideoConversion;
use App\Models\VideoFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;

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
        [$width, $height, $frameRate, $rotationAngle, $flip, $audio] = $this->SetNullableVariables();
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
            'rotation_angle' => $rotationAngle,
            'flip' => $flip,
            'audio' => $audio,
        ]);

        ConvertSingleVideo::dispatch($videoConversion);

        return $data['guid'];
    }

    public function MultipleConvert(): string
    {
        $guid = Str::uuid();
        $videoFiles = $this->request->file('video');
        $videoConversion = [];
        [$width, $height, $frameRate, $rotationAngle, $flip, $audio] = $this->SetNullableVariables();

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
                'rotation_angle' => $rotationAngle,
                'flip' => $flip,
                'audio' => $audio,
            ]);
        }

        ConvertMultipleVideo::dispatch($guid, $convertedFormat, $width, $height, $frameRate, $rotationAngle, $flip, $audio);

        return $guid;
    }

    private function SetNullableVariables(): array
    {
        $width = $this->request->input('width', null);
        $height = $this->request->input('height', null);
        $frameRate = $this->request->input('frame_rate', null);
        $rotationAngle = $this->request->input('rotation_angle', null);
        $flip = $this->request->input('flip', null);
        $audio = $this->request->input('audio_volume') / 100 ?? null;
        return [$width, $height, $frameRate, $rotationAngle, $flip, $audio];
    }
}