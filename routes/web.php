<?php

use App\Http\Controllers\Admin\OrderFileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('/privacy', 'privacy')->name('privacy');

Route::get('/admin/order-files/{file}/download', OrderFileController::class)
    ->middleware('auth')
    ->name('admin.order-files.download');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
