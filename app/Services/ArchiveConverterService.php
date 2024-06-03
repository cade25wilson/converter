<?php

namespace App\Services;

use App\Jobs\ConvertMultipleArchive;
use App\Jobs\ConvertSingleArchive;
use App\Services\ConversionService;
use App\Models\ArchiveConversion;
use App\Models\ArchiveFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArchiveConverterService 
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function SingleConvert(): string
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

    public function MultipleConvert(): string
    {
        $guid = Str::uuid();
        $archiveFiles = $this->request->file('archive');
        $archiveConversion = [];

        foreach ($archiveFiles as $archive) {
            $originalName = $archive->getClientOriginalName();
            $archive->storeAs('archive/' . $guid . '/', $originalName);
            $fileSize = $archive->getSize();

            $format = ArchiveFormat::where('id', $this->request->input('format'))->first();
            $convertedFormat = $format->extension;

            $archiveConversion[] = ArchiveConversion::create([
                'original_name' => $archive->getClientOriginalName(),
                'converted_format' => $format->id,
                'converted_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.' . $convertedFormat,
                'status' => 'pending',
                'guid' => $guid,
                'file_size' => $fileSize,
            ]);
        }

        ConvertMultipleArchive::dispatch($guid, $convertedFormat);
        return $guid;
    }
}