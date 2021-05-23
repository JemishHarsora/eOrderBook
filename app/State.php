<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class State extends Model
{
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
