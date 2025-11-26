<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

it('displays the admin login page', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
    $response->assertSee('Admin Login');
    $response->assertSee('Username');
    $response->assertSee('Password');
});

it('redirects to admin dashboard on successful admin login', function () {
    $admin = Admin::factory()->create([
        'username' => 'adminuser',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/admin/login', [
        'username' => 'adminuser',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertSessionHas('success', 'You have been successfully logged in as admin!');
    $this->assertAuthenticatedAs($admin, 'admin');
});

it('shows error message when admin username does not exist', function () {
    // First visit the login page to set the referer
    $this->get('/admin/login');

    $response = $this->post('/admin/login', [
        'username' => 'nonexistent-admin',
        'password' => 'password123',
    ]);

    $response->assertSessionHas('error', 'admin does not exist');
    $response->assertSessionHasInput('username', 'nonexistent-admin');
    $this->assertGuest('admin');
});

it('shows error message when admin password is incorrect', function () {
    $admin = Admin::factory()->create([
        'username' => 'adminuser',
        'password' => Hash::make('correctpassword'),
    ]);

    // First visit the login page to set the referer
    $this->get('/admin/login');

    $response = $this->post('/admin/login', [
        'username' => 'adminuser',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHas('error', 'username or password is invalid');
    $response->assertSessionHasInput('username', 'adminuser');
    $this->assertGuest('admin');
});


