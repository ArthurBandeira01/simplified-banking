<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TransactionController::class, 'index'])->name('statusApi');
Route::post('/transfer', [TransactionController::class, 'transfer'])->name('transfer');
