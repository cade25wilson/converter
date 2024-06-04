<?php

namespace App\Services;

use App\Jobs\ConvertSingleEbook;
use App\Jobs\ConvertMultipleEbook;
use App\Models\EbookConversion;
use App\Models\EbookFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EbookConverterService
{
    protected Request $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function SingleConvert(): string
    {
        $conversionService = new ConversionService();
        $data = $conversionService->SetVariables($this->request, 'ebook');

        $format = EbookFormat::where('id', $this->request->input('format'))->first();
        $convertedFormat = $format->extension;

        $ebookConversion = EbookConversion::create([
            'original_name' => $data['originalName'],
            'converted_format' => $format->id,
            'converted_name' => pathinfo($data['originalName'], PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $data['guid'],
            'file_size' => $data['file_size'],
        ]);

        ConvertSingleEbook::dispatch($ebookConversion);

        return $data['guid'];
    }

    public function MultipleConvert(): string
    {
        $guid = str::uuid();
        $ebookFiles = $this->request->file('ebook');
        $ebookConversion = [];

        foreach ($ebookFiles as $ebook) {
            $originalName = $ebook->getClientOriginalName();
            $ebook->storeAs('ebook/' . $guid . '/', $originalName);
            $fileSize = $ebook->getSize();

            $format = EbookFormat::where('id', $this->request->input('format'))->first();
            $convertedFormat = $format->extension;

            $ebookConversion[] = EbookConversion::create([
                'original_name' => $originalName,
                'converted_format' => $format->id,
                'converted_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'file_size' => $fileSize,
            ]);
        }

        ConvertMultipleEbook::dispatch($guid, $convertedFormat);

        return $guid;
    }   
}