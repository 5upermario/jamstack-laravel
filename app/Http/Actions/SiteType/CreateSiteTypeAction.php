<?php

namespace App\Http\Actions\SiteType;

use App\Http\Requests\CreateSiteTypeRequest;
use App\Site;

class CreateSiteTypeAction
{
    public function __invoke(CreateSiteTypeRequest $request, $id)
    {
        $request->validated();

        /** @var Site $site */
        $site = Site::find($id);
        $type = $site->types()->create([
            'name'        => $request->input('name'),
            'api_name'    => $request->input('api_name'),
            'description' => $request->input('description', '')
        ]);

        return ['id' => $type->id];
    }
}
