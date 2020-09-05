<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $table = 'stories';

    protected $fillable = [
        'user_id', 'body', 'tags',
    ];

    public function users()
    {
        return $this->belongsTo('App\User');
    }

    public function video()
    {
        return $this->hasOne('App\Video');
    }

    public function photo()
    {
        return $this->hasOne('App\Photo');
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
