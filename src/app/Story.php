<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $table = 'stories';

    protected $fillable = [
        'body',
    ];

    public function user()
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

    public function link()
    {
        return $this->hasOne('App\Link');
    }

    public function sharedStory()
    {
        return $this->hasOne('App\Share');
    }

    public function shared()
    {
        return $this->hasMany('App\Share', 'shared_story_id');
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    public function likes()
    {
        return $this->morphMany('App\Like', 'likeable');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }
}
