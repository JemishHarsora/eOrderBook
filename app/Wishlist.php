<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function productPrice()
    {
        return $this->hasOne(ProductPrice::class,'id','product_id');
    }
}
