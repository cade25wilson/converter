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
            $images = array_diff($images, ['.', '..']); // Remove '.' and '..'

            if (empty($images)) {
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

            // $zip = new \ZipArchive();

            // $zipFileName = storage_path('app/images/' . $this->guid . '.zip');

            // if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            //     $options = array('add_path' => 'images/', 'remove_all_path' => TRUE);
            //     $zip->addGlob(storage_path('app/images/' . $this->guid . '/*'), GLOB_BRACE, $options);
            //     $zip->close();
            // }
            ImageConverterService::ZipImages($this->guid);
            ImageConverterService::deleteDirectory(storage_path('app/images/' . $this->guid));
            // $this->deleteDirectory(storage_path('app/images/' . $this->guid));

            Imageconversion::where('guid', $this->guid)->update(['status' => 'completed']);
        } catch (\Exception $e) {
            Log::error('Multiple Image conversion failed: ' . $e->getMessage());
        }
    }

    // private function deleteDirectory($dir): void
    // {
    //     if (!file_exists($dir)) {
    //         return;
    //     }
    //     $files = array_diff(scandir($dir), ['.', '..']);
    //     foreach ($files as $file) {
    //         (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
    //     }
    //     rmdir($dir);
    // }
}
