<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int        $id
 * @property string     $name
 * @property string     $created_at
 * @property string     $updated_at
 * @property Collection $users
 * @property Collection $types
 */
class Site extends Model
{
    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_site')->as('site')->withPivot('role');
    }

    public function types()
    {
        return $this->hasMany('App\SiteType');
    }

    public function jsonSerialize()
    {
        $data = $this->toArray();

        if (!empty($data['user'])) {
            $data['role'] = $data['user']['role'];
            unset($data['user']);
        }

        return $data;
    }
}
