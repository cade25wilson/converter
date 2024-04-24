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

class ConvertMultipleSpreadsheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $guid;
    protected string $format;
    /**
     * Create a new job instance.
     */
    public function __construct(string $guid, string $format)
    {
        $this->guid = $guid;
        $this->format = $format;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $spreadsheetPath = storage_path('app/spreadsheet/' . $this->guid);
            $spreadsheets = array_diff(scandir($spreadsheetPath), ['.', '..']);

            if (empty($spreadsheets)) {
                return;
            }

            SpreadsheetConversion::where('guid', $this->guid)->update(['status' => 'processing']);
            ImageConverted::dispatch($this->guid, 'processing');

            foreach ($spreadsheets as $spreadsheet) {
                $this->processSpreadsheet($spreadsheetPath, $spreadsheet);
            }

            ConversionService::ZipFiles($this->guid, 'spreadsheet');
            ConversionService::DeleteDirectory($spreadsheetPath);
            SpreadsheetConversion::where('guid', $this->guid)->update(['status' => 'completed']);
            ImageConverted::dispatch($this->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            SpreadsheetConversion::where('guid', $this->guid)->update(['status' => 'failed']);
            ImageConverted::dispatch($this->guid, 'failed');            
        }
    }

    private function processSpreadsheet(string $spreadsheetPath, string $spreadsheet): void
    {
        $spreadsheetConversion = SpreadsheetConversion::where('guid', $this->guid)
            ->where('original_name', $spreadsheet)
            ->first();

        $sourceFile = $spreadsheetPath . '/' . $spreadsheet;
        $destinationPath = $spreadsheetPath;
        $destinationFilename = $spreadsheetConversion->converted_name;

        $extension = $spreadsheetConversion->convertedFormat->extension;
        $spreadsheet = IOFactory::load($sourceFile);

        $writer = IOFactory::createWriter($spreadsheet, ucfirst($extension));
        $writer->save("$destinationPath/$destinationFilename");

        unlink($sourceFile);
    }
}
