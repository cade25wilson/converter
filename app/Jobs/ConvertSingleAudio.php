<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\Audioconversion;
use App\Services\ConversionService;
use App\Services\FfmpegService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertSingleAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FfmpegService;

    protected $audioConversion;
    /**
     * Create a new job instance.
     */
    public function __construct(Audioconversion $audioConversion)
    {
        $this->audioConversion = $audioConversion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->audioConversion->update(['status' => 'processing']);
            ImageConverted::dispatch($this->audioConversion->guid, 'processing');
            // add this to service as well(redundant code)
            // $sourceFile = '/var/www/converter/storage/app/audio/' . $this->audioConversion->guid . '/' . $this->audioConversion->original_name;
            // $destinationFile = '/var/www/converter/storage/app/audio/' . $this->audioConversion->guid . '/' . $this->audioConversion->converted_name;

            // $sourceFileEscaped = escapeshellarg($sourceFile);
            // $destinationFileEscaped = escapeshellarg($destinationFile);
            // $command = "ffmpeg -i $sourceFileEscaped $destinationFileEscaped";
            $command = $this->buildFFmpegCommand($this->audioConversion->guid, $this->audioConversion->original_name, $this->audioConversion->converted_name, null, null, null, null, null, $this->audioConversion->audio, $this->audioConversion->fade_in, $this->audioConversion->fade_out, null, 'audio');
            $output = [];
            $return_var = null;

            exec($command . " 2>&1", $output, $return_var);
            unlink(storage_path('app/audio/' . $this->audioConversion->guid . '/' . $this->audioConversion->original_name));
            ConversionService::ZipFiles($this->audioConversion->guid, 'audio');
            ConversionService::DeleteDirectory(storage_path('app/audio/' . $this->audioConversion->guid));
            $this->audioConversion->update(['status' => 'completed']);
            ImageConverted::dispatch($this->audioConversion->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->audioConversion->update(['status' => 'failed']);
        }
    }
}
