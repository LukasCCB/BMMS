<?php

use App\Http\Controllers\BulkSendController;
use App\Http\Controllers\RegisterNumberController;
use Illuminate\Support\Facades\Route;

Route::post('/add-bulk-number', [RegisterNumberController::class, 'addBulkNumber']);
Route::post('/bulk-send-file', [BulkSendController::class, 'BulkSendFile']);
