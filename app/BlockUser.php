<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BlockUser extends Model
{
    // protected $table = 'blockUsers';
    protected $fillable = ['user_id','blocker_id','status','reason'];


    public function user(){
    	return $this->belongsTo(user::class);
    }

    public function t1()
    {
        return $this->belongsTo(user::class);//c_id - customer id
    }
    public function t11()
    {
        return $this->belongsTo(user::class);//s_id - staff id
    }
}
