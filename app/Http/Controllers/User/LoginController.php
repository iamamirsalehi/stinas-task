<?php

namespace App\Http\Controllers\User;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Services\Auth\LoginService;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function __construct(private LoginService $loginService)
    {}
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request): RedirectResponse
    {
        $username = $request->get('username');
        $password = $request->get('password');

        try{
            $this->loginService->login($username, $password);
            
            return redirect()->route('dashboard.')
                ->with('success', 'You have been successfully logged in!');
        }catch(BusinessException $exception){
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('error', $exception->getMessage());
        }
    }
}
