<?php

namespace App\Http\Actions\Site;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteSiteAction
{
    public function __invoke(Request $request, $id)
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user->ownedSites->contains($id)) {
            return new Response('', 404);
        }

        $user->sites()->delete($id);

        return new JsonResponse(['success' => true]);
    }
}
