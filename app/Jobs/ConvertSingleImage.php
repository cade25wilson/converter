<?php

namespace App\Jobs;

use App\Models\Imageconversion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imagick as Imagick;

class ConvertSingleImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imageConversion;
    protected $image;
    protected $width;
    protected $height;
    /**
     * Create a new job instance.
     */
    // public function __construct(Imageconversion $imageConversion, $image, int $width, int $height)
    public function __construct(Imageconversion $imageConversion)       
    {
        $this->imageConversion = $imageConversion;
        // $this->image = $image;
        // $this->width = $width;
        // $this->height = $height;
    }
    /**
     * Execute the job.
     * return a file
    */
    public function handle(): void
    {
        try {
            $this->imageConversion->update(['status' => 'processing']);
            //get the file at storage/app/images/$this->imageConversion->original_name
            $imagick = new Imagick(storage_path('app/images/' . $this->imageConversion->original_name));

            if ($this->imageConversion->width && $this->imageConversion->height) {
                \Log::error('Resizing image to ' . $this->width . 'x' . $this->height);
                $imagick->resizeImage($this->imageConversion->width, $this->imageConversion->height, Imagick::FILTER_LANCZOS, 1);
            }

            $imagick->writeImage(storage_path('app/images/' . $this->imageConversion->converted_name));
            
            $this->imageConversion->update(['status' => 'completed']);
        } catch (\Exception $e) {
            \Log::error('Image conversion failed: ' . $e->getMessage());
        }
    }
}
