<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcrController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/ocr/extract', [OcrController::class, 'extract'])->name('ocr.test');
Route::get("/ocr/test", function() {
    return view("ocr.test");
});
