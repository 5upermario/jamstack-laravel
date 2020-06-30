<?php

namespace App\Http\Actions\Auth;

use App\User;
use Illuminate\Http\Request;

class LogoutAction
{
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->tokens()->where('name', 'login')->delete();

        return ['success' => true];
    }
}
