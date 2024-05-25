<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class CheckoutButton extends Model
{
    protected $table = 'checkout_buttons';
    protected $guarded = array();

    public function getData()
    {
    	$data = static::select("checkout_buttons.*")->orderBy("checkout_buttons.id","DESC")->where('user_id', \Auth::user()->id)->get();

        return $data;
    }

    public function storeData($input) {
        return static::create($input);
    }

    public function findData($id)
    {
        return static::find($id);
    }

    public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }

    public function destroyData($id)
    {
        return static::where('id',$id)->delete();
    }

    public function getUserDetailsByCheckoutBtnId($button_id)
    {
        return static::select('users.id as id', 'users.mid as mid',
                'users.amexmid as amexmid', 'users.visamid as visamid',
                'users.mastercardmid as mastercardmid', 'users.discovermid as discovermid')
                ->join('users','users.id','=','checkout_buttons.user_id')
                ->where('checkout_buttons.id', $button_id)
                ->first();
    }
}
