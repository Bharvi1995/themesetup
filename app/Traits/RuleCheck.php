<?php

namespace App\Traits;

use App\User;
use App\Application;

trait RuleCheck
{
    // ================================================
    /* method : userCardRulesCheck
    * @param  : 
    * @description : user card rules check and mid
    */// ===============================================
    public function userCardRulesCheck($input, $user)
    {
        $rules = \DB::table('rules')
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->where('user_id', $input["user_id"])
            ->where('rules_type', 'Card')
            ->orderBy('priority', 'asc')
            ->get();

        if (count($rules) == 0) {
            return false;
        }

        $category = Application::where('user_id', $input['user_id'])
            ->value('category_id');

        $input['user_country'] = $input['user_country'] ?? 'NOAPP';
        $input['bin_country_code'] = $input['bin_country_code'] ?? 'NOAPP';
        $input['card_type'] = $input['card_type'] ?? '0';
        $input['is_white_label'] = $input['is_white_label'] ?? 0;
        $input['bin_number'] = $input['bin_number'] ?? 0;

        foreach ($rules as $value) {
            $rule_condition = str_replace(
                ['currency', 'amount', 'card_type', 'country', 'category', 'bin_cou_code', 'card_wl','bin_number'],
                ['"' . $input['user_currency'] . '"', '"' . $input['amount_in_usd'] . '"', $input['card_type'], '"' . $input['user_country'] . '"', '"' . $category . '"', '"' . $input['bin_country_code'] . '"', '"' . strval($input['is_white_label']) . '"', '"' . $input['bin_number'] . '"'],
                $value->rule_condition
            );
            $condition = "return " . $rule_condition . ";";

            try {
                $test = eval($condition);
                if ($test) {

                    $input['payment_gateway_id'] = $value->assign_mid;

                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            return $value->assign_mid;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::info([
                    'user rule issue' => $condition,
                    'exceptions' => $e->getMessage()
                ]);
            }
        }
        return false;
    }

    // ================================================
    /* method : cardRulesCheck
    * @param  : 
    * @description : user card rules check and mid
    */// ===============================================
    public function cardRulesCheck($input, $user)
    {
        $rules = \DB::table('rules')
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->whereNull('user_id')
            ->where('rules_type', 'Card')
            ->orderBy('priority', 'asc')
            ->get();

        if (count($rules) == 0) {
            return false;
        }

        $category = Application::where('user_id', $input['user_id'])
            ->value('category_id');

        $input['user_country'] = $input['user_country'] ?? 'NOAPP';
        $input['bin_country_code'] = $input['bin_country_code'] ?? 'NOAPP';
        $input['card_type'] = $input['card_type'] ?? '0';
        $input['is_white_label'] = $input['is_white_label'] ?? 0;
        $input["user"] = $input["user_id"] ?? "NOAPP";
        $input['bin_number'] = $input['bin_number'] ?? 0;

        foreach ($rules as $value) {
            $rule_condition = str_replace(
                ['currency', 'amount', 'card_type', 'country', 'category', 'bin_cou_code', "card_wl", 'user', 'bin_number'],
                ['"' . $input['user_currency'] . '"', '"' . $input['amount_in_usd'] . '"', $input['card_type'], '"' . $input['user_country'] . '"', '"' . $category . '"', '"' . $input['bin_country_code'] . '"', '"' . strval($input['is_white_label']) . '"', '"' . $input['user'] . '"', '"' . $input['bin_number'] . '"'],
                $value->rule_condition
            );
            $condition = "return " . $rule_condition . ";";

            try {
                $test = eval($condition);
                if ($test) {

                    $input['payment_gateway_id'] = $value->assign_mid;

                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            return $value->assign_mid;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::info([
                    'v2 global rule issue' => $condition,
                    'exceptions' => $e->getMessage()
                ]);
            }
        }
        return false;
    }

    // ================================================
    /* method : userUPIRulesCheck
    * @param  : 
    * @description : user upi rules check and mid
    */// ===============================================
    public function userUPIRulesCheck($input, $user)
    {
        $rules = \DB::table('rules')
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->where('user_id', $input["user_id"])
            ->where('rules_type', 'UPI')
            ->orderBy('priority', 'asc')
            ->get();

        if (count($rules) == 0) {
            return false;
        }

        $category = Application::where('user_id', $input['user_id'])
            ->value('category_id');

        $input['user_country'] = $input['user_country'] ?? 'NOAPP';
        $input['bin_country_code'] = $input['bin_country_code'] ?? 'NOAPP';
        $input['card_type'] = $input['card_type'] ?? '0';

        foreach ($rules as $value) {
            $rule_condition = str_replace(
                ['currency', 'amount', 'card_type', 'country', 'category', 'bin_cou_code'],
                ['"' . $input['user_currency'] . '"', '"' . $input['amount_in_usd'] . '"', $input['card_type'], '"' . $input['user_country'] . '"', '"' . $category . '"', '"' . $input['bin_country_code'] . '"'],
                $value->rule_condition
            );
            $condition = "return " . $rule_condition . ";";

            try {
                $test = eval($condition);
                if ($test) {

                    $input['payment_gateway_id'] = $value->assign_mid;

                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            return $value->assign_mid;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::info([
                    'v2 user rule issue' => $condition,
                    'exceptions' => $e->getMessage()
                ]);
            }
        }
        return false;
    }

    // ================================================
    /* method : upiRulesCheck
    * @param  : 
    * @description : user upi rules check and mid
    */// ===============================================
    public function upiRulesCheck($input, $user)
    {
        $rules = \DB::table('rules')
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->whereNull('user_id')
            ->where('rules_type', 'UPI')
            ->orderBy('priority', 'asc')
            ->get();

        if (count($rules) == 0) {
            return false;
        }

        $category = Application::where('user_id', $input['user_id'])
            ->value('category_id');

        $input['user_country'] = $input['user_country'] ?? 'NOAPP';
        $input['bin_country_code'] = $input['bin_country_code'] ?? 'NOAPP';
        $input['card_type'] = $input['card_type'] ?? '0';

        foreach ($rules as $value) {
            $rule_condition = str_replace(
                ['currency', 'amount', 'card_type', 'country', 'category', 'bin_cou_code'],
                ['"' . $input['user_currency'] . '"', '"' . $input['amount_in_usd'] . '"', $input['card_type'], '"' . $input['user_country'] . '"', '"' . $category . '"', '"' . $input['bin_country_code'] . '"'],
                $value->rule_condition
            );
            $condition = "return " . $rule_condition . ";";

            try {
                $test = eval($condition);
                if ($test) {

                    $input['payment_gateway_id'] = $value->assign_mid;

                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            return $value->assign_mid;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::info([
                    'v2 global rule issue' => $condition,
                    'exceptions' => $e->getMessage()
                ]);
            }
        }
        return false;
    }

    // ================================================
    /* method : userCryptoRulesCheck
    * @param  : 
    * @description : user crypto rules check and mid
    */// ===============================================
    public function userCryptoRulesCheck($input, $user)
    {
        $rules = \DB::table('rules')
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->where('user_id', $input["user_id"])
            ->where('rules_type', 'Crypto')
            ->orderBy('priority', 'asc')
            ->get();

        if (count($rules) == 0) {
            return false;
        }

        $category = Application::where('user_id', $input['user_id'])
            ->value('category_id');

        $input['user_country'] = $input['user_country'] ?? 'NOAPP';
        $input['bin_country_code'] = $input['bin_country_code'] ?? 'NOAPP';
        $input['card_type'] = $input['card_type'] ?? '0';

        foreach ($rules as $value) {
            $rule_condition = str_replace(
                ['currency', 'amount', 'card_type', 'country', 'category', 'bin_cou_code'],
                ['"' . $input['user_currency'] . '"', '"' . $input['amount_in_usd'] . '"', $input['card_type'], '"' . $input['user_country'] . '"', '"' . $category . '"', '"' . $input['bin_country_code'] . '"'],
                $value->rule_condition
            );
            $condition = "return " . $rule_condition . ";";

            try {
                $test = eval($condition);
                if ($test) {

                    $input['payment_gateway_id'] = $value->assign_mid;

                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            return $value->assign_mid;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::info([
                    'v2 user rule issue' => $condition,
                    'exceptions' => $e->getMessage()
                ]);
            }
        }
        return false;
    }

    // ================================================
    /* method : cryptoRulesCheck
    * @param  : 
    * @description : user crypto rules check and mid
    */// ===============================================
    public function cryptoRulesCheck($input, $user)
    {
        $rules = \DB::table('rules')
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->whereNull('user_id')
            ->where('rules_type', 'Crypto')
            ->orderBy('priority', 'asc')
            ->get();

        if (count($rules) == 0) {
            return false;
        }

        $category = Application::where('user_id', $input['user_id'])
            ->value('category_id');

        $input['user_country'] = $input['user_country'] ?? 'NOAPP';
        $input['bin_country_code'] = $input['bin_country_code'] ?? 'NOAPP';
        $input['card_type'] = $input['card_type'] ?? '0';

        foreach ($rules as $value) {
            $rule_condition = str_replace(
                ['currency', 'amount', 'card_type', 'country', 'category', 'bin_cou_code'],
                ['"' . $input['user_currency'] . '"', '"' . $input['amount_in_usd'] . '"', $input['card_type'], '"' . $input['user_country'] . '"', '"' . $category . '"', '"' . $input['bin_country_code'] . '"'],
                $value->rule_condition
            );
            $condition = "return " . $rule_condition . ";";

            try {
                $test = eval($condition);
                if ($test) {

                    $input['payment_gateway_id'] = $value->assign_mid;

                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            return $value->assign_mid;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::info([
                    'v2 global rule issue' => $condition,
                    'exceptions' => $e->getMessage()
                ]);
            }
        }
        return false;
    }

    // ================================================
    /* method : userBankRulesCheck
    * @param  : 
    * @description : user bank rules check and mid
    */// ===============================================
    public function userBankRulesCheck($input, $user)
    {
        $rules = \DB::table('rules')
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->where('user_id', $input["user_id"])
            ->where('rules_type', 'Bank')
            ->orderBy('priority', 'asc')
            ->get();

        if (count($rules) == 0) {
            return false;
        }

        $category = Application::where('user_id', $input['user_id'])
            ->value('category_id');

        $input['user_country'] = $input['user_country'] ?? 'NOAPP';
        $input['bin_country_code'] = $input['bin_country_code'] ?? 'NOAPP';
        $input['card_type'] = $input['card_type'] ?? '0';

        foreach ($rules as $value) {
            $rule_condition = str_replace(
                ['currency', 'amount', 'card_type', 'country', 'category', 'bin_cou_code'],
                ['"' . $input['user_currency'] . '"', '"' . $input['amount_in_usd'] . '"', $input['card_type'], '"' . $input['user_country'] . '"', '"' . $category . '"', '"' . $input['bin_country_code'] . '"'],
                $value->rule_condition
            );
            $condition = "return " . $rule_condition . ";";

            try {
                $test = eval($condition);
                if ($test) {

                    $input['payment_gateway_id'] = $value->assign_mid;

                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            return $value->assign_mid;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::info([
                    'v2 user rule issue' => $condition,
                    'exceptions' => $e->getMessage()
                ]);
            }
        }
        return false;
    }

    // ================================================
    /* method : bankRulesCheck
    * @param  : 
    * @description : user bank rules check and mid
    */// ===============================================
    public function bankRulesCheck($input, $user)
    {
        $rules = \DB::table('rules')
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->whereNull('user_id')
            ->where('rules_type', 'Bank')
            ->orderBy('priority', 'asc')
            ->get();

        if (count($rules) == 0) {
            return false;
        }

        $category = Application::where('user_id', $input['user_id'])
            ->value('category_id');

        $input['user_country'] = $input['user_country'] ?? 'NOAPP';
        $input['bin_country_code'] = $input['bin_country_code'] ?? 'NOAPP';
        $input['card_type'] = $input['card_type'] ?? '0';

        foreach ($rules as $value) {
            $rule_condition = str_replace(
                ['currency', 'amount', 'card_type', 'country', 'category', 'bin_cou_code'],
                ['"' . $input['user_currency'] . '"', '"' . $input['amount_in_usd'] . '"', $input['card_type'], '"' . $input['user_country'] . '"', '"' . $category . '"', '"' . $input['bin_country_code'] . '"'],
                $value->rule_condition
            );
            $condition = "return " . $rule_condition . ";";

            try {
                $test = eval($condition);
                if ($test) {

                    $input['payment_gateway_id'] = $value->assign_mid;

                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            return $value->assign_mid;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::info([
                    'v2 global rule issue' => $condition,
                    'exceptions' => $e->getMessage()
                ]);
            }
        }
        return false;
    }
}