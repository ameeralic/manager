<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseTagController;
use App\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('payment-methods', PaymentMethodController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('expense-categories/quick-create', [ExpenseCategoryController::class, 'quickCreate'])->name('expense-categories.quick-create');
    Route::resource('expense-categories', ExpenseCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('expense-tags', ExpenseTagController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::get('/', fn () => redirect()->route('dashboard'));
