<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductPrice::class);
    }

    public function pickup_point()
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function refund_request()
    {
        return $this->hasOne(RefundRequest::class);
    }

    public function getseller()
    {
        return $this->belongsTo(user::class,'seller_id');
    }
}
