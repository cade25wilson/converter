<?php

namespace App\Services;

use App\Jobs\ConvertMultipleArchive;
use App\Jobs\ConvertSingleArchive;
use App\Services\ConversionService;
use App\Models\ArchiveConversion;
use App\Models\ArchiveFormat;
use Illuminate\Http\Request;

class ArchiveConverterService 
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function SingleArchiveConvert(): string
    {
        $conversionService = new ConversionService();
        $data = $conversionService->SetVariables($this->request, 'archive');

        $format = ArchiveFormat::where('id', $this->request->input('format'))->first();
        $convertedFormat = $format->extension; 

        $archiveConversion = ArchiveConversion::create([
            'original_name' => $data['originalName'],
            'converted_format' => $format->id,
            'converted_name' => pathinfo($data['originalName'], PATHINFO_FILENAME) . '.' . $convertedFormat,
            'status' => 'pending',
            'guid' => $data['guid'],
            'file_size' => $data['file_size'],
        ]);

        ConvertSingleArchive::dispatch($archiveConversion);

        return $data['guid'];
    }

    public function MultipleArchiveConvert(): string
    {
        // code to convert multiple archives
    }
}