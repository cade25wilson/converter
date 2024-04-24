<?php

use App\Http\Controllers\FormatController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\MessagesController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/conversions/audio', [ConversionController::class, 'audioconvert']);
Route::post('/conversions/image', [ConversionController::class, 'imageconvert']);
Route::post('/conversions/spreadsheet', [ConversionController::class, 'spreadsheetconvert']);
Route::post('/conversions/video', [ConversionController::class, 'videoconvert']);

Route::get('/formats/audio', [FormatController::class, 'audio']);
Route::get('/formats/image', [FormatController::class, 'image']);
Route::get('/formats/spreadsheet', [FormatController::class, 'spreadsheet']);
Route::get('/formats/video', [FormatController::class, 'video']);

Route::get('/audio/{audioPath}', function ($audioPath) {
    $path = public_path('audio/' . $audioPath);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
})->where('audioPath', '.*');

Route::get('/images/{imagePath}', function ($imagePath) {
    $path = public_path('images/' . $imagePath);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
})->where('imagePath', '.*');

Route::get('/video/{videoPath}', function ($videoPath) {
    $path = public_path('video/' . $videoPath);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
})->where('videoPath', '.*');

Route::get('/spreadsheet/{spreadsheetPath}', function ($spreadsheetPath) {
    $path = public_path('spreadsheet/' . $spreadsheetPath);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
})->where('spreadsheetPath', '.*');

Route::get('/messages', [MessagesController::class, 'show']);
Route::post('/messages', [MessagesController::class, 'store']);