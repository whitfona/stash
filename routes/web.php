<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('search'));

Route::livewire('/search', 'search')->name('search');

Route::livewire('/locations', 'locations.index')->name('locations.index');
Route::livewire('/locations/create', 'locations.form')->name('locations.create');
Route::livewire('/locations/{location}', 'locations.show')->name('locations.show');
Route::livewire('/locations/{location}/edit', 'locations.form')->name('locations.edit');

Route::livewire('/items', 'items.index')->name('items.index');
Route::livewire('/items/create', 'items.form')->name('items.create');
Route::livewire('/items/{item}', 'items.show')->name('items.show');
Route::livewire('/items/{item}/edit', 'items.form')->name('items.edit');
