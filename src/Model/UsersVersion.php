<?php

namespace App\Model;

class UsersVersion extends Model
{
    protected $connection = 'web';
    protected $fillable = ['user_id', 'version'];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
