<?php

namespace App\Http\Actions\Site;

use App\Http\Requests\RenameSiteRequest;
use App\Site;
use Illuminate\Http\Exceptions\HttpResponseException;

class RenameSiteAction
{
    public function __invoke(RenameSiteRequest $request, $id)
    {
        $request->validated();

        /** @var Site $site */
        $site = Site::find($id);
        $site->name = $request->input('name');
        $site->save();
    }
}
