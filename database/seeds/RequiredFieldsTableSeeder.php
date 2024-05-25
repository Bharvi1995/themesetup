<?php

use Illuminate\Database\Seeder;
use App\RequiredFields;
class RequiredFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('required_fields')->insert(array(
	     	array(
		       'field_title' => 'First name',
		       'field' => 'user_first_name',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Last name',
		       'field' => 'user_last_name',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Email',
		       'field' => 'user_email',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Address',
		       'field' => 'user_address',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Customer Order id',
		       'field' => 'user_order_ref',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Country',
		       'field' => 'user_country',
		       'field_type' => 'string',
		       'field_validation' => 'required|max:2|min:2|regex:(\\b[A-Z]+\\b)',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'State',
		       'field' => 'user_state',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'City',
		       'field' => 'user_city',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Zip',
		       'field' => 'user_zip',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Ip address',
		       'field' => 'user_ip',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Phone No',
		       'field' => 'user_phone_no',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Amount',
		       'field' => 'user_amount',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Currency',
		       'field' => 'user_currency',
		       'field_type' => 'string',
		       'field_validation' => 'required|max:3|min:3|regex:(\\b[A-Z]+\\b)',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Card No',
		       'field' => 'user_card_no',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'ccexpiry month',
		       'field' => 'user_ccexpiry_month',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'ccexpiry year',
		       'field' => 'user_ccexpiry_year',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'cvv number',
		       'field' => 'user_cvv_number',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	     	array(
		       'field_title' => 'Redirect url',
		       'field' => 'user_redirect_url',
		       'field_type' => 'string',
		       'field_validation' => 'required',
		       'created_at' => date('Y-m-d H:i:s'),
		       'updated_at' => date('Y-m-d H:i:s')
	     	),
	   	));
    }
}
