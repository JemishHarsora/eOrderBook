<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellersBrand extends Model
{
    protected $table ='seller_brand_area';

    public function brands() {
        return $this->hasOne('App\Models\Brand','id','brand_id');
    }

    public function areas() {
        return $this->hasOne('App\Area','id','area_id');
    }
}
