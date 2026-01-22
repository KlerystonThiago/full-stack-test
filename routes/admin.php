<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\TeamController;
use Illuminate\Support\Facades\Route;


Route::resource('users', UserController::class);
Route::resource('customers', CustomerController::class);
Route::resource('invoices', InvoiceController::class);
Route::resource('teams', TeamController::class);
