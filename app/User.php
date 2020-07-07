<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int        $id
 * @property string     $name
 * @property string     $email
 * @property string     $email_verified_at
 * @property string     $password
 * @property string     $remember_token
 * @property string     $created_at
 * @property string     $updated_at
 * @property Collection $sites
 * @property Collection $ownedSites
 * @property Collection $ownedOrAdminSites
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sites()
    {
        return $this->belongsToMany('App\Site', 'user_site');
    }

    public function ownedSites()
    {
        return $this->belongsToMany('App\Site', 'user_site')->wherePivot('role', 'owner');
    }

    public function ownedOrAdminSites()
    {
        return $this->belongsToMany('App\Site', 'user_site')->wherePivotIn('role', ['owner', 'admin']);
    }
}
