<?php

declare(strict_types=1);

namespace App\Http\Actions\Site;

use Illuminate\Support\Facades\Auth;

class ListSitesAction
{
    public function __invoke()
    {
        return ['data' => Auth::user()->sites];
    }
}
