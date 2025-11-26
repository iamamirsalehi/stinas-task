<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\Auth\AdminLoginService;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function __construct(private AdminLoginService $adminLoginService)
    {}
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request): RedirectResponse
    {
        $username = $request->get('username');
        $password = $request->get('password');

        try{
            $this->adminLoginService->login($username, $password);
            
            return redirect()->route('admin.dashboard')
                ->with('success', 'You have been successfully logged in as admin!');
        }catch(BusinessException $exception){
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('error', $exception->getMessage());
        }
    }
}

