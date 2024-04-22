<?php

use App\Livewire\CategoriesList;
use App\Livewire\ProductsList;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
// Route::group(['middleware' => ['auth']], function () {
//     Route::get('categories', CategoriesList::class)->name('categories.index');
//     Route::get('products', ProductsList::class)->name('products.index');
// });
Route::get('categories', CategoriesList::class)->name('categories.index');
Route::get('products', ProductsList::class)->name('products.index');
