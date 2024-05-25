<?php

namespace App;

namespace App;

use DB;
use Mail;
use Auth;
use Exception;
use App\User;
use Carbon\Carbon;
use App\Application;
use App\Mail\TransactionMail;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class StoreProduct extends Model
{
    use SoftDeletes;
    protected $table = 'store_products';
    protected $guarded = [];
    protected $dates = ['created_at', 'updated_at'];

    public function findData($id)
    {
        return static::find($id);
    }

    public function storeData($input)
    {
        return static::create($input);
    }

    public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }

    public function destroyData($id)
    {
        return static::where('id', $id)->delete();
    }

    public function getStoreProducts($input, $noList)
    {
        $data = static::select("*");

        if (isset($input['store_id']) && $input['store_id'] != null) {
            $data = $data->where('store_products.store_id', $input['store_id']);
        }

        if (isset($input['name']) && !empty($input['name']))
        {
            $data = $data->where('name', 'like', '%'.$input['name'].'%');
        }

        if (isset($input['status']) && $input['status'] != null)
        {
            $data = $data->where('status', $input['status']);
        }

        $data = $data->orderBy('id', 'desc')->paginate($noList);

        return $data;
    }

    public function orders() {
        return $this->hasMany('App\Order', 'product_id', 'id');
    }
}
