<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\Audioconversion;
use App\Services\AudioConverterService;
use App\Services\ConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertMultipleAudio implements ShouldQueue
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
        try{
            $audioPath = storage_path('app/audio/' . $this->guid);
            $audios = array_diff(scandir($audioPath), ['.', '..']);

            if (empty($audios)) {
                return;
            }

            Audioconversion::where('guid', $this->guid)->update(['status' => 'processing']);
            ImageConverted::dispatch($this->guid, 'processing');

            foreach ($audios as $audio) {
                $this->processAudio($audioPath, $audio);
            }

            ConversionService::ZipFiles($this->guid, 'audio');
            ConversionService::DeleteDirectory($audioPath);
            AudioConversion::where('guid', $this->guid)->update(['status' => 'completed']);
            ImageConverted::dispatch($this->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            AudioConversion::where('guid', $this->guid)->update(['status' => 'failed']);
            ImageConverted::dispatch($this->guid, 'failed');
        }
    }

    private function processAudio(string $guid, string $audio)
    {
        $sourceFile =  $guid . '/' . $audio;
        if ($audio === pathinfo($audio, PATHINFO_FILENAME) . '.' . $this->format) {
            return;
        }
        $destinationFile = $guid . '/' . pathinfo($audio, PATHINFO_FILENAME) . '.' . $this->format;

        $command = "ffmpeg -i $sourceFile $destinationFile";
        $output = array();
        $return_var = null;

        exec($command . " 2>&1", $output, $return_var);
        unlink($sourceFile);
    }
}
