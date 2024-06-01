<?php

namespace App\Http\Controllers;

use App\Services\ConversionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConversionController extends Controller
{
    private $conversionService;

    public function __construct(ConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    public function convert(Request $request, string $type): JsonResponse
    {
        $validationRules = $this->conversionService->getValidationRules($type);
        
        if($this->conversionService->validate($request, $validationRules) === false) {
            return response()->json(['errors' => 'The given data was invalid.'], 422);
        }

        $service = $this->conversionService->getService($request, $type);
        if(count($request->$type) > 1) {
            $guid = $service->MultipleConvert();
        } else {
            $guid = $service->SingleConvert();
        }

        return response()->json(['message' => 'Conversion Started', 'guid' => $guid]);
    }

    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'guid' => 'required|string',
            'type' => 'required|in:archive,audio,image,spreadsheet,video'
        ]);
        
        $guid = $request->guid;
        $type = $request->type;

        $file = storage_path('app/' . $type . '/' . $guid . '.zip');

        if (!file_exists($file)) {
            return http_response_code(200);
        }

        // Delete the file
        unlink($file);

        return response()->json(['success' => 'File deleted'], 200);
    }

    public function urlconvert(Request $request, string $type)
    {        
        // $lastSegment = last($request->segments());
        $data = json_decode($request->getContent(), true);
        $format = (int)$data['format'];
        $files = $data[$type];
    
        $downloadedFiles = [];
        foreach($files as $file) {
            $filePath = $this->dropboxdownload(json_decode($file, true));
            $downloadedFiles[] = new \Illuminate\Http\UploadedFile($filePath, basename($filePath));
        }
    
        // Create a new request for the imageconvert function
        $newRequest = new Request();
        $newRequest->merge(['format' => $format]);
        $newRequest->files->set($type, $downloadedFiles);
    
        // Call the imageconvert function
        return $this->convert($newRequest, $type);
    }
    
    private function dropboxdownload(array $file): string
    {
        $fileUrl = str_replace('dl=0', 'dl=1', $file['name']);
        $downloadFile = file_get_contents($fileUrl);
        $fileName = basename(parse_url($fileUrl, PHP_URL_PATH));
    
        $filePath = 'test/' . $fileName;
        Storage::put($filePath, $downloadFile);
        return Storage::path($filePath);
    }
}