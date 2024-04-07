<?php

namespace App\Jobs;

use App\Models\Imageconversion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imagick as Imagick;

class ConvertMultipleImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $guid;
    protected string $format;
    /**
     * Create a new job instance.
     */
    public function __construct(string $guid, string $format)
    {
        $this->guid = $guid;
        $this->format = $format;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $images = scandir(storage_path('app/images/' . $this->guid));
            if ($images === false) {
                return;
            } else {
                Imageconversion::where('guid', $this->guid)->update(['status' => 'processing']);
            }
            foreach ($images as $image) {
                if ($image === '.' || $image === '..') {
                    continue;
                }
                $imagick = new Imagick(storage_path('app/images/' . $this->guid . '/' . $image));
                $imagick->writeImage(storage_path('app/images/' . $this->guid . '/' . pathinfo($image, PATHINFO_FILENAME) . '.' . $this->format));
                //if successful delete the original image
                unlink(storage_path('app/images/' . $this->guid . '/' . $image));
            }
            Imageconversion::where('guid', $this->guid)->update(['status' => 'completed']);
        } catch (\Exception $e) {
            \Log::error('Multiple Image conversion failed: ' . $e->getMessage());
        }
    }
}
