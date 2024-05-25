<?php

namespace App\PaymentGateways;

interface PaymentGatewayContract
{
    public function validate($fieldIds);


    public function charge();


    public function transaction($input, $check_assign_mid);
}
