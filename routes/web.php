<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Task
    Route::livewire('task', 'pages::task.index')->name('task');

    Route::middleware('role:admin')->group(function () {

        // Kategori Task
        Route::livewire('category', 'pages::category-task.index')->name('category');

        // Akun pengguna
        Route::livewire('user', 'pages::user.index')->name('user');
    });
});

require __DIR__ . '/settings.php';
