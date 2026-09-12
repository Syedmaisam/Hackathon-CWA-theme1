<?php

use App\Http\Controllers\Maisam\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReportController::class, 'create'])->name('reports.create');
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
Route::post('/reports/{report}/clarify', [ReportController::class, 'clarify'])->name('reports.clarify');
Route::post('/reports/{report}/confirm-category', [ReportController::class, 'confirmCategory'])->name('reports.confirm-category');
