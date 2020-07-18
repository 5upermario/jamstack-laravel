<?php

declare(strict_types=1);

namespace App\Http\Actions\Site;

use App\Site;
use App\User;
use Illuminate\Support\Facades\Auth;

class ChangeOwnerAction
{
    public function __invoke($id, $user_id)
    {
        /** @var Site $site */
        $site = Site::find($id);
        /** @var User $admin */
        $admin = $site->users()->find($user_id);

        if ($admin && $admin->site->role == User::SITE_ROLE_ADMIN) {
            $site->users()->updateExistingPivot(Auth::user()->id, ['role' => User::SITE_ROLE_ADMIN]);
            $site->users()->updateExistingPivot($admin->id, ['role' => User::SITE_ROLE_OWNER]);
        }
    }
}
