<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Merchantapplication extends Model
{
    use Cachable;
    protected $table = 'merchantapplications';
    protected $guarded = array();


    public function getData($input)
    {
        $data = static::select("merchantapplications.*")
            ->leftJoin('users', function($join) use($input) {
                $join->on('users.id', '=', 'merchantapplications.user_id')
                    ->where('users.is_sub_user', '0');
            });

        if(isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email',  'like', '%' . $input['email'] . '%');
        }
        if(isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('merchantapplications.company_name',  'like', '%' . $input['company_name'] . '%');
        }

        if(isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use($input){
                    $query->orWhere('merchantapplications.company_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.company_number', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.first_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.last_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.email', 'like', '%'.$input['global_search'].'%');
                });
        }

        $data = $data->orderBy("merchantapplications.id","desc")
            ->get();

        return $data;
    }

    public function getAllMerchantapplicationForExcel($input)
    {
        $data = static::select("merchantapplications.company_name as company_name","merchantapplications.company_number as company_number","merchantapplications.first_name as first_name","merchantapplications.last_name as last_name","merchantapplications.email as email");

        if(isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use($input){
                    $query->orWhere('merchantapplications.company_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.company_number', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.first_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.last_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.email', 'like', '%'.$input['global_search'].'%');
                });
        }

        $data = $data->orderBy("merchantapplications.id","DESC")->get();

        return $data;
    }

    public function findData($id)
    {
        return static::find($id);
    }

    public function AddData($input)
    {
        return static::create($input);
    }

    public function destroyData($id)
    {
        return static::find($id)->delete();
    }

    public function updateData($id, $input)
    {
        \DB::beginTransaction();
        try {
            if($input['user_id'] != 0) {
                \DB::table('users')
                    ->where('id', $input['user_id'])
                    ->update(['email' => $input['email']]);
            }

            static::find($id)->update($input);

            \DB::commit();
            return true;

        } catch (\Exception $e) {
            \DB::rollback();
            return false;
        }
    }

    public function cloneMerchant($id, $input)
    {
        \DB::beginTransaction();
        try {
            // Inser Data in users table
            $user['name'] = '';
            $user['email'] = $input['email'];
            $user['password'] = \Hash::make('codenest@123');
            $user['token'] = 'codenest@123';
            $lastUserId = \DB::table('users')->insertGetId($user);

            // insert data in merchantapplications table
            $MerchantData = \DB::table('merchantapplications')->where('id', $id)->first();
            $MerchantData->email = $input['email'];
            $MerchantData->user_id = $lastUserId;
            unset($MerchantData->id);
            $MerchantDataInsert['user_id'] = $MerchantData->user_id;
            $MerchantDataInsert['company_name'] = $MerchantData->company_name;
            $MerchantDataInsert['company_number'] = $MerchantData->company_number;
            $MerchantDataInsert['first_name'] = $MerchantData->first_name;
            $MerchantDataInsert['last_name'] = $MerchantData->last_name;
            $MerchantDataInsert['email'] = $MerchantData->email;
            $MerchantDataInsert['area_code'] = $MerchantData->area_code;
            $MerchantDataInsert['phone_no'] = $MerchantData->phone_no;
            $MerchantDataInsert['street_address_1'] = $MerchantData->street_address_1;
            $MerchantDataInsert['street_address_2'] = $MerchantData->street_address_2;
            $MerchantDataInsert['city'] = $MerchantData->city;
            $MerchantDataInsert['state'] = $MerchantData->state;
            $MerchantDataInsert['zip_code'] = $MerchantData->zip_code;
            $MerchantDataInsert['country'] = $MerchantData->country;
            $MerchantDataInsert['website'] = $MerchantData->website;
            $MerchantDataInsert['describe_products'] = $MerchantData->describe_products;
            $MerchantDataInsert['bussiness_registration_date'] = $MerchantData->describe_products;
            $MerchantDataInsert['monthly_sales_volume'] = $MerchantData->describe_products;
            $MerchantDataInsert['average_price'] = $MerchantData->describe_products;
            $MerchantDataInsert['max_price'] = $MerchantData->max_price;
            $MerchantDataInsert['marketing_and_sales_procedure'] = $MerchantData->marketing_and_sales_procedure;
            $MerchantDataInsert['descriptor_needed'] = $MerchantData->descriptor_needed;
            $MerchantDataInsert['have_credit_card'] = $MerchantData->have_credit_card;
            $MerchantDataInsert['banking_details'] = $MerchantData->banking_details;
            $MerchantDataInsert['chargeback_percentage'] = $MerchantData->chargeback_percentage;
            $MerchantDataInsert['login_status'] = $MerchantData->login_status;
            $MerchantDataInsert['created_at'] = date('Y-m-d H:i:s');
            $MerchantDataInsert['updated_at'] = date('Y-m-d H:i:s');
            \DB::table('merchantapplications')->insert($MerchantDataInsert);

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollback();
            return false;
        }
    }

    public function destroyWithUserId($user_id)
    {
        return static::where('user_id',$user_id)->delete();
    }

    // ================================================
    /* method : getMerchantByID
    * @param  :
    * @Description : get merchant and user data by user_id
    */// ==============================================
    public function getMerchantByID($user_id)
    {
        return static::select(
                'merchantapplications.company_name',
                'users.*'
            )
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'merchantapplications.user_id')
                    ->where('users.is_sub_user', '0');
            })
            ->where('users.id', '=', $user_id)

        ->first();
    }
    public function getMerchantCompanyName()
    {
        return static::select('company_name', 'user_id')
        ->join('users', function($join){
            $join->on('users.id', '=', 'merchantapplications.user_id')
                ->where('users.main_user_id', '0');
            })
        ->get();
    }


}
