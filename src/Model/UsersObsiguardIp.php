<?php

namespace App\Model;

class UsersObsiguardIp extends Model
{
    protected $connection = 'web';

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
