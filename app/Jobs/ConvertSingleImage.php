<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\Imageconversion;
use App\Services\ConversionService;
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
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes
    /**
     * Create a new job instance.
     */
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
            // Imageconversion::where('guid', $this->imageConversion->guid)->update(['status' => 'processing']);
            $this->imageConversion->update(['status' => 'processing']);
            ImageConverted::dispatch($this->imageConversion->guid, 'processing');
            $image = new Imagick(storage_path('app/image/' . $this->imageConversion->guid . '/' . $this->imageConversion->original_name));

            if($this->imageConversion->width && $this->imageConversion->height) {
                $image->resizeImage($this->imageConversion->width, $this->imageConversion->height, Imagick::FILTER_LANCZOS, 1);
            }

            if($this->imageConversion->watermark) {
                $watermark = new Imagick(storage_path('app/image/' . $this->imageConversion->guid . '/' . $this->imageConversion->watermark));
                $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, 0, 0);
            }

            if($this->imageConversion->strip_metadata) {
                $image->stripImage();
            }
            $image->setImageCompressionQuality($this->imageConversion->quality);
            $image->writeImage(storage_path('app/image/' . $this->imageConversion->guid . '/' . $this->imageConversion->converted_name));
            unlink(storage_path('app/image/' . $this->imageConversion->guid . '/' . $this->imageConversion->original_name));

            ConversionService::ZipFiles($this->imageConversion->guid, 'image');
            ConversionService::DeleteDirectory(storage_path('app/image/' . $this->imageConversion->guid));
            $this->imageConversion->update(['status' => 'completed']);
            ImageConverted::dispatch($this->imageConversion->guid, 'completed');        
        } catch (\Exception $e) {
            ImageConverted::dispatch($this->imageConversion->guid, 'failed');
            $this->imageConversion->update(['status' => 'failed']);
            Log::error('Image conversion failed: ' . $e->getMessage());
        }
    }
}