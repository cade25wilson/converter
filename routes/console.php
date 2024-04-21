<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('clearoldfiles', function() {
    $files = glob(storage_path('app/audio/*'));
    $now = time();
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 60 * 60 * 24) {
                unlink($file);
            }
        }
    }

    $files = glob(storage_path('app/images/*'));
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 60 * 60 * 24) {
                unlink($file);
            }
        }
    }
})->purpose('Clear files older than 24 hours')->everyThirtyMinutes();