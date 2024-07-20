<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\Imageconversion;
use App\Services\ImageConverterService;
use App\Services\ConversionService;
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
    protected bool $stripMetaData;

         /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(string $guid, string $format, $width = null, $height = null, $watermark = null, bool $stripMetaData = false)
    {
        $this->guid = $guid;
        $this->format = $format;
        $this->width = $width;
        $this->height = $height;
        $this->watermark = $watermark;
        $this->stripMetaData = $stripMetaData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $imagePath = storage_path('app/image/' . $this->guid);
            $images = array_diff(scandir($imagePath), ['.', '..']);

            if (empty($images)) {
                return;
            }

            Imageconversion::where('guid', $this->guid)->update(['status' => 'processing']);
            ImageConverted::dispatch($this->guid, 'processing');

            foreach ($images as $image) {
                $this->processImage($imagePath, $image, $this->watermark, $this->stripMetaData);
            }

            if($this->watermark) {
                unlink($imagePath . '/' . $this->watermark);
            }

            ConversionService::ZipFiles($this->guid, 'image');
            ConversionService::DeleteDirectory($imagePath);
            Imageconversion::where('guid', $this->guid)->update(['status' => 'completed']);
            ImageConverted::dispatch($this->guid, 'completed');
        } catch (\Exception $e) {
            ImageConverted::dispatch($this->guid, 'failed');
            Log::error('Multiple Image conversion failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processImage($imagePath, $image, $watermark, $stripMetaData): void
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

        if($stripMetaData) {
            $imagick->stripImage();
        }

        $imagick->writeImage($imagePath . '/' . pathinfo($image, PATHINFO_FILENAME) . '.' . $this->format);
        unlink($imagePath . '/' . $image);
    }
}
