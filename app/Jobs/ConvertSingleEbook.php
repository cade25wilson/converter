<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\EbookConversion;
use App\Services\ConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertSingleEbook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected EbookConversion $ebookConversion;
    /**
     * Create a new job instance.
     */
    public function __construct(EbookConversion $ebookConversion)
    {
        $this->ebookConversion = $ebookConversion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->ebookConversion->update(['status' => 'processing']);
            ImageConverted::dispatch($this->ebookConversion->guid, 'processing');
            $sourceFile = '/var/www/converter/storage/app/ebook/' . $this->ebookConversion->guid . '/' . $this->ebookConversion->original_name;
            $destinationFile = '/var/www/converter/storage/app/ebook/' . $this->ebookConversion->guid . '/' . $this->ebookConversion->converted_name;
            $sourceFileEscaped = escapeshellarg($sourceFile);
            $destinationFileEscaped = escapeshellarg($destinationFile);
            $command = "ebook-convert $sourceFileEscaped $destinationFileEscaped";
            $output = array();
            $return_var = null;

            exec($command . " 2>&1", $output, $return_var);
            unlink(storage_path('app/ebook/' . $this->ebookConversion->guid . '/' . $this->ebookConversion->original_name));
            ConversionService::ZipFiles($this->ebookConversion->guid, 'ebook');
            ConversionService::DeleteDirectory(storage_path('app/ebook/' . $this->ebookConversion->guid));
            $this->ebookConversion->update(['status' => 'completed']);
            ImageConverted::dispatch($this->ebookConversion->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->ebookConversion->update(['status' => 'failed']);
        }
    }
}
