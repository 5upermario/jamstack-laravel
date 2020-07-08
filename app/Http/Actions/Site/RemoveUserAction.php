<?php

namespace App\Http\Actions\Site;

use App\Site;

class RemoveUserAction
{
    public function __invoke($id, $user_id)
    {
        /** @var Site $site */
        $site = Site::find($id);
        $site->users()->detach($user_id);
    }
}
