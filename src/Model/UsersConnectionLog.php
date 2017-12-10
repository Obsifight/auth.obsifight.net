<?php

namespace App\Model;

class UsersConnectionLog extends Model
{
    protected $connection = 'web';
    protected $fillable = ['ip', 'user_id', 'type'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
