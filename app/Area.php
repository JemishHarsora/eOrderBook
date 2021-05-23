<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class Area extends Model
{
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
