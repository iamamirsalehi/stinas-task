<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Ticket\ApproveController;
use App\Http\Controllers\Admin\Ticket\BulkApproveController;
use App\Http\Controllers\Admin\Ticket\BulkRejectController;
use App\Http\Controllers\Admin\Ticket\DownloadTicketFileController;
use App\Http\Controllers\Admin\Ticket\ListTicketController;
use App\Http\Controllers\Admin\Ticket\RejectController;
use App\Http\Controllers\Admin\Ticket\ShowTicketController;
use Illuminate\Support\Facades\Route;

Route::view('/admin/login', 'auth.admin-login')->name('admin.login.show');
Route::post('/admin/login', LoginController::class)->name('admin.login');

Route::prefix('admin')->middleware('auth:admin')->name('admin.')->group(function () {
    Route::get('dashboard', ListTicketController::class)->name('dashboard');
    Route::get('tickets/{id}', ShowTicketController::class)->name('tickets.show');
    Route::get('tickets/{id}/download', DownloadTicketFileController::class)->name('tickets.download');
    Route::post('tickets/{id}/approve', ApproveController::class)->name('tickets.approve');
    Route::post('tickets/{id}/reject', RejectController::class)->name('tickets.reject');
    Route::post('tickets/bulk-approve', BulkApproveController::class)->name('tickets.bulk-approve');
    Route::post('tickets/bulk-reject', BulkRejectController::class)->name('tickets.bulk-reject');
});
