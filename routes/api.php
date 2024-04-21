<?php

use App\Http\Controllers\FormatController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\MessagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/conversions/image', [ConversionController::class, 'imageconvert']);
Route::post('/conversions/audio', [ConversionController::class, 'audioconvert']);

Route::get('/formats/image', [FormatController::class, 'index']);
Route::get('/formats/audio', [FormatController::class, 'audio']);

Route::get('/images/{imagePath}', function ($imagePath) {
    $path = public_path('images/' . $imagePath);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
})->where('imagePath', '.*');

Route::post('/messages', [MessagesController::class, 'store']);
Route::get('/messages', [MessagesController::class, 'show']);