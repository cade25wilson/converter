<?php

namespace App\Services;

use App\Jobs\ConvertSingleEbook;
use App\Models\EbookConversion;
use App\Models\EbookFormat;
use Illuminate\Http\Request;

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

    public function MultipleConvert()
    {

    }   
}