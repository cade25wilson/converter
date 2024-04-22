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

class ConvertMultipleVideo implements ShouldQueue
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
            $videoPath = storage_path('app/video/' . $this->guid);
            $videos = array_diff(scandir($videoPath), ['.', '..']);

            if (empty($videos)) {
                return;
            }

            VideoConversion::where('guid', $this->guid)->update(['status' => 'processing']);
            ImageConverted::dispatch($this->guid, 'processing');

            foreach ($videos as $video) {
                $this->processVideo($videoPath, $video);
            }

            ConversionService::ZipFiles($this->guid, 'video');
            ConversionService::DeleteDirectory($videoPath);
            VideoConversion::where('guid', $this->guid)->update(['status' => 'completed']);
            ImageConverted::dispatch($this->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            VideoConversion::where('guid', $this->guid)->update(['status' => 'failed']);
            ImageConverted::dispatch($this->guid, 'failed');
        }
    }

    //this may need to be removed for to consolidate with processAudio
    private function processVideo(string $guid, string $video)
    {
        $sourceFile = $guid . '/' . $video;
        $destinationFile = $guid . '/' . pathinfo($video, PATHINFO_FILENAME) . '.' . $this->format;

        $command = "ffmpeg -i $sourceFile $destinationFile";
        $output = array();
        $return_var = null;

        exec($command . " 2>&1", $output, $return_var);
        unlink($sourceFile);
    }
}
