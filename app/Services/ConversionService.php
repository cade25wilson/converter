<?php

namespace App\Services;

class ConversionService 
{
    public static function ZipFiles($guid, $type): void
    {
        $zip = new \ZipArchive();
        $zipFileName = storage_path('app/' . $type . '/' . $guid . '.zip');

        if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            $options = array('add_path' => $type . '/', 'remove_all_path' => TRUE);
            $zip->addGlob(storage_path('app/' . $type . '/' . $guid . '/*'), GLOB_BRACE, $options);
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