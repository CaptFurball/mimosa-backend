<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'user_id', 'url', 'host', 'title', 'description', 'image_url'
    ];

    public function story()
    {
        return $this->belongTo('App\Story');
    }
}
