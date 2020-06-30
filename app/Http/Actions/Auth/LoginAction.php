<?php

namespace App\Http\Actions\Auth;

use App\Http\Requests\LoginRequest;
use App\User;

class LoginAction
{
    public function __invoke(LoginRequest $request)
    {
        $request->validated();

        /** @var User $user */
        $user = $request->input('user');

        return [
            'token' => $user->createToken('login')->plainTextToken
        ];
    }
}
