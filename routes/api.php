<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\FileSizeController;
use App\Http\Controllers\FormatController;
use App\Http\Controllers\MessagesController;
use App\Models\ConversionTypes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::delete('/conversions/delete', [ConversionController::class, 'delete']);
Route::post('/conversions/archive', [ConversionController::class, 'archiveconvert']);
Route::post('/conversions/audio', [ConversionController::class, 'audioconvert']);
Route::post('/conversions/image', [ConversionController::class, 'imageconvert']);
Route::post('/conversions/spreadsheet', [ConversionController::class, 'spreadsheetconvert']);
Route::post('/conversions/video', [ConversionController::class, 'videoconvert']);

Route::post('/conversions/url/archive', [ConversionController::class, 'urlconvert']);
Route::post('/conversions/url/audio', [ConversionController::class, 'urlconvert']);
Route::post('/conversions/url/image', [ConversionController::class, 'urlconvert']);
Route::post('/conversions/url/spreadsheet', [ConversionController::class, 'urlconvert']);
Route::post('/conversions/url/video', [ConversionController::class, 'urlconvert']);

Route::get('/filesize/all', [FileSizeController::class, 'totalTransferredSize']);
Route::get('/filesize/archive', [FileSizeController::class, 'totalTransferredArchiveSize']);
Route::get('/filesize/audio', [FileSizeController::class, 'totalTransferredAudioSize']);
Route::get('/filesize/image', [FileSizeController::class, 'totalTransferredImageSize']);
Route::get('/filesize/spreadsheet', [FileSizeController::class, 'totalTransferredSpreadsheetSize']);
Route::get('/filesize/video', [FileSizeController::class, 'totalTransferredVideoSize']);

Route::get('/formats/archive', [FormatController::class, 'archive']);
Route::get('/formats/audio', [FormatController::class, 'audio']);
Route::get('/formats/image', [FormatController::class, 'image']);
Route::get('/formats/spreadsheet', [FormatController::class, 'spreadsheet']);
Route::get('/formats/video', [FormatController::class, 'video']);

Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:2,1');

Route::get('/messages', [MessagesController::class, 'show']);
Route::post('/messages', [MessagesController::class, 'store']);

Route::post('/auth/signup', [AuthController::class, 'create']);
Route::post('/auth/signin', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

$directories = ConversionTypes::all()->pluck('name')->toArray();
foreach($directories as $directory){
    Route::get('/' . $directory . '/{filename}', function ($filename) use ($directory) {
        $path = storage_path('app/' . $directory . '/' . $filename);
        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        return Response::make($file, 200)->header("Content-Type", $type);
    })->where('filename', '.*');
}