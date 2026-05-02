<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'website.index')->name('home');


Route::view('/men', 'website.pages.men')->name('men');
Route::view('/women', 'website.pages.women')->name('women');
Route::view('/abouts', 'website.pages.abouts')->name('abouts');
Route::view('/contacts', 'website.pages.contacts')->name('contacts');
Route::view('/carts', 'website.pages.carts')->name('carts');



require __DIR__ . '/settings.php';
