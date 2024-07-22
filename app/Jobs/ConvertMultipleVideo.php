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
use Throwable;

class ConvertMultipleVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $guid;
    protected string $format;
    protected ?int $width;
    protected ?int $height;

    /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3000; // 5 minutes
    /**
     * Create a new job instance.
     */
    public function __construct(string $guid, string $format, ?int $width, ?int $height)
    {
        $this->guid = $guid;
        $this->format = $format;
        $this->width = $width;
        $this->height = $height;
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
    private function processVideo(string $guid, string $video): void
    {
        $sourceFile = $guid . '/' . $video;
        $destinationFile = $guid . '/' . pathinfo($video, PATHINFO_FILENAME) . '.' . $this->format;

        // Use escapeshellarg to escape the file paths
        $escapedSourceFile = escapeshellarg($sourceFile);
        $escapedDestinationFile = escapeshellarg($destinationFile);

        // Build the FFmpeg command with optional width and height
        $command = "ffmpeg -i $escapedSourceFile";

        if ($this->width && $this->height) {
            $command .= " -vf scale={$this->width}:{$this->height}";
        }

        $command .= " $escapedDestinationFile";

        $output = array();
        $return_var = null;

        exec($command . " 2>&1", $output, $return_var);
        unlink($sourceFile);
    }

    public function failed(?Throwable $e): void
    {
        Log::error($e->getMessage());
        VideoConversion::where('guid', $this->guid)->update(['status' => 'failed']);
        ImageConverted::dispatch($this->guid, 'failed');
    }
}
