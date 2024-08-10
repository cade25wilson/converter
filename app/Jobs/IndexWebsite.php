<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class IndexWebsite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'api.indexnow.org/IndexNow',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
        "host": "fileconverter.services",
        "key": "b21dd2a8154c4384b481679b726f6a99",
        "keyLocation": "https://fileconverter.services/b21dd2a8154c4384b481679b726f6a99.txt",
        "urlList": [
            "https://fileconverter.services/",
            "https://fileconverter.services/archives",
            "https://fileconverter.services/audios",
            "https://fileconverter.services/contact",
            "https://fileconverter.services/images",
            "https://fileconverter.services/previousconversions",
            "https://fileconverter.services/signin",
            "https://fileconverter.services/signup",
            "https://fileconverter.services/spreadsheets",
            "https://fileconverter.services/videos"
            ]
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        Log::info("HTTP Response Code: " . $http_code);
    }

    public function failed(?Throwable $e): void
    {
        Log::error($e->getMessage());
    }
}
