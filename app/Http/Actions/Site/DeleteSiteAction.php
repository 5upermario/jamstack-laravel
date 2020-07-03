<?php

namespace App\Http\Actions\Site;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteSiteAction
{
    public function __invoke(Request $request, $id)
    {
        /** @var User $user */
        $user = $request->user();
        $user->sites()->delete($id);

        return new JsonResponse(['success' => true]);
    }
}
