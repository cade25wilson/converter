<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\SpreadsheetConversion;
use App\Services\ConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ConvertSingleSpreadsheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected SpreadsheetConversion $spreadsheetConversion;
    /**
     * Create a new job instance.
     */
    public function __construct(SpreadsheetConversion $spreadsheetConversion)
    {
        $this->spreadsheetConversion = $spreadsheetConversion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $this->spreadsheetConversion->update(['status' => 'processing']);
            ImageConverted::dispatch($this->spreadsheetConversion->guid, 'processing');

            // WE'LL IMPLEMENT THIS BAD BOY LATER
            // ConversionService::SetFilePaths($this->spreadsheetConversion, 'spreadsheet');
            $sourceFile = '/var/www/converter/storage/app/spreadsheet/' . $this->spreadsheetConversion->guid . '/' . $this->spreadsheetConversion->original_name;
            $destinationPath = '/var/www/converter/storage/app/spreadsheet/' . $this->spreadsheetConversion->guid;
            $destinationFilename = $this->spreadsheetConversion->converted_name;

            $extension = $this->spreadsheetConversion->convertedFormat->extension;
            $spreadsheet = IOFactory::load($sourceFile);

            $writer = IOFactory::createWriter($spreadsheet, ucfirst($extension));
            $writer->save("$destinationPath/$destinationFilename");

            unlink($sourceFile);
            ConversionService::ZipFiles($this->spreadsheetConversion->guid, 'spreadsheet');
            ConversionService::DeleteDirectory(storage_path('app/spreadsheet/' . $this->spreadsheetConversion->guid));
            $this->spreadsheetConversion->update(['status' => 'completed']);
            ImageConverted::dispatch($this->spreadsheetConversion->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->spreadsheetConversion->update(['status' => 'failed']);
            ImageConverted::dispatch($this->spreadsheetConversion->guid, 'failed');
        }
    }
}
