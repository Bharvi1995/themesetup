<?php

namespace App\PaymentGateways;

use App\RequiredField;
use App\WebsiteUrl;
use Illuminate\Support\Facades\Validator;

class PaymentGateway
{
    public function validate($fieldIds)
    {
        // create validations array
        $requiredFields = RequiredField::whereIn('id', $fieldIds)->get();

        $validations = [];
        foreach ($requiredFields as $field) {
            $validations[$field->field] = $field->field_validation;
        }

        return Validator::make(request()->all(), $validations);
    }
}
