<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;

class ProductPrice extends Model
{
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class,'product_id','id');
    }
}
