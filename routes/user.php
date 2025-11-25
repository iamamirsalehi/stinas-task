<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\RegisterController;

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', RegisterController::class)->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', LoginController::class)->name('login');

Route::get('/dashboard', function () {
    return view('user.dashboard');
})->name('user.dashboard');

Route::get('/tickets/create', function () {
    return view('user.tickets.create');
})->name('tickets.create');

