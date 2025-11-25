<?php

use App\Exception\BusinessException;
use App\Exception\UserBusinessException;
use App\Http\Controllers\User\LoginController;
use App\Http\Requests\User\LoginRequest;
use App\Models\User;
use App\Services\AuthService\LoginService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

it('redirects to dashboard with success message on successful login', function () {
    $loginService = Mockery::mock(LoginService::class);
    $controller = new LoginController($loginService);
    
    $username = 'testuser';
    $password = 'password123';
    
    $request = LoginRequest::create('/login', 'POST', [
        'username' => $username,
        'password' => $password,
    ]);

    $loginService
        ->shouldReceive('login')
        ->once()
        ->with($username, $password)
        ->andReturnNull();

    $response = $controller($request);

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())
        ->toContain('/dashboard')
        ->and(session('success'))
        ->toBe('You have been successfully logged in!');
});

it('redirects back with error message when user does not exist', function () {
    $loginService = Mockery::mock(LoginService::class);
    $controller = new LoginController($loginService);
    
    $username = 'nonexistent';
    $password = 'password123';
    
    $request = LoginRequest::create('/login', 'POST', [
        'username' => $username,
        'password' => $password,
    ]);

    $loginService
        ->shouldReceive('login')
        ->once()
        ->with($username, $password)
        ->andThrow(UserBusinessException::userDoesNotExist());

    $response = $controller($request);

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())
        ->toContain('/login')
        ->and(session('error'))
        ->toBe('user does not exist')
        ->and(session('_old_input.username'))
        ->toBe($username);
});

it('redirects back with error message when password is invalid', function () {
    $loginService = Mockery::mock(LoginService::class);
    $controller = new LoginController($loginService);
    
    $username = 'testuser';
    $password = 'wrongpassword';
    
    $request = LoginRequest::create('/login', 'POST', [
        'username' => $username,
        'password' => $password,
    ]);

    $loginService
        ->shouldReceive('login')
        ->once()
        ->with($username, $password)
        ->andThrow(UserBusinessException::usernameOrPasswordIsInvalid());

    $response = $controller($request);

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())
        ->toContain('/login')
        ->and(session('error'))
        ->toBe('username or password is invalid')
        ->and(session('_old_input.username'))
        ->toBe($username);
});

it('preserves username input on error', function () {
    $loginService = Mockery::mock(LoginService::class);
    $controller = new LoginController($loginService);
    
    $username = 'testuser';
    $password = 'wrongpassword';
    
    $request = LoginRequest::create('/login', 'POST', [
        'username' => $username,
        'password' => $password,
    ]);

    $loginService
        ->shouldReceive('login')
        ->once()
        ->andThrow(UserBusinessException::usernameOrPasswordIsInvalid());

    $response = $controller($request);

    expect(session('_old_input'))
        ->toHaveKey('username')
        ->and(session('_old_input.username'))
        ->toBe($username);
});
