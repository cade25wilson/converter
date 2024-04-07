<?php

use Imagick as Imagick;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $formats = Imagick::queryFormats();
    // alphbetize the formats
    sort($formats);
    return $formats;
});

Route::get('/test', function () {
    return view('formtest');
});

Route::post('/test', [ImageController::class, 'convert'])->name('uploadImage');
