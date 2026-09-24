<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/api/sheet/{tabKey}', [GoogleSheetController::class, 'getSheetData']);
