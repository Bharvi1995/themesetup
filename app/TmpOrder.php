<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TmpOrder extends Model
{
    use SoftDeletes;

    /**
	 * The attribute that assign the database table.
	 *
	 * @var array
	 */
    protected $table = 'tmp_orders';

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = array();

    protected $fillable = [
        'user_id',
        'store_id',
        'product_id',
        'transaction_order_id',
        'email', 
        'amount',
        'currency',
        'quantity',
        'total_amount',
        'token',
    ];

}
