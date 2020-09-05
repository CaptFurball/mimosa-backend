<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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

    public function stories()
    {
        return $this->hasMany('App\Story');
    }

    public function videos()
    {
        return $this->hasMany('App\Video');
    }

    public function photos()
    {
        return $this->hasMany('App\Photo');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function following()
    {
        return $this->hasMany('App\Follower');
    }

    public function followers()
    {
        return $this->hasMany('App\Follower', 'following');
    }
}
