<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    /**
	 * The attribute that assign the database table.
	 *
	 * @var array
	 */
    protected $table = 'orders';

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

    public function getAllOrders($input, $noList)
    {
        $data = static::select('product_id', 'store_id', 'email', 'token', 'amount', 'currency', 'quantity', 'created_at');

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('user_id', $input['user_id']);
        }

        $data = $data->orderBy('id', 'desc')->paginate($noList);

        return $data;
    }

    public function product() {
        return $this->belongsTo('App\StoreProduct', 'product_id', 'id');
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id', 'id');
    }
}
