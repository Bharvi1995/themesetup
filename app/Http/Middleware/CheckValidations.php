<?php

namespace App\Http\Middleware;

use Closure;
use Validator;
class CheckValidations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        // create $customer_order_id
        if (isset($request['customer_order_id']) && $request['customer_order_id'] != null) {
            $customer_order_id = $request['customer_order_id'];
        } else {
            $request['customer_order_id'] = null;
            $customer_order_id = null;
        }
        // gateway object
        $check_assign_mid = checkAssignMID($request['payment_gateway_id']);

        if ($check_assign_mid == false) {

            // \Log::info(['error_type' => 'Trying to get property \'gateway_table\' of non-object', 'details' => $input]);

            return response()->json([
                'status' => 'fail',
                'message' => 'Your account is temporarily disabled, please contact admin.',
                'customer_order_id' => $request['customer_order_id'],
                'api_key' => $request['api_key'],
            ]);
        }
        
        // validations
        $validations = json_decode($check_assign_mid->required_fields, 1);

        // create validations array
        foreach ($validations as $value) {
            $new_validations[$value] = config('required_field.total_fields.'.$value.'.validate');
        }
        $request_only = config('required_field.required_all_fields');
        $input = $request->only($request_only);
        $validator = Validator::make($input, $new_validations);
        if ($validator->fails()) {

            $errors = $validator->errors()->messages();

            return response()->json([
                'status' => 'fail',
                'message' => 'Some parameters are missing or invalid request data.',
                'errors' => $errors,
                'customer_order_id' => $customer_order_id,
                'api_key' => $input['api_key'],
            ]);
        }
        return $next($request);
    }
}
