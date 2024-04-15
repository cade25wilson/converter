<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\Imageconversion;
use App\Services\ImageConverterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Imagick as Imagick;

class ConvertMultipleImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $guid;
    protected string $format;
    protected $width;   
    protected $height;
    protected $watermark;
    /**
     * Create a new job instance.
     */
    public function __construct(string $guid, string $format, $width = null, $height = null, $watermark = null)
    {
        $this->guid = $guid;
        $this->format = $format;
        $this->width = $width;
        $this->height = $height;
        $this->watermark = $watermark;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $imagePath = storage_path('app/images/' . $this->guid);
            $images = array_diff(scandir($imagePath), ['.', '..']);

            if (empty($images)) {
                return;
            }

            ImageConverterService::updateStatus('processing', $this->guid,);
            ImageConverted::dispatch($this->guid, 'processing');
            Imageconversion::where('guid', $this->guid)->update(['status' => 'processing']);

            foreach ($images as $image) {
                $this->processImage($imagePath, $image, $this->watermark);
            }
            if($this->watermark) {
                unlink($imagePath . '/' . $this->watermark);
            }

            ImageConverterService::ZipImages($this->guid);
            ImageConverterService::deleteDirectory($imagePath);
            ImageConverterService::updateStatus('completed', $this->guid);
            ImageConverted::dispatch($this->guid, 'completed');
        } catch (\Exception $e) {
            Log::error('Multiple Image conversion failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processImage($imagePath, $image, $watermark): void
    {
        // Skip if the image is the watermark
        if ($image === $watermark) {
            return;
        }

        $imagick = new Imagick($imagePath . '/' . $image);

        if ($this->width && $this->height) {
            $imagick->resizeImage($this->width, $this->height, Imagick::FILTER_LANCZOS, 1);
        }
        
        if($watermark) {
            $watermark = new Imagick($imagePath . '/' . $watermark);
            $imagick->compositeImage($watermark, Imagick::COMPOSITE_OVER, 0, 0);
        }

        $imagick->writeImage($imagePath . '/' . pathinfo($image, PATHINFO_FILENAME) . '.' . $this->format);
        unlink($imagePath . '/' . $image);
    }
}
