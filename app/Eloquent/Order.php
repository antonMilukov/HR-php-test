<?php

namespace App\Eloquent;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public static function getInstance()
    {
        return new self();
    }

    public static function getForOrderList()
    {
        return self::getInstance()
            ->with(['partner', 'order_products', 'order_products.product'])
            ->get();
    }

    public function partner()
    {
        return $this->hasOne('App\Eloquent\Partner', 'id', 'partner_id');
    }

    public function order_products()
    {
        return $this->hasMany('App\Eloquent\OrderProduct', 'order_id', 'id');
    }

    public function getSumAttribute()
    {
        $r = 0;
        foreach ($this->order_products as $orderProduct){
            $r += $orderProduct->price * $orderProduct->quantity;
        }
        return $r;
    }

    public function getContentAttribute()
    {
        $r = [];
        foreach ($this->order_products as $orderProduct){
            $product = $orderProduct->product;
            $r []= $product->name;
        }
        return implode(', ', $r);
    }

    const STATUS_NEW = 0;
    const STATUS_ACCEPTED = 10;
    const STATUS_FINISHED = 20;
    const ALIAS_TITLE = 'title';
    public static $statusData = [
        self::STATUS_NEW => [
            self::ALIAS_TITLE => 'новый'
        ],
        self::STATUS_ACCEPTED => [
            self::ALIAS_TITLE => 'подтвержден'
        ],
        self::STATUS_FINISHED => [
            self::ALIAS_TITLE => 'завершен'
        ]
    ];

    public function getReadableStatusAttribute()
    {
        $r = 'не определен';
        if (isset(self::$statusData[$this->status])){
            $r = self::$statusData[$this->status][self::ALIAS_TITLE];
        }
        return $r;
    }
}
