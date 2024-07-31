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
use Throwable;

class ConvertMultipleAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FfmpegService;

    protected string $guid;
    protected string $format;
    protected ?int $audioVolume;
    protected ?bool $fadeIn;
    protected ?bool $fadeOut;
    protected ?bool $reverseAudio;

             /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 5 minutes

    
    /**
     * Create a new job instance.
     */
    public function __construct(string $guid, string $format, ?int $audioVolume, ?bool $fadeIn, ?bool $fadeOut, ?bool $reverseAudio)
    {
        $this->guid = $guid;
        $this->format = $format;
        $this->audioVolume = $audioVolume;
        $this->fadeIn = $fadeIn;
        $this->fadeOut = $fadeOut;
        $this->reverseAudio = $reverseAudio;
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
                $this->processAudio($this->guid, $audio);
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

    private function processAudio(string $guid, string $audio): void
    {
        $sourceFile = storage_path('app/audio/' . $guid . '/' . $audio);
        $filenameWithoutExtension = pathinfo($audio, PATHINFO_FILENAME);
        $fileExtension = pathinfo($audio, PATHINFO_EXTENSION);
        $desiredFormat = $this->format;
        if ($fileExtension === $desiredFormat) {
            return;
        }
        log::info("guid " . $guid. " audio " . $audio . " volume " . $this->audioVolume . " fade in " . $this->fadeIn . " Fade out " . $this->fadeOut);
        $command = $this->buildFfmpegCommand(
            $guid,
            $audio,
            $filenameWithoutExtension . '.' . $desiredFormat,
            null,
            null,
            null,
            null,
            null,
            $this->audioVolume,
            $this->fadeIn,
            $this->fadeOut,
            $this->reverseAudio,
            'audio'
        );
        $output = [];
        $return_var = null;
        exec($command . " 2>&1", $output, $return_var);
        log::info($command);
        unlink($sourceFile);
    }

    public function failed(?Throwable $e): void
    {
        Log::error($e->getMessage());
        Audioconversion::where('guid', $this->guid)->update(['status' => 'failed']);
        ImageConverted::dispatch($this->guid, 'failed');
    }
}
