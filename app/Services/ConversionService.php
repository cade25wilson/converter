<?php

namespace App\Services;

use App\Rules\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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

    public static function AddFilesToFolder(array $files, string $type): string
    {
        $guid = Str::uuid();
        self::MakeFolder($guid, $type);
        foreach ($files as $file) {
            Log::info('Copying file ' . $file);
            copy($file, storage_path('app/' . $type . '/' . $guid . '/' . basename($file)));
        }
        return $guid;
    }

    private static function MakeFolder(string $guid, string $type): void
    {
        mkdir(storage_path('app/' . $type . '/' . $guid));
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
        if ($this->isUrl($file)){
            $this->downloadFile($file);
        }
        $file->storeAs($type . '/' . $guid . '/', $file->getClientOriginalName());
    }

    private function isUrl($file): bool
    {
        return filter_var($file, FILTER_VALIDATE_URL) !== false;   
    }

    private function downloadFile($file): void
    {
        // $file = file_get_contents($file);
        // $file->storeAs($type . '/' . $guid . '/', $file->getClientOriginalName());
        Log::info('Downloaded file from URL' . $file);
    }

    public function getValidationRules(string $type): array
    {
        $rules = [
            'archive' => [
                $type.'.*' => ['required', new Folder],
                'format' => 'required|exists:archive_formats,id',
            ],
            'audio' => [
                $type.'.*' => 'required|file',
                'format' => 'required|exists:audio_formats,id',
            ],
            'ebook' => [
                $type.'.*' => 'required|file',
                'format' => 'required|exists:ebook_formats,id',
            ],
            'image' => [
                $type.'.*' => 'required',
                'format' => 'required|exists:image_formats,id',
                'email' => 'email',
                'width' => 'numeric|integer|required_with:height',
                'height' => 'numeric|integer|required_with:width',
                'watermark' => 'image',
                'strip_metadata' => 'boolean',
                'quality' => 'numeric|min:0|max:100'
            ],
            'spreadsheet' => [
                $type.'.*' => 'required|file',
                'format' => 'required|exists:spreadsheet_formats,id',
            ],
            'video' => [
                $type.'.*' => 'required|file',
                'format' => 'required|exists:video_formats,id',
            ]
        ];

        return $rules[$type];
    }

    public function getService(Request $request, string $type): mixed
    {
        $services = [
            'archive' => ArchiveConverterService::class,
            'audio' => AudioConverterService::class,
            'image' => ImageConverterService::class,
            'ebook' => EbookConverterService::class,
            'spreadsheet' => SpreadsheetConverterService::class,
            'video' => VideoConverterService::class
        ];

        return new $services[$type]($request);
    }

    public function validate(Request $request, mixed $validationRules):bool
    {
        try {
            $request->validate($validationRules);
        } catch(ValidationException) {
            return false;
        }
        return true;
    }
}