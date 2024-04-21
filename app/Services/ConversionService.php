<?php

namespace App\Services;

class ConversionService 
{

    public static function ZipImages($guid): void
    {
        $zip = new \ZipArchive();
        $zipFileName = storage_path('app/images/' . $guid . '.zip');

        if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            $options = array('add_path' => 'images/', 'remove_all_path' => TRUE);
            $zip->addGlob(storage_path('app/images/' . $guid . '/*'), GLOB_BRACE, $options);
            $zip->close();
        }
    }

    public static function ZipFiles($guid): void
    {
        $zip = new \ZipArchive();
        $zipFileName = storage_path('app/audio/' . $guid . '.zip');

        if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            $options = array('add_path' => 'audio/', 'remove_all_path' => TRUE);
            $zip->addGlob(storage_path('app/audio/' . $guid . '/*'), GLOB_BRACE, $options);
            $zip->close();
        }
    }
    
    public static function DeleteDirectory($dir): void
    {
        if (!file_exists($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }

}