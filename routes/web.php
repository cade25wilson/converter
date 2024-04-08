<?php

use Imagick as Imagick;
use App\Http\Controllers\ImageController;
use App\Models\Format;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $formats = Imagick::queryFormats();
    // alphbetize the formats
    sort($formats);
    return $formats;
});

Route::get('/test', function () {
    $formats = Format::all(); 
    return view('formtest', ['formats' => $formats]);
});

Route::post('/test', [ImageController::class, 'convert'])->name('uploadImage');
