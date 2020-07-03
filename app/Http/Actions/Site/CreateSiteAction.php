<?php

namespace App\Http\Actions\Site;

use App\Http\Requests\CreateSiteRequest;
use App\User;

class CreateSiteAction
{
    public function __invoke(CreateSiteRequest $request)
    {
        $request->validated();

        /** @var User $user */
        $user = $request->user();
        $site = $user->sites()->create(['name' => $request->input('name')], ['role' => 'owner']);

        return ['success' => true, 'id' => $site->id];
    }
}
