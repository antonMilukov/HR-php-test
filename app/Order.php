<?php

namespace App;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Driver\Query;

class Order extends Model
{

    protected $guarded = ['id'];
    protected $fillable = [
        'status',
        'client_email',
        'partner_id'
    ];

    /**
     *
     * @return Order
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * Return scoped result
     * @return Order[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getForOrderList()
    {
        return self::getInstance()
            ->withRelations()
            ->get();
    }

    /**
     * Relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partner()
    {
        return $this->hasOne('App\Partner', 'id', 'partner_id');
    }

    /**
     * Relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order_products()
    {
        return $this->hasMany('App\OrderProduct', 'order_id', 'id');
    }

    /**
     * Sum of order
     * @return float|int
     */
    public function getSumAttribute()
    {
        $r = 0;
        foreach ($this->order_products as $orderProduct){
            $r += $orderProduct->price * $orderProduct->quantity;
        }
        return $r;
    }

    /**
     * United string with content of order
     * @return string
     */
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

    /**
     * Human readable string of status param
     * @return string
     */
    public function getReadableStatusAttribute()
    {
        $r = 'не определен';
        if (isset(self::$statusData[$this->status])){
            $r = self::$statusData[$this->status][self::ALIAS_TITLE];
        }
        return $r;
    }

    /**
     * Scope for eloquent builder
     * @param $builder
     * @return mixed
     */
    public function scopeWithRelations($builder)
    {
        return $builder->with(['partner', 'order_products', 'order_products.product']);
    }
}
