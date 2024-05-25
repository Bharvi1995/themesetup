<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // the attribure that is assigned the table
    protected $table = 'product';

    protected $fillable = [
        'user_id', 'name', 'currency', 'price', 'link', 'image'
    ];

    public function getAllProducts($user_id)
    {
        return Product::where('user_id', $user_id)->get();
    }

    // ================================================
    /* method : getProductAndUser
    * @param  : 
    * @Description : get product and user record
    */// ==============================================
    public function getProductAndUser($link)
    {
    	$data = static::select(
                    'product.*',
                    'users.email',
                    'users.api_key',
                    'users.main_user_id',
                    'users.is_sub_user'
                )
				->join('users', 'product.user_id','users.id')
                ->where('product.link', $link)
				->first();

        return $data;
    }
}
