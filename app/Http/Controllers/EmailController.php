<?php

namespace App\Http\Controllers;

use App\Mail\DownloadEmail;
use App\Models\ConversionTypes;
use App\Services\ConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class EmailController extends Controller
{
    public function download(Request $request, $type)
    {
        $validTypes = ConversionTypes::pluck('name')->toArray();
        if (!in_array($type, $validTypes)) {
            return response()->json(['error' => 'Invalid type'], 422);
        }

        try {
            $request->validate([
                'email' => 'required|email',
                'guids' => 'required|array',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $files = [];
        foreach ($request->guids as $guid) {
            $filePaths = glob(storage_path("app/{$type}/{$guid}.*"));
            if (!empty($filePaths)) {
                $files[] = $filePaths[0];
            }
            Log::info(print_r($files, true));
        }

        $conversionService = new ConversionService();
        $guid = $conversionService->AddFilesToFolder($files, $type);
        $conversionService->ZipFiles($guid, $type);
        $conversionService->DeleteDirectory(storage_path("app/{$type}/{$guid}"));
        $email = new DownloadEmail($request->email, $guid, $type);
        Mail::to($request->email)->send($email);
        return response()->json(['guid' => $guid]);
    }
}
