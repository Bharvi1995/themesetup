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

class Store extends Model
{
    use SoftDeletes;
    protected $table = 'stores';
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

    public function getAllStores($input, $noList)
    {
        $data = static::select("id", "user_id","slug","name", "contact_us_email as email", 'banner_image_1', "currency"/* , "status" */);
        
        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('stores.user_id', $input['user_id']);
        }

        if (isset($input['name']) && !empty($input['name'])) {
            $data = $data->where('stores.name' ,'like', '%'.$input['name'].'%');
        }
        
        if (isset($input['email']) && !empty($input['email'])) {
            $data = $data->where('stores.contact_us_email' ,'like', '%'.$input['email'].'%');
        }
        
        $data = $data->orderBy('id', 'desc')->paginate($noList);

        return $data;
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function orders() {
        return $this->hasMany('App\Order', 'store_id', 'id');
    }
}
