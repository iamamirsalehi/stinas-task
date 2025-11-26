<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Ticket\DownloadTicketFileController;
use App\Http\Controllers\Admin\Ticket\ListTicketController;
use App\Http\Controllers\Admin\Ticket\ShowTicketController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login.show');

Route::post('/admin/login', LoginController::class)->name('admin.login');

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', ListTicketController::class)->name('admin.dashboard');
    Route::get('/admin/tickets/{id}', ShowTicketController::class)->name('admin.tickets.show');
    Route::get('/admin/tickets/{id}/download', DownloadTicketFileController::class)->name('admin.tickets.download');
});

Route::post('')->name('tickets.store');
Route::post('')->name('admin.tickets.bulk-action');
Route::post('{id}/app')->name('admin.tickets.approve');
Route::post('{id}/re')->name('admin.tickets.reject');

