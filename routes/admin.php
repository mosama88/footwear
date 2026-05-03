<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/', 'dashboard.index')->name('dashboard');
});


Route::post('login',[])->name('dashboard.login');
