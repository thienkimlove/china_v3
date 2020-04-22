<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'details';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    protected $fillable = [

        'shop_id',
        'order_id',
        'detail_code',

        'itemId',
        'itemName',
        'itemImage',
        'itemLink',
        'saleLocation',
        'wholesales',

        'propertiesType',
        'propertiesName',
        'propertiesId',
        'propertiesImage',
        'skuId',

        'itemPriceNDT',
        'quantity',

        'stock',
        'is_tally',
        'is_close_wood',
        'note',
    ];


    public $appends = [
        'total_amount_ndt',
        'total_amount_vnd'
    ];

    public function getTotalAmountNdtAttribute()
    {
        return round($this->itemPriceNDT*$this->quantity);
    }

    public function getTotalAmountVndAttribute()
    {
        return round($this->total_amount_ndt*$this->order->exchange_rate);
    }


    public function afterCreated()
    {
        $orderDetailCount = Detail::where('order_id', $this->order_id)->count();

        $this->update([
            'detail_code' => $this->order->order_code.'_'.($orderDetailCount+1)
        ]);
    }


    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
