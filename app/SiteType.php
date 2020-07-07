<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 * @property string $api_name
 * @property string $description
 * @property Site   $site
 */
class SiteType extends Model
{
    protected $fillable = [
        'name',
        'api_name',
        'description',
    ];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }
}
