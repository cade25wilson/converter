<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\VideoConversion;
use App\Services\ConversionService;
use App\Services\VideoConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConvertSingleVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, VideoConversionService;

    protected VideoConversion $videoConversion;

    /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3000; // 5 minutes

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

            $command = $this->buildFFmpegCommand($this->videoConversion->guid, $this->videoConversion->original_name, $this->videoConversion->converted_name, $this->videoConversion->width, $this->videoConversion->height, $this->videoConversion->rotation_angle, $this->videoConversion->flip, $this->videoConversion->frame_rate, $this->videoConversion->audio);
            $output = [];
            $return_var = null;

            exec($command . " 2>&1", $output, $return_var);

            // Perform post-processing actions
            unlink(storage_path('app/video/' . $this->videoConversion->guid . '/' . $this->videoConversion->original_name));
            ConversionService::ZipFiles($this->videoConversion->guid, 'video');
            ConversionService::DeleteDirectory(storage_path('app/video/' . $this->videoConversion->guid));
            $this->videoConversion->update(['status' => 'completed']);
            ImageConverted::dispatch($this->videoConversion->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            ImageConverted::dispatch($this->videoConversion->guid, 'failed');
            $this->videoConversion->update(['status' => 'failed']);
        }
    }

    public function failed(?Throwable $e): void
    {
        Log::error($e->getMessage());
        ImageConverted::dispatch($this->videoConversion->guid, 'failed');
        $this->videoConversion->update(['status' => 'failed']);
    }

    private function getFFmpegTransposeValue(int $rotationAngle): int
    {
        switch ($rotationAngle) {
            case 90:
                return 1;
            case 180:
                return 2;
            case 270:
                return 3;
            default:
                return 0;
        }
    }

    private function getFlip(): string
    {
        switch ($this->videoConversion->flip) {
            case "h":
                return "hflip";
            case "v":
                return "vflip";
            case "b":
                return "vflip,hflip";
            default:
                return "";
        }
    }
}
