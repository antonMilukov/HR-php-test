<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    public function product()
    {
        return $this->hasOne('App\Eloquent\Product', 'id', 'product_id');
    }
}
