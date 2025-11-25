<?php

use App\Exception\BusinessException;
use App\Exception\UserBusinessException;
use App\Http\Controllers\User\LoginController;
use App\Http\Requests\User\LoginRequest;
use App\Models\User;
use App\Services\Auth\LoginService;
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
        ->toContain('/dashboard');
    
    // Check that the response has the success message in session
    $session = $response->getSession();
    expect($session->get('success'))->toBe('You have been successfully logged in!');
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
    
    // Set the previous URL to /login so redirect()->back() works
    $request->headers->set('referer', '/login');

    $loginService
        ->shouldReceive('login')
        ->once()
        ->with($username, $password)
        ->andThrow(UserBusinessException::userDoesNotExist());

    $response = $controller($request);

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class);
    
    $session = $response->getSession();
    expect($session->get('error'))->toBe('user does not exist')
        ->and($session->get('_old_input.username'))->toBe($username);
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
    
    // Set the previous URL to /login so redirect()->back() works
    $request->headers->set('referer', '/login');

    $loginService
        ->shouldReceive('login')
        ->once()
        ->with($username, $password)
        ->andThrow(UserBusinessException::usernameOrPasswordIsInvalid());

    $response = $controller($request);

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class);
    
    $session = $response->getSession();
    expect($session->get('error'))->toBe('username or password is invalid')
        ->and($session->get('_old_input.username'))->toBe($username);
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
    
    // Set the previous URL to /login so redirect()->back() works
    $request->headers->set('referer', '/login');

    $loginService
        ->shouldReceive('login')
        ->once()
        ->andThrow(UserBusinessException::usernameOrPasswordIsInvalid());

    $response = $controller($request);

    $session = $response->getSession();
    $oldInput = $session->get('_old_input', []);
    expect($oldInput)->toHaveKey('username')
        ->and($oldInput['username'])->toBe($username);
});
