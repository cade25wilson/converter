<?php

namespace App\Jobs;

use App\Models\Imageconversion;
use App\Services\ImageConverterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Imagick as Imagick;

class  ConvertMultipleImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $guid;
    protected string $format;
    protected int $width;   
    protected int $height;
    /**
     * Create a new job instance.
     */
    public function __construct(string $guid, string $format, int $width, int $height)
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
            $imagePath = storage_path('app/images/' . $this->guid);
            $images = array_diff(scandir($imagePath), ['.', '..']);

            if (empty($images)) {
                return;
            }

            Imageconversion::where('guid', $this->guid)->update(['status' => 'processing']);

            foreach ($images as $image) {
                $this->processImage($imagePath, $image);
            }

            ImageConverterService::ZipImages($this->guid);
            ImageConverterService::deleteDirectory($imagePath);

            Imageconversion::where('guid', $this->guid)->update(['status' => 'completed']);
        } catch (\Exception $e) {
            Log::error('Multiple Image conversion failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processImage($imagePath, $image): void
    {
        $imagick = new Imagick($imagePath . '/' . $image);

        if ($this->width && $this->height) {
            $imagick->resizeImage($this->width, $this->height, Imagick::FILTER_LANCZOS, 1);
        }

        $imagick->writeImage($imagePath . '/' . pathinfo($image, PATHINFO_FILENAME) . '.' . $this->format);
        unlink($imagePath . '/' . $image);
    }
}
