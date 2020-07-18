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
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_site')->withPivot('role');
    }

    public function types()
    {
        return $this->hasMany('App\SiteType');
    }
}
