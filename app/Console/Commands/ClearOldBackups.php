<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearOldBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-old-backups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear backups older than 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = time();
        $files = glob(storage_path('app/backups/*'));
        foreach($files as $file){
            if(is_file($file)){
                if($now - filemtime($file) >= 60 * 60 * 24 * 7){
                    unlink($file);
                }
            }
        }
    }
}
