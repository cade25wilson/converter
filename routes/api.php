<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormatController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\MessagesController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::post('/conversions/audio', [ConversionController::class, 'audioconvert']);
Route::post('/conversions/image', [ConversionController::class, 'imageconvert']);
Route::post('/conversions/spreadsheet', [ConversionController::class, 'spreadsheetconvert']);
Route::post('/conversions/video', [ConversionController::class, 'videoconvert']);

Route::get('/formats/audio', [FormatController::class, 'audio']);
Route::get('/formats/image', [FormatController::class, 'image']);
Route::get('/formats/spreadsheet', [FormatController::class, 'spreadsheet']);
Route::get('/formats/video', [FormatController::class, 'video']);

Route::get('/messages', [MessagesController::class, 'show']);
Route::post('/messages', [MessagesController::class, 'store']);

Route::post('/auth/signup', [AuthController::class, 'create']);

$directories = ['audio', 'image', 'spreadsheet', 'video'];
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

Route::get('/email/verify', function() {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');
 
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');