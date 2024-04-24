<?php

namespace App\Services;

use App\Jobs\ConvertSingleSpreadsheet;
use App\Models\SpreadsheetConversion;
use App\Models\SpreadsheetFormat;
use App\Services\ConversionService;
use Illuminate\Http\Request;

class SpreadsheetConverterService
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function SingleSpreadsheetConvert()
    {
        $conversionService = new ConversionService();
        $data = $conversionService->SetVariables($this->request, 'spreadsheet');

        $originalFormat = SpreadsheetFormat::where('extension', $data['originalFormat'])->value('id');

        $format = SpreadsheetFormat::where('id', $this->request->input('format'))->first();
        $convertedFormat = $format->extension;  

        $spreadsheetConversion = SpreadsheetConversion::create([
            'original_name' => $data['originalName'],
            'original_format' => $originalFormat,
            'converted_format' => $format->id,
            'converted_name' => pathinfo($data['originalName'], PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $data['guid'],
        ]);

        ConvertSingleSpreadsheet::dispatch($spreadsheetConversion);
        
        return $data['guid'];
    }

    public function MultipleSpreadsheetConvert() 
    {

    }
}