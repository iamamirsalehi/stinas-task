<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login.show');

Route::post('/admin/login', LoginController::class)->name('admin.login');

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

