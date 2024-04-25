<?php

use App\Livewire\CategoriesList;
use App\Livewire\Forms\ProductForm;
use App\Livewire\OrderForm;
use App\Livewire\OrdersList;
use App\Livewire\ProductForm as LivewireProductForm;
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
Route::group(['middleware' => ['auth']], function () {

    Route::get('categories', CategoriesList::class)->name('categories.index');


    Route::prefix('products')->name('products.')
        ->group(function () {
            // Route::get('/create', LivewireProductForm::class)->name('create');
            Route::get('/{product}', \App\Livewire\ProductForm::class)->name('edit');
            Route::get('/', ProductsList::class)->name('index');
                // Route::get('products/create', LivewireProductForm::class)->name('products.create');
        });


    Route::get('orders', OrdersList::class)->name('orders.index');
    Route::get('orders/create', OrderForm::class)->name('orders.create');
    Route::get('orders/{order}', OrderForm::class)->name('orders.edit');
});
