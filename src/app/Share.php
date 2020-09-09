<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    protected $fillable = [
        'user_id', 'shared_story_id'
    ];

    public function story()
    {
        return $this->belongsTo('App\Story', 'shared_story_id');
    }
}
