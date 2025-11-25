<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\User\Ticket\AddNewTicketController;
use App\Http\Controllers\User\Ticket\ListTicketController;

Route::name('auth.')->middleware('guest')->group(function (){
    Route::view('register', 'auth.register')->name('register.show');
    Route::post('register', RegisterController::class)->name('register');
    
    Route::view('login', 'auth.login')->name('login.show');
    Route::post('login', LoginController::class)->name('login');
});

Route::prefix('dashboard')->name('dashboard.')->middleware('auth')->group(function (){
    Route::get('',ListTicketController::class)->name('index');
    Route::view('tickets/create', 'user.tickets.create')->name('tickets.create.show');
    Route::post('tickets', AddNewTicketController::class)->name('tickets.create');
});
