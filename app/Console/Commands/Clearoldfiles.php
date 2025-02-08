<?php

namespace App\Console\Commands;

use App\Models\ConversionTypes;
use Illuminate\Console\Command;

class Clearoldfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clearoldfiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear files older than 72 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directories = ConversionTypes::all()->pluck('name')->toArray();
        $now = time();

        foreach($directories as $directory){
            $files = glob(storage_path('app/' . $directory . '/*'));
            foreach ($files as $file) {
                if (is_file($file)) {
                    if ($now - filemtime($file) >= 60 * 60 * 24 * 3) {
                        unlink($file);
                    }
                }
            }
        }
    }
}
