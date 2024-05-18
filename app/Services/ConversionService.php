<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public function SetVariables(Request $request, string $type): array
    {
        $guid = Str::uuid();
        $file = $request->file($type)[0];
        $originalName = $file->getClientOriginalName();
        $this->StoreFile($file, $guid, $type);
        return [
            'guid' => $guid,
            'originalName' => $originalName,
            'originalFormat' => $file->getClientOriginalExtension(),
            'format' => $request->input('format'),
            'file_size' => $file->getSize()
        ];
    }

    private function StoreFile($file, $guid, $type): void
    {
        $file->storeAs($type . '/' . $guid . '/', $file->getClientOriginalName());
    }
}