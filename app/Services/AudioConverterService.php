<?php

namespace App\Services;

use App\Jobs\ConvertSingleAudio;
use App\Models\Audioconversion;
use App\Models\AudioFormats;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AudioConverterService
{
    public function SingleAudioConvert(Request $request)
    {
        $guid = Str::uuid();
        $audio = $request->file('audio')[0];
        $originalName = $audio->getClientOriginalName();
        $audio->storeAs('audio/' . $guid . '/', $originalName);

        $originalFormat = AudioFormats::where('extension', $audio->getClientOriginalExtension())->value('id');

        $format = AudioFormats::where('id', $request->input('format'))->first();
        $convertedFormat = $format->extension;

        $audioConversion = Audioconversion::create([
            'original_name' => $audio->getClientOriginalName(),
            'original_format' => $originalFormat,
            'converted_format' => $format->id,
            'converted_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $guid,
        ]);

        ConvertSingleAudio::dispatch($audioConversion);

        return $guid;
    }

    public function MultipleAudioConvert(Request $request)
    {

    }
}