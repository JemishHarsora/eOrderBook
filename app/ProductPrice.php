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
}
