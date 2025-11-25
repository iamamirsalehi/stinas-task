<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

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

Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/tickets/{id}', function ($id) {
    return view('admin.tickets.show', ['ticket' => null]);
})->name('admin.tickets.show');

Route::post('')->name('tickets.store');
Route::post('')->name('admin.tickets.bulk-action');
Route::post('{id}/app')->name('admin.tickets.approve');
Route::post('{id}/re')->name('admin.tickets.reject');