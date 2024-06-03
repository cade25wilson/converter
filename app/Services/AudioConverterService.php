<?php

namespace App\Services;

use App\Jobs\ConvertMultipleAudio;
use App\Jobs\ConvertSingleAudio;
use App\Models\Audioconversion;
use App\Models\AudioFormats;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AudioConverterService
{
    protected Request $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function SingleConvert()
    {
        $conversionService = new ConversionService();
        $data = $conversionService->SetVariables($this->request, 'audio');

        // $originalFormat = AudioFormats::where('extension', $data['originalFormat'])->value('id');

        $format = AudioFormats::where('id', $this->request->input('format'))->first();
        $convertedFormat = $format->extension;

        $audioConversion = Audioconversion::create([
            'original_name' => $data['originalName'],
            // 'original_format' => $originalFormat,
            'converted_format' => $format->id,
            'converted_name' => pathinfo($data['originalName'], PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $data['guid'],
            'file_size' => $data['file_size'],
        ]);

        ConvertSingleAudio::dispatch($audioConversion);

        return $data['guid'];
    }

    public function MultipleConvert()
    {
        $guid = Str::uuid();
        $audioFiles = $this->request->file('audio');
        $audioConversion = [];

        foreach ($audioFiles as $audio) {
            $originalName = $audio->getClientOriginalName();
            $audio->storeAs('audio/' . $guid . '/', $originalName);
            $fileSize = $audio->getSize();
            // $originalFormat = AudioFormats::where('extension', $audio->getClientOriginalExtension())->value('id');

            $format = AudioFormats::where('id', $this->request->input('format'))->first();
            $convertedFormat = $format->extension;

            $audioConversion[] = Audioconversion::create([
                'original_name' => $audio->getClientOriginalName(),
                // 'original_format' => $originalFormat,
                'converted_format' => $format->id,
                'converted_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'file_size' => $fileSize,
            ]);
        }

        ConvertMultipleAudio::dispatch($guid, $convertedFormat);

        return $guid;
    }
}