<?php

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
