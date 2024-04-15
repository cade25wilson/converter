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

class ConvertSingleImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imageConversion;
    /**
     * Create a new job instance.
     */
    // public function __construct(Imageconversion $imageConversion, $image, int $width, int $height)
    public function __construct(Imageconversion $imageConversion)       
    {
        $this->imageConversion = $imageConversion;
    }

    /**
     * Execute the job.
     * return a file
    */

    public function handle(): void
    {
        try {
            ImageConverterService::updateStatus('processing', $this->imageConversion->guid);
            $image = new Imagick(storage_path('app/images/' . $this->imageConversion->guid . '/' . $this->imageConversion->original_name));

            // Resize the image if width and height are set
            if ($this->imageConversion->width && $this->imageConversion->height) {
                $image->resizeImage($this->imageConversion->width, $this->imageConversion->height, Imagick::FILTER_LANCZOS, 1);
            }

            if($this->imageConversion->watermark) {
                $watermark = new Imagick(storage_path('app/images/' . $this->imageConversion->guid . '/' . $this->imageConversion->watermark));
                $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, 0, 0);
            }

            $image->writeImage(storage_path('app/images/' . $this->imageConversion->guid . '/' . $this->imageConversion->converted_name));
            unlink(storage_path('app/images/' . $this->imageConversion->guid . '/' . $this->imageConversion->original_name));

            ImageConverterService::ZipImages($this->imageConversion->guid);
            ImageConverterService::deleteDirectory(storage_path('app/images/' . $this->imageConversion->guid));

            ImageConverterService::updateStatus('completed', $this->imageConversion->guid);
            event(new ImageConverted($this->imageConversion->guid, 'completed'));
            ImageConverted::dispatch($this->imageConversion->guid, 'completed');
        
        } catch (\Exception $e) {
            Log::error('Image conversion failed: ' . $e->getMessage());
            throw $e;
        }
    }
}