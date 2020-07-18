<?php

declare(strict_types=1);

namespace App\Http\Actions\Site;

use App\Site;
use App\User;
use Illuminate\Support\Facades\Auth;

class ChangeUserRoleAction
{
    public function __invoke($id, $user_id, $role)
    {
        /** @var Site $site */
        $site = Site::find($id);
        /** @var User $user */
        $user = User::find($user_id);

        if (!$user || $user->id == Auth::user()->id || !$user->sites->contains($site))
            abort(404);

        $user->sites()->updateExistingPivot($id, ['role' => User::getRole($role)]);
    }
}
