<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\ArchiveConversion;
use App\Services\ConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertSingleArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ArchiveConversion $archiveConversion;
    /**
     * Create a new job instance.
     */
    public function __construct(ArchiveConversion $archiveConversion)
    {
        $this->archiveConversion = $archiveConversion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $this->archiveConversion->update(['status' => 'processing']);
            sleep(1);
            ImageConverted::dispatch($this->archiveConversion->guid, 'processing');
            $sourceFile = '/var/www/converter/storage/app/archive/' . $this->archiveConversion->guid . '/' . $this->archiveConversion->original_name;
            $destinationFile = '/var/www/converter/storage/app/archive/' . $this->archiveConversion->guid . '/' . $this->archiveConversion->converted_name;

            $sourceFileEscaped = escapeshellarg($sourceFile);
            $destinationFileEscaped = escapeshellarg($destinationFile);

            shell_exec("7z x $sourceFileEscaped -so | 7z a -si $destinationFileEscaped");
            unlink(storage_path('app/archive/' . $this->archiveConversion->guid . '/' . $this->archiveConversion->original_name));
            ConversionService::ZipFiles($this->archiveConversion->guid, 'archive');
            ConversionService::DeleteDirectory(storage_path('app/archive/' . $this->archiveConversion->guid));
            $this->archiveConversion->update(['status' => 'completed']);
            ImageConverted::dispatch($this->archiveConversion->guid, 'completed');
        } catch (\Exception $e) {
            $this->archiveConversion->update(['status' => 'failed']);
            ImageConverted::dispatch($this->archiveConversion->guid, 'failed');
            Log::error("Archive conversion failed: " . $e->getMessage());
            throw $e;
        }
    }
}
