<?php

namespace App\Http\Actions\Auth;

use App\User;
use App\Http\Requests\RegistrationRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegistrationAction
{
    public function __invoke(RegistrationRequest $request)
    {
        $request->validated();

        try {
            $user           = new User;
            $user->name     = '';
            $user->email    = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->save();

            $token = $user->createToken('login');

            return ['token' => $token->plainTextToken];
        } catch (QueryException $e) {
            if (Str::startsWith($e->getMessage(), 'SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint')) {
                return ['errors' => ['credentials' => ['Invalid credentials.']]];
            }
        }
    }
}
