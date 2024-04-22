<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\VideoConversion;
use App\Services\ConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertSingleVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected VideoConversion $videoConversion;

    /**
     * Create a new job instance.
     */
    public function __construct(VideoConversion $videoConversion)
    {
        $this->videoConversion = $videoConversion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->videoConversion->update(['status' => 'processing']);
            ImageConverted::dispatch($this->videoConversion->guid, 'processing');
            // add this to service as well(redundant code)
            $sourceFile = '/var/www/converter/storage/app/video/' . $this->videoConversion->guid . '/' . $this->videoConversion->original_name;
            $destinationFile = '/var/www/converter/storage/app/video/' . $this->videoConversion->guid . '/' . $this->videoConversion->converted_name;
            
            $command = "ffmpeg -i $sourceFile $destinationFile";
            $output = array();
            $return_var = null;

            exec($command . " 2>&1", $output, $return_var);
            Log::info('command' . $command);
            unlink(storage_path('app/video/' . $this->videoConversion->guid . '/' . $this->videoConversion->original_name));
            ConversionService::ZipFiles($this->videoConversion->guid, 'video');
            ConversionService::DeleteDirectory(storage_path('app/video/' . $this->videoConversion->guid));
            $this->videoConversion->update(['status' => 'completed']);
            ImageConverted::dispatch($this->videoConversion->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->videoConversion->update(['status' => 'failed']);
        }
    }
}
