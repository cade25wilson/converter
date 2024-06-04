<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FileSizeController;
use App\Http\Controllers\FormatController;
use App\Http\Controllers\MessagesController;
use App\Models\ConversionTypes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::delete('/conversions/delete', [ConversionController::class, 'delete']);
Route::post('/conversions/{type}', [ConversionController::class, 'convert']);

Route::post('/conversions/url/{type}', [ConversionController::class, 'urlconvert']);

Route::post('/email/{type}', [EmailController::class, 'download']);

Route::get('/filesize/{type}', [FileSizeController::class, 'totalTransferredSize']);

Route::get('/formats/{type}', [FormatController::class, 'format']);

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