<?php

use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CalculatorController::class, 'index'])->name('calculator.index');

Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator'); // ✅ beda nama

Route::post('/calculate', [CalculatorController::class, 'calculate'])->name('calculator.calculate');

Route::post('/generate-pdf', [PdfController::class, 'generate'])->name('pdf.generate');