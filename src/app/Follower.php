<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $fillable = [
        'following',
    ];

    public function followingUser()
    {
        return $this->belongsTo('App\User', 'following');
    }

    public function followerUser()
    {
        return $this->belongsTo('App\User');
    }
}
