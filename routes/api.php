<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;


Route::post('/transfer', [TransactionController::class, 'transfer'])
    ->name('transactions.transfer');
