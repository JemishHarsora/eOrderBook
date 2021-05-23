<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer_shop extends Model
{
    public function user()
    {
        return $this->belongTo(User::class);
    }
}
