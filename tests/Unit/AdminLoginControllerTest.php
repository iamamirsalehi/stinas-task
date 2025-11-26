<?php

use App\Exception\AdminException;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\Auth\AdminLoginService;
use Illuminate\Http\RedirectResponse;

it('redirects to admin dashboard with success message on successful admin login', function () {
    $loginService = Mockery::mock(AdminLoginService::class);
    $controller = new LoginController($loginService);
    
    $username = 'adminuser';
    $password = 'password123';
    
    $request = LoginRequest::create('/admin/login', 'POST', [
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
        ->toContain('/admin/dashboard');
    
    $session = $response->getSession();
    expect($session->get('success'))->toBe('You have been successfully logged in as admin!');
});

it('redirects back with error message when admin does not exist', function () {
    $loginService = Mockery::mock(AdminLoginService::class);
    $controller = new LoginController($loginService);
    
    $username = 'nonexistent-admin';
    $password = 'password123';
    
    $request = LoginRequest::create('/admin/login', 'POST', [
        'username' => $username,
        'password' => $password,
    ]);
    
    // Set the previous URL to /admin/login so redirect()->back() works
    $request->headers->set('referer', '/admin/login');

    $loginService
        ->shouldReceive('login')
        ->once()
        ->with($username, $password)
        ->andThrow(AdminException::adminDoesNotExist());

    $response = $controller($request);

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class);
    
    $session = $response->getSession();
    expect($session->get('error'))->toBe('admin does not exist')
        ->and($session->get('_old_input.username'))->toBe($username);
});

it('redirects back with error message when admin password is invalid', function () {
    $loginService = Mockery::mock(AdminLoginService::class);
    $controller = new LoginController($loginService);
    
    $username = 'adminuser';
    $password = 'wrongpassword';
    
    $request = LoginRequest::create('/admin/login', 'POST', [
        'username' => $username,
        'password' => $password,
    ]);
    
    // Set the previous URL to /admin/login so redirect()->back() works
    $request->headers->set('referer', '/admin/login');

    $loginService
        ->shouldReceive('login')
        ->once()
        ->with($username, $password)
        ->andThrow(AdminException::usernameOrPasswordIsInvalid());

    $response = $controller($request);

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class);
    
    $session = $response->getSession();
    expect($session->get('error'))->toBe('username or password is invalid')
        ->and($session->get('_old_input.username'))->toBe($username);
});


