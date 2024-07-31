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
        [$audio, $fadeIn, $fadeOut, $reverseAudio] = $this->setNullableVariables();
        $format = AudioFormats::where('id', $this->request->input('format'))->first();
        $convertedFormat = $format->extension;

        $audioConversion = Audioconversion::create([
            'original_name' => $data['originalName'],
            'converted_format' => $format->id,
            'converted_name' => pathinfo($data['originalName'], PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $data['guid'],
            'file_size' => $data['file_size'],
            'audio' => $audio,
            'fade_in' => $fadeIn,
            'fade_out' => $fadeOut,
            'reverse_audio' => $reverseAudio,
        ]);

        ConvertSingleAudio::dispatch($audioConversion);

        return $data['guid'];
    }

    public function MultipleConvert()
    {
        $guid = Str::uuid();
        $audioFiles = $this->request->file('audio');
        [$audio, $fadeIn, $fadeOut, $reverseAudio] = $this->setNullableVariables();
        $audioConversion = [];

        foreach ($audioFiles as $audios) {
            $originalName = $audios->getClientOriginalName();
            $audios->storeAs('audio/' . $guid . '/', $originalName);
            $fileSize = $audios->getSize();
            
            $format = AudioFormats::where('id', $this->request->input('format'))->first();
            $convertedFormat = $format->extension;

            $audioConversion[] = Audioconversion::create([
                'original_name' => $audios->getClientOriginalName(),
                'converted_format' => $format->id,
                'converted_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'file_size' => $fileSize,
                'audio' => $audio,
                'fade_in' => $fadeIn,
                'fade_out' => $fadeOut,
                'reverse_audio' => $reverseAudio,
            ]);
        }

        ConvertMultipleAudio::dispatch($guid, $convertedFormat, $audio, $fadeIn, $fadeOut, $reverseAudio);

        return $guid;
    }

    private function setNullableVariables(): array
    {
        $audio = $this->request->input('audio_volume') / 100 ?? null;
        $fadeIn = $this->request->input('fade_in', false);
        $fadeOut = $this->request->input('fade_out', false);
        $reverseAudio = $this->request->input('reverse_audio', false);
        return [$audio, $fadeIn, $fadeOut, $reverseAudio];
    }
}