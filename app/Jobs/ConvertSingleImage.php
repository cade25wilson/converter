<?php

namespace App\Jobs;

use App\Models\Imageconversion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imagick as Imagick;

class ConvertSingleImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $imageConversion;
    protected $image;
    /**
     * Create a new job instance.
     */
    public function __construct(Imageconversion $imageConversion, $image)
    {
        $this->imageConversion = $imageConversion;
        $this->image = $image;
    }

    /**
     * Execute the job.
     * return a file
    */
    public function handle(): void
    {
        try {
            $this->imageConversion->update(['status' => 'processing']);
    
            $imagick = new Imagick(storage_path('app/' . $this->image));
            $imagick->writeImage(storage_path('app/' . $this->imageConversion->converted_path));
            
            $this->imageConversion->update(['status' => 'completed']);
        } catch (\Exception $e) {
            \Log::error('Image conversion failed: ' . $e->getMessage());
        }
    }
}
