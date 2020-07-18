<?php

namespace App\Http\Actions\Site;

use App\Site;
use App\User;
use Illuminate\Http\Request;

class AssignUserAction
{
    public function __invoke(Request $request, $id)
    {
        /** @var User $user */
        $user = User::where('email', $request->input('email'))->first();
        /** @var Site $site */
        $site = Site::find($id);
        $site->users()->attach($user->id, ['role' => User::getRole($request->input('role'))]);

        return ['success' => true];
    }
}
