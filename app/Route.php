<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class Route extends Model
{
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function findSeller()
    {
        return $this->belongsTo(User::class,'seller_id');
    }
}
