<?php

namespace App\Http\Actions\Site;

use App\Http\Requests\CreateSiteRequest;
use App\Site;
use App\User;

class CreateSiteAction
{
    public function __invoke(CreateSiteRequest $request)
    {
        $request->validated();

        /** @var User $user */
        $user       = $request->user();
        $site       = new Site();
        $site->name = $request->input('name');
        $site->save();
        $user->sites()->attach($site->id, ['role' => 'owner']);

        return ['success' => true, 'id' => $site->id];
    }
}
