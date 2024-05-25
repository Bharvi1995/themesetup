<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserBankDetailFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required|min:2|max:150',
            // 'address' => 'required|min:5|max:1500',
            // 'aba_routing' => 'max:1500',
            // 'swift_code' => 'required|max:1500',
            // 'iban' => 'required|max:1500',
            // 'account_name' => 'required|min:2|max:100',
            // 'account_number' => 'required|max:100',
            // 'account_holder_address' => 'max:1500',
            // 'additional_information' => 'max:5000',
        ];
    }
}
