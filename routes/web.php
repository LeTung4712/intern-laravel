<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\CalculatorController;
/*
 * Global Routes
 *
 * Routes that are used between both frontend and backend.
 */

// Switch between the included languages
Route::get('lang/{lang}', [LocaleController::class, 'change'])->name('locale.change');

/*
 * Frontend Routes
 */
Route::group(['as' => 'frontend.'], function () {
    includeRouteFiles(__DIR__ . '/frontend/');
});

/*
 * Backend Routes
 *
 * These routes can only be accessed by users with type `admin`
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    includeRouteFiles(__DIR__ . '/backend/');
});

//tạo route cho phép tính
Route::group(['prefix' => 'calculator', 'as' => 'calculator.'], function () {
    Route::get('/', [CalculatorController::class, 'index'])->name('index');
    Route::post('/calculate', [CalculatorController::class, 'calculate'])->name('calculate');
    Route::get('/history', [CalculatorController::class, 'history'])->name('history');
    Route::post('/clear', [CalculatorController::class, 'clearHistory'])->name('clear');
});
