<?php

namespace App\Http\Controllers\User;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Services\Auth\LoginSessionGenerator;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    public function __construct(
        private UserService $userService,
        private LoginSessionGenerator $loginSessionGenerator,
    )
    {}
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request): RedirectResponse
    {
        $username = $request->get('username');
        $password = $request->get('password');

        try{
            $user = $this->userService->add($username, $password);

            $this->loginSessionGenerator->login($user);

            return redirect()->route('user.dashboard')
                ->with('success', 'You have been successfully registered!');
        }catch(BusinessException $exception){
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('error', $exception->getMessage());
        }
    }
}
