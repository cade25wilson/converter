<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\EbookConversion;
use App\Services\ConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertMultipleEbook implements ShouldQueue
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
            $ebookPath = storage_path('app/ebook/' . $this->guid);
            $ebooks = array_diff(scandir($ebookPath), ['.', '..']);

            if (empty($ebooks)) {
                return;
            }

            EbookConversion::where('guid', $this->guid)->update(['status' => 'processing']);
            ImageConverted::dispatch($this->guid, 'processing');

            foreach($ebooks as $ebook) {
                $this->processEbook($ebookPath, $ebook);
            }

            ConversionService::ZipFiles($this->guid, 'ebook');
            ConversionService::DeleteDirectory($ebookPath);
            EbookConversion::where('guid', $this->guid)->update(['status' => 'completed']);
            ImageConverted::dispatch($this->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            EbookConversion::where('guid', $this->guid)->update(['status' => 'failed']);
            ImageConverted::dispatch($this->guid, 'failed');
        }
    }

    private function processEbook(string $ebookPath, string $ebook): void
    {
        $sourcefile = $ebookPath . '/' . $ebook;
        $destinationFile = $ebookPath . '/' . pathinfo($ebook, PATHINFO_FILENAME) . '.' . $this->format;

        $command = "ebook-convert $sourcefile $destinationFile";
        $output = array();
        $return_var = null;

        exec($command, $output, $return_var);
        unlink($sourcefile);
    }
}
