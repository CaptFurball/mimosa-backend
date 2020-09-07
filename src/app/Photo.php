<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
        'user_id', 'path',
    ];

    public function story()
    {
        return $this->belongTo('App\Story');
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    public function likes()
    {
        return $this->morphMany('App\Like', 'likeable');
    }
}
