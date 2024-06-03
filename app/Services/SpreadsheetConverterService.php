<?php

namespace App\Services;

use App\Jobs\ConvertSingleSpreadsheet;
use App\Jobs\ConvertMultipleSpreadsheet;
use App\Models\SpreadsheetConversion;
use App\Models\SpreadsheetFormat;
use App\Services\ConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SpreadsheetConverterService
{
    protected Request $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function SingleConvert()
    {
        $conversionService = new ConversionService();
        $data = $conversionService->SetVariables($this->request, 'spreadsheet');

        // $originalFormat = SpreadsheetFormat::where('extension', $data['originalFormat'])->value('id');

        $format = SpreadsheetFormat::where('id', $this->request->input('format'))->first();
        $convertedFormat = $format->extension;  

        $spreadsheetConversion = SpreadsheetConversion::create([
            'original_name' => $data['originalName'],
            // 'original_format' => $originalFormat,
            'converted_format' => $format->id,
            'converted_name' => pathinfo($data['originalName'], PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $data['guid'],
            'file_size' => $data['file_size'],
        ]);

        ConvertSingleSpreadsheet::dispatch($spreadsheetConversion);
        
        return $data['guid'];
    }

    public function MultipleConvert() 
    {
        $guid = Str::uuid();
        $spreadsheetFiles = $this->request->file('spreadsheet');
        $spreadsheetConversion = [];

        foreach($spreadsheetFiles as $spreadsheet){
            $originalName = $spreadsheet->getClientOriginalName();
            $spreadsheet->storeAs('spreadsheet/' . $guid . '/', $originalName);
            $fileSize = $spreadsheet->getSize();
            // $originalFormat = SpreadsheetFormat::where('extension', $spreadsheet->getClientOriginalExtension())->value('id');

            $format = SpreadsheetFormat::where('id', $this->request->input('format'))->first();
            $convertedFormat = $format->extension;

            $spreadsheetConversion[] = SpreadsheetConversion::create([
                'original_name' => $spreadsheet->getClientOriginalName(),
                // 'original_format' => $originalFormat,
                'converted_format' => $format->id,
                'converted_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'file_size' => $fileSize,
            ]);
        }

        ConvertMultipleSpreadsheet::dispatch($guid, $convertedFormat);

        return $guid;   
    }
}