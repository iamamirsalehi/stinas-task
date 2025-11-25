<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('displays the login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Login');
    $response->assertSee('Username');
    $response->assertSee('Password');
});

it('redirects to dashboard on successful login', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/login', [
        'username' => 'testuser',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('success', 'You have been successfully logged in!');
    $this->assertAuthenticatedAs($user);
});

it('shows error message when username does not exist', function () {
    $response = $this->post('/login', [
        'username' => 'nonexistent',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHas('error', 'user does not exist');
    $response->assertSessionHasInput('username', 'nonexistent');
    $this->assertGuest();
});

it('shows error message when password is incorrect', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => Hash::make('correctpassword'),
    ]);

    $response = $this->post('/login', [
        'username' => 'testuser',
        'password' => 'wrongpassword',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHas('error', 'username or password is invalid');
    $response->assertSessionHasInput('username', 'testuser');
    $this->assertGuest();
});

it('requires username field', function () {
    $response = $this->post('/login', [
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('username');
});

it('requires password field', function () {
    $response = $this->post('/login', [
        'username' => 'testuser',
    ]);

    $response->assertSessionHasErrors('password');
});

it('preserves username input on validation error', function () {
    $response = $this->post('/login', [
        'username' => 'testuser',
    ]);

    $response->assertSessionHasInput('username', 'testuser');
});

it('displays success message on dashboard after login', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => Hash::make('password123'),
    ]);

    $this->post('/login', [
        'username' => 'testuser',
        'password' => 'password123',
    ]);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('You have been successfully logged in!');
});

it('displays error message on login page after failed login', function () {
    $response = $this->post('/login', [
        'username' => 'nonexistent',
        'password' => 'password123',
    ]);

    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('user does not exist');
});

it('logs in user with correct credentials case-sensitively', function () {
    $user = User::factory()->create([
        'username' => 'TestUser',
        'password' => Hash::make('password123'),
    ]);

    // Test exact case match
    $response = $this->post('/login', [
        'username' => 'TestUser',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

it('does not log in user with wrong case username', function () {
    $user = User::factory()->create([
        'username' => 'TestUser',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/login', [
        'username' => 'testuser', // different case
        'password' => 'password123',
    ]);

    $response->assertRedirect('/login');
    $this->assertGuest();
});

