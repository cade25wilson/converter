<?php

use Imagick as Imagick;
use App\Http\Controllers\ImageController;
use App\Models\Format;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/{vue_capture?}', function () {
    return view('welcome');
})->where('vue_capture', '[\/\w\.-]*');

Route::get('/test', function () {
    $formats = Format::all(); 
    return view('formtest', ['formats' => $formats]);
});

Route::post('/test', [ImageController::class, 'convert'])->name('uploadImage');
