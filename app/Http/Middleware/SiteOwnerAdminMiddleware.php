<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteOwnerAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user->ownedOrAdminSites->contains($request->route('id'))) {
            throw new NotFoundHttpException();
        }

        return $next($request);
    }
}
