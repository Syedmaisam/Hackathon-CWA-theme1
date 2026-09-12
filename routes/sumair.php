<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Sumair's routes. The /admin panel is registered separately by
// App\Providers\Filament\AdminPanelProvider and needs no entry here.

// Static screen behind the Help tab. No controller: there is nothing to fetch.
Route::get('/help', fn () => Inertia::render('Sumair/Help'))->name('help');
