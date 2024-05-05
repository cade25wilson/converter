<?php 

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/{vue_capture?}', function () {
    return view('welcome');
})->where('vue_capture', '^(?!api).*$');
?>