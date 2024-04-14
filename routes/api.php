<?php

use App\Http\Controllers\FormatController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/convert', [ImageController::class, 'Convert']);

Route::resource('formats', FormatController::class);