<?php

namespace App\Jobs;

use App\Events\ImageConverted;
use App\Models\ArchiveConversion;
use App\Services\ConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertMultipleArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $guid;
    protected string $format;

                 /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 800; // 5 minutes
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
            $archivePath = storage_path('app/archive/' . $this->guid);
            $archives = array_diff(scandir($archivePath), ['.', '..']);

            if (empty($archives)) {
                return;
            }

            ArchiveConversion::where('guid', $this->guid)->update(['status' => 'processing']);
            ImageConverted::dispatch($this->guid, 'processing');

            foreach($archives as $archive) {
                $this->processArchive($archivePath, $archive);
            }

            ConversionService::ZipFiles($this->guid, 'archive');
            ConversionService::DeleteDirectory($archivePath);
            ArchiveConversion::where('guid', $this->guid)->update(['status' => 'completed']);
            ImageConverted::dispatch($this->guid, 'completed');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            ArchiveConversion::where('guid', $this->guid)->update(['status' => 'failed']);
            ImageConverted::dispatch($this->guid, 'failed');
            throw $e;
        }
    }

    private function processArchive(string $archivePath, string $archive): void
    {
        $sourceFile = $archivePath . '/' . $archive;
        $destinationFile = $archivePath . '/' . pathinfo($archive, PATHINFO_FILENAME) . '.' . $this->format;

        $sourceFileEscaped = escapeshellarg($sourceFile);
        $destinationFileEscaped = escapeshellarg($destinationFile);

        shell_exec("7z x $sourceFileEscaped -so | 7z a -si $destinationFileEscaped");
        unlink(storage_path('app/archive/' . $this->guid . '/' . $archive));
    }
}
