<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('displays the register page', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertSee('Create Account');
    $response->assertSee('Username');
    $response->assertSee('Password');
    $response->assertSee('Confirm Password');
});

it('redirects to dashboard on successful registration', function () {
    $response = $this->post('/register', [
        'username' => 'newuser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('success', 'You have been successfully registered!');
    
    $this->assertDatabaseHas('users', [
        'username' => 'newuser',
    ]);
    
    $user = User::where('username', 'newuser')->first();
    $this->assertAuthenticatedAs($user);
});

it('shows error message when username already exists', function () {
    User::factory()->create([
        'username' => 'existinguser',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/register', [
        'username' => 'existinguser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHas('error', 'username already exists');
    $response->assertSessionHasInput('username', 'existinguser');
    $this->assertGuest();
});

it('requires username field', function () {
    $response = $this->post('/register', [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('username');
});

it('requires password field', function () {
    $response = $this->post('/register', [
        'username' => 'newuser',
    ]);

    $response->assertSessionHasErrors('password');
});

it('requires password confirmation', function () {
    $response = $this->post('/register', [
        'username' => 'newuser',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('password');
});

it('validates password confirmation matches', function () {
    $response = $this->post('/register', [
        'username' => 'newuser',
        'password' => 'password123',
        'password_confirmation' => 'differentpassword',
    ]);

    $response->assertSessionHasErrors('password');
});

it('validates username minimum length', function () {
    $response = $this->post('/register', [
        'username' => 'ab', // less than 3 characters
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('username');
});

it('validates password minimum length', function () {
    $response = $this->post('/register', [
        'username' => 'newuser',
        'password' => '1234', // less than 5 characters
        'password_confirmation' => '1234',
    ]);

    $response->assertSessionHasErrors('password');
});

it('preserves username input on validation error', function () {
    $response = $this->post('/register', [
        'username' => 'newuser',
        'password' => '1234', // invalid password
        'password_confirmation' => '1234',
    ]);

    $response->assertSessionHasInput('username', 'newuser');
});

it('preserves username input on business exception', function () {
    User::factory()->create([
        'username' => 'existinguser',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/register', [
        'username' => 'existinguser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasInput('username', 'existinguser');
});

it('displays success message on dashboard after registration', function () {
    $this->post('/register', [
        'username' => 'newuser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('You have been successfully registered!');
});

it('displays error message on register page after failed registration', function () {
    User::factory()->create([
        'username' => 'existinguser',
        'password' => Hash::make('password123'),
    ]);

    $this->post('/register', [
        'username' => 'existinguser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertSee('username already exists');
});

it('creates user with hashed password', function () {
    $this->post('/register', [
        'username' => 'newuser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::where('username', 'newuser')->first();
    
    expect($user)->not->toBeNull()
        ->and($user->password)->not->toBe('password123')
        ->and(Hash::check('password123', $user->password))->toBeTrue();
});

it('logs in user automatically after registration', function () {
    $response = $this->post('/register', [
        'username' => 'newuser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::where('username', 'newuser')->first();
    $this->assertAuthenticatedAs($user);
});

it('handles username case sensitivity', function () {
    User::factory()->create([
        'username' => 'TestUser',
        'password' => Hash::make('password123'),
    ]);

    // Different case should be allowed
    $response = $this->post('/register', [
        'username' => 'testuser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertDatabaseHas('users', [
        'username' => 'testuser',
    ]);
});

