<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ExportSummaryToPdfController;
use App\Http\Livewire\Register;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::domain(config("filament.domain"))
    ->middleware(config("filament.middleware.base"))
    ->prefix(config("filament.path"))
    ->group(function () {
        Route::get("/register", Register::class)->name("register");
    });

Route::get('download', [DownloadController::class, 'download'])->name('download');
Route::get('export_summary_to_pdf', ExportSummaryToPdfController::class)->name('export_summary_to_pdf');
