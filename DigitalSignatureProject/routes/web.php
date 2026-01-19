<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignatureController;

// Home page - redirect to Nutrient viewer
Route::get('/', function () {
    return view('welcome');
});

// Intervention Image approach routes
Route::get('/signature', [SignatureController::class, 'generateImages'])->name('signature.generate');
Route::get('/signature-form', [SignatureController::class, 'showForm'])->name('signature.form');
Route::post('/save-signature', [SignatureController::class, 'saveSignature'])->name('save.signature');

// Nutrient Web SDK approach routes
Route::get('/nutrient', [SignatureController::class, 'showNutrientViewer'])->name('nutrient.viewer');
