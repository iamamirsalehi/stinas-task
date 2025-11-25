<?php

use App\Exception\BusinessException;
use App\Exception\UserBusinessException;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Requests\User\RegisterRequest;
use App\Models\User;
use App\Services\Auth\LoginSessionGenerator;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;

it('redirects to dashboard with success message on successful registration', function () {
    $userService = Mockery::mock(UserService::class);
    $loginSessionGenerator = Mockery::mock(LoginSessionGenerator::class);
    $controller = new RegisterController($userService, $loginSessionGenerator);
    
    $username = 'newuser';
    $password = 'password123';
    $passwordConfirmation = 'password123';
    
    $user = User::factory()->make([
        'username' => $username,
    ]);
    
    $request = RegisterRequest::create('/register', 'POST', [
        'username' => $username,
        'password' => $password,
        'password_confirmation' => $passwordConfirmation,
    ]);

    $userService
        ->shouldReceive('add')
        ->once()
        ->with($username, $password)
        ->andReturn($user);

    $loginSessionGenerator
        ->shouldReceive('login')
        ->once()
        ->with($user)
        ->andReturnNull();

    $response = $controller($request);

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())
        ->toContain('/dashboard');
    
    $session = $response->getSession();
    expect($session->get('success'))->toBe('You have been successfully registered!');
});

it('redirects back with error message when username already exists', function () {
    $userService = Mockery::mock(UserService::class);
    $loginSessionGenerator = Mockery::mock(LoginSessionGenerator::class);
    $controller = new RegisterController($userService, $loginSessionGenerator);
    
    $username = 'existinguser';
    $password = 'password123';
    $passwordConfirmation = 'password123';
    
    $request = RegisterRequest::create('/register', 'POST', [
        'username' => $username,
        'password' => $password,
        'password_confirmation' => $passwordConfirmation,
    ]);
    
    // Set the previous URL to /register so redirect()->back() works
    $request->headers->set('referer', '/register');

    $userService
        ->shouldReceive('add')
        ->once()
        ->with($username, $password)
        ->andThrow(UserBusinessException::usernameAlreadyExists());

    $loginSessionGenerator
        ->shouldNotReceive('login');

    $response = $controller($request);

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class);
    
    $session = $response->getSession();
    expect($session->get('error'))->toBe('username already exists')
        ->and($session->get('_old_input.username'))->toBe($username);
});

it('preserves username input on error', function () {
    $userService = Mockery::mock(UserService::class);
    $loginSessionGenerator = Mockery::mock(LoginSessionGenerator::class);
    $controller = new RegisterController($userService, $loginSessionGenerator);
    
    $username = 'existinguser';
    $password = 'password123';
    $passwordConfirmation = 'password123';
    
    $request = RegisterRequest::create('/register', 'POST', [
        'username' => $username,
        'password' => $password,
        'password_confirmation' => $passwordConfirmation,
    ]);
    
    // Set the previous URL to /register so redirect()->back() works
    $request->headers->set('referer', '/register');

    $userService
        ->shouldReceive('add')
        ->once()
        ->andThrow(UserBusinessException::usernameAlreadyExists());

    $response = $controller($request);

    $session = $response->getSession();
    $oldInput = $session->get('_old_input', []);
    expect($oldInput)->toHaveKey('username')
        ->and($oldInput['username'])->toBe($username);
});

it('calls login session generator after successful user creation', function () {
    $userService = Mockery::mock(UserService::class);
    $loginSessionGenerator = Mockery::mock(LoginSessionGenerator::class);
    $controller = new RegisterController($userService, $loginSessionGenerator);
    
    $username = 'newuser';
    $password = 'password123';
    $passwordConfirmation = 'password123';
    
    $user = User::factory()->make([
        'username' => $username,
    ]);
    
    $request = RegisterRequest::create('/register', 'POST', [
        'username' => $username,
        'password' => $password,
        'password_confirmation' => $passwordConfirmation,
    ]);

    $userService
        ->shouldReceive('add')
        ->once()
        ->andReturn($user);

    $loginSessionGenerator
        ->shouldReceive('login')
        ->once()
        ->with(Mockery::on(function ($arg) use ($user) {
            return $arg instanceof User && $arg->username === $user->username;
        }))
        ->andReturnNull();

    $controller($request);
});

