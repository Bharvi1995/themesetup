<?php

namespace App\Http\Controllers\Admin;

use App\Application;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Categories;
use App\Rules;
use App\RulesList;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CreateRulesController extends Controller
{
    protected $Categories, $ExcludeRules, $application;
    public function __construct()
    {
        $this->Categories = new Categories;
        $this->ExcludeRules = array(159, 160);
        $this->application = new Application;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment_gateway_id = DB::table('middetails')->get();
        $rules = Rules::select("rules.id", "rules.rules_name", "rules.assign_mid", "middetails.bank_name", "rules.status", "rules.rule_condition_view")->leftjoin('middetails', function ($join) {
            $join->on('middetails.id', '=', 'rules.assign_mid');
        })->whereNull("rules.deleted_at")->get();
        $CardRules = Rules::where("rules_type", "Card")->whereNull("rules.user_id")->whereNull("rules.deleted_at")->count();
        $CryptoRules = Rules::where("rules_type", "Crypto")->whereNull("rules.user_id")->whereNull("rules.deleted_at")->count();
        $BankRules = Rules::where("rules_type", "Bank")->whereNull("rules.user_id")->whereNull("rules.deleted_at")->count();
        $upiRules = Rules::where("rules_type", "UPI")->whereNull("rules.user_id")->whereNull("rules.deleted_at")->count();
        return view('admin.createRules.index', compact('rules', 'payment_gateway_id', 'CardRules', 'CryptoRules', 'BankRules', 'upiRules'));
    }

    public function list($type)
    {
        if (empty($type)) {
            return redirect()->route("admin.create_rules.index");
        }
        $rules = Rules::select("rules.id", "rules.rules_name", "rules.assign_mid", "middetails.bank_name", "rules.status", "rules.rule_condition_view")->leftjoin('middetails', function ($join) {
            $join->on('middetails.id', '=', 'rules.assign_mid');
        })->where("rules.rules_type", $type)->whereNull('rules.user_id')->whereNull("rules.deleted_at")->get();
        if ($type == "Card") {
            $mid_type = 1;
        } elseif ($type == "Crypto") {
            $mid_type = 3;
        } elseif ($type == "Bank") {
            $mid_type = 2;
        } elseif ($type == "UPI") {
            $mid_type = 4;
        }
        $payment_gateway_id = DB::table('middetails')->where(["mid_type" => $mid_type])->get();
        return view('admin.createRules.list', compact('rules', 'payment_gateway_id', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $categories = $this->Categories->getData();
        $type = $request->type;
        if (empty($type)) {
            return redirect()->route("admin.create_rules.index");
        }
        $users = $this->application->getCompanyName();
        return view('admin.createRules.create', compact('categories', 'type', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            //'is_card_type' => 'nullable|numeric|in:0,1',
            'type' => 'required'
        ]);
        $input = Arr::except($request->all(), ['_token']);
        DB::beginTransaction();
        try {
            $is_success = false;
            $rules = new Rules();
            $rules->rules_name = $input["title"];
            $rules->rules_type = $input["type"];
            //$rules->is_card_type = $input["is_card_type"] ?? 0;
            $count = count($input["selector"]);
            if ($count >= 1) {
                $str = "";
                $qryStr = "";
                for ($i = 1; $i <= $count - 1; $i++) {
                    $index = $i - 1;
                    if ($input["txHiddenAdd"][$i] == "Y") {
                        if (isset($input["selector"][$i]) && !empty($input["selector"][$i])) {
                            $fieldOperator = "";
                            $fieldValue = "";
                            if ($input["selector"][$i] == "amount") {
                                $fieldOperator = "amountoperator_" . $index;
                                $fieldValue = "amount_" . $index;
                                $str .= $input["selector"][$i] . " " . $input[$fieldOperator] . " " . $input[$fieldValue] . " AND ";
                                $qryStr .= $input["selector"][$i] . " ";
                                if ($input[$fieldOperator] == "=") {
                                    $qryStr .= "==";
                                } else {
                                    $qryStr .= $input[$fieldOperator];
                                }
                                $qryStr .= " " . $input[$fieldValue] . " && ";
                            } else {
                                if ($input["selector"][$i] == "currency") {
                                    $fieldOperator = "currencyoperator_" . $index;
                                    $fieldValue = "currency_" . $index;
                                }
                                if ($input["selector"][$i] == "category") {
                                    $fieldOperator = "categoryoperator_" . $index;
                                    $fieldValue = "category_" . $index;
                                }
                                if ($input["selector"][$i] == "country") {
                                    $fieldOperator = "countryoperator_" . $index;
                                    $fieldValue = "country_" . $index;
                                }
                                if ($input["selector"][$i] == "bin_cou_code") {
                                    $fieldOperator = "bincountryoperator_" . $index;
                                    $fieldValue = "bincountry_" . $index;
                                }
                                if ($input["selector"][$i] == "bin_number") {
                                    $fieldOperator = "binnumberoperator_" . $index;
                                    $fieldValue = "binnumber_" . $index;
                                }
                                if ($input["selector"][$i] == "card_type") {
                                    $fieldOperator = "cardtypeoperator_" . $index;
                                    $fieldValue = "cardtype_" . $index;
                                }
                                if ($input["selector"][$i] == "card_wl") {
                                    $fieldOperator = "cardwloperator_" . $index;
                                    $fieldValue = "cardwl_" . $index;
                                }
                                if ($input["selector"][$i] == "user") {
                                    $fieldOperator = "useroperator_" . $index;
                                    $fieldValue = "user_" . $index;
                                }
                                $str .= $input["selector"][$i] . " " . $input[$fieldOperator] . " ";
                                //$qryStr .= $input["selector"][$i]." ";
                                if ($input[$fieldOperator] == "=") {
                                    if ($input["selector"][$i] == "bin_number") {
                                        $str .= "'" . $input[$fieldValue] . "'";
                                        $qryStr .= $input["selector"][$i] . " == " . "'" . $input[$fieldValue] . "'";
                                    }else{
                                        $str .= "'" . $input[$fieldValue]["0"] . "'";
                                        $qryStr .= $input["selector"][$i] . " == " . "'" . $input[$fieldValue]["0"] . "'";
                                    }
                                } elseif ($input[$fieldOperator] == "In") {
                                    if ($input["selector"][$i] == "bin_number") {
                                        $strBin = explode(',', $input[$fieldValue]);
                                        $str .= json_encode($strBin);
                                        $qryStr .= "in_array(" . $input["selector"][$i] . ", " . json_encode($strBin) . ")";
                                    }else{
                                        $str .= json_encode($input[$fieldValue]);
                                        $qryStr .= "in_array(" . $input["selector"][$i] . ", " . json_encode($input[$fieldValue]) . ")";
                                    }
                                } elseif ($input[$fieldOperator] == "NotIn") {
                                    if ($input["selector"][$i] == "bin_number") {
                                        $strBin = explode(',', $input[$fieldValue]);
                                        $str .= json_encode($strBin);
                                        $qryStr .= "!in_array(" . $input["selector"][$i] . ", " . json_encode($strBin) . ")";
                                    }else{
                                        $str .= json_encode($input[$fieldValue]);
                                        $qryStr .= "!in_array(" . $input["selector"][$i] . ", " . json_encode($input[$fieldValue]) . ")";
                                    }
                                }
                                // else{
                                //     $str .= json_encode($input[$fieldValue]);
                                //     $qryStr .= json_encode($input[$fieldValue]);
                                // }
                                $str .= " AND ";
                                $qryStr .= " && ";
                            }
                        }
                    }
                }
            }
            $rules->rule_condition = rtrim($qryStr, " && ");
            $rules->rule_condition_view = rtrim($str, " AND ");
            if ($rules->save()) {
                Rules::where("id", $rules->id)->update(["priority" => $rules->id]);
                DB::commit();
                \Session::put('success', 'Rule has been created successfully.');
                return redirect()->route('admin.create_rules.list', [$request->type]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            \Session::put('error', 'Something went wrong.Try Again.');
            return redirect()->back();
        }
    }

    public function changeAssignMID(Request $request)
    {
        try {
            if (Rules::where("id", $request->get('id'))->update(['assign_mid' => $request->get('assignMID')])) {
                return response()->json([
                    'success' => true,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function deleteRules(Request $request)
    {
        $input = Arr::except($request->all(), array('_token', '_method'));
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                if (!in_array($value, $this->ExcludeRules)) {
                    Rules::where("id", $value)->delete();
                }
            }
            return response()->json(['success' => true]);
        }
        try {
            if (!in_array($request->get('id'), $this->ExcludeRules)) {
                Rules::where("id", $request->get('id'))->delete();
            }
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    public function changeRulesStatus(Request $request, $status)
    {
        $update = ['status' => $status];
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                if (!in_array($value, $this->ExcludeRules)) {
                    Rules::where("id", $value)->update($update);
                }
            }
            if ($status == '1') {
                $msg = 'Rule activated successfully';
            } else {
                $msg = 'Rule deactivated successfully';
            }
            return response()->json([
                'success' => true,
                'msg' => $msg,
            ]);
        }
        $id = $request->get('id');
        Rules::where("id", $id)->update($update);
        if ($status == '1') {
            $msg = 'Rule activated successfully';
        } else {
            $msg = 'Rule deactivated successfully';
        }
        return response()->json([
            'success' => true,
            'msg' => $msg,
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $status = $request->get('status');
        $update = ['status' => $status];
        Rules::where("id", $id)->update($update);
        if ($status == '1') {
            notificationMsg('success', 'Rule activated successfully');
        } else {
            notificationMsg('success', 'Rule deactivated successfully ');
        }
        return redirect()->back();
        //return redirect()->route('admin.create_rules.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = $this->Categories->getData();
        $rule = Rules::find($id);
        $conditions = explode('AND', $rule->rule_condition_view);
        $users = $this->application->getCompanyName();
        if ($rule) {
            return view('admin.createRules.edit', compact('rule', 'categories', 'conditions', 'users'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'type' => 'required'
        ]);
        $input = Arr::except($request->all(), ['_token']);
        // dd($input);
        DB::beginTransaction();
        try {
            $is_success = false;
            $rules = Rules::find($id);
            $rules->rules_name = $input["title"];
            $count = count($input["selector"]);
            if ($count >= 1) {
                // dd($rules);
                $str = "";
                $qryStr = "";
                for ($i = 1; $i <= $count - 1; $i++) {
                    $index = $i - 1;
                    if ($input["txHiddenAdd"][$i] == "Y") {
                        if (isset($input["selector"][$i]) && !empty($input["selector"][$i])) {
                            $fieldOperator = "";
                            $fieldValue = "";
                            if ($input["selector"][$i] == "amount") {
                                $fieldOperator = "amountoperator_" . $index;
                                $fieldValue = "amount_" . $index;
                                $str .= $input["selector"][$i] . " " . $input[$fieldOperator] . " " . $input[$fieldValue] . " AND ";
                                $qryStr .= $input["selector"][$i] . " ";
                                if ($input[$fieldOperator] == "=") {
                                    $qryStr .= "==";
                                } else {
                                    $qryStr .= $input[$fieldOperator];
                                }
                                $qryStr .= " " . $input[$fieldValue] . " && ";
                            } else {
                                if ($input["selector"][$i] == "currency") {
                                    $fieldOperator = "currencyoperator_" . $index;
                                    $fieldValue = "currency_" . $index;
                                }
                                if ($input["selector"][$i] == "category") {
                                    $fieldOperator = "categoryoperator_" . $index;
                                    $fieldValue = "category_" . $index;
                                }
                                if ($input["selector"][$i] == "country") {
                                    $fieldOperator = "countryoperator_" . $index;
                                    $fieldValue = "country_" . $index;
                                }
                                if ($input["selector"][$i] == "bin_cou_code") {
                                    $fieldOperator = "bincountryoperator_" . $index;
                                    $fieldValue = "bincountry_" . $index;
                                }
                                if ($input["selector"][$i] == "card_type") {
                                    $fieldOperator = "cardtypeoperator_" . $index;
                                    $fieldValue = "cardtype_" . $index;
                                }
                                if ($input["selector"][$i] == "card_wl") {
                                    $fieldOperator = "cardwloperator_" . $index;
                                    $fieldValue = "cardwl_" . $index;
                                }
                                if ($input["selector"][$i] == "bin_number") {
                                    $fieldOperator = "binnumberoperator_" . $index;
                                    $fieldValue = "binnumber_" . $index;
                                }
                                if ($input["selector"][$i] == "user") {
                                    $fieldOperator = "useroperator_" . $index;
                                    $fieldValue = "user_" . $index;
                                }
                                $str .= $input["selector"][$i] . " " . $input[$fieldOperator] . " ";
                                //$qryStr .= $input["selector"][$i]." ";
                                if ($input[$fieldOperator] == "=") {
                                    if ($input["selector"][$i] == "bin_number") {
                                        $str .= "'" . $input[$fieldValue] . "'";
                                        $qryStr .= $input["selector"][$i] . " == " . "'" . $input[$fieldValue] . "'";
                                    }else{
                                        $str .= "'" . $input[$fieldValue]["0"] . "'";
                                        $qryStr .= $input["selector"][$i] . " == " . "'" . $input[$fieldValue]["0"] . "'";
                                    }
                                } elseif ($input[$fieldOperator] == "In") {
                                    if ($input["selector"][$i] == "bin_number") {
                                        $strBin = explode(',', $input[$fieldValue]);
                                        $str .= json_encode($strBin);
                                        $qryStr .= "in_array(" . $input["selector"][$i] . ", " . json_encode($strBin) . ")";
                                    }else{
                                        $str .= json_encode($input[$fieldValue]);
                                        $qryStr .= "in_array(" . $input["selector"][$i] . ", " . json_encode($input[$fieldValue]) . ")";
                                    }
                                } elseif ($input[$fieldOperator] == "NotIn") {
                                    if ($input["selector"][$i] == "bin_number") {
                                        $strBin = explode(',', $input[$fieldValue]);
                                        $str .= json_encode($strBin);
                                        $qryStr .= "!in_array(" . $input["selector"][$i] . ", " . json_encode($strBin) . ")";
                                    }else{
                                        $str .= json_encode($input[$fieldValue]);
                                        $qryStr .= "!in_array(" . $input["selector"][$i] . ", " . json_encode($input[$fieldValue]) . ")";
                                    }
                                }
                                // else{
                                //     $str .= json_encode($input[$fieldValue]);
                                //     $qryStr .= json_encode($input[$fieldValue]);
                                // }
                                $str .= " AND ";
                                $qryStr .= " && ";
                            }
                        }
                    }
                }
            }
            // dump($qryStr);
            // dd($str);
            $rules->rule_condition = rtrim($qryStr, " && ");
            $rules->rule_condition_view = rtrim($str, " AND ");
            if ($rules->save()) {
                // Rules::where("id", $rules->id)->update(["priority" => $rules->id]);
                DB::commit();
                Session::put('success', 'Rule has been updated successfully.');
                return redirect()->route('admin.create_rules.list', $rules->rules_type);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::put('error', 'Something went wrong.Try Again.');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!in_array($id, $this->ExcludeRules)) {
            try {
                Rules::where("id", $id)->delete();
                notificationMsg('success', 'Rule deleted Successfully!');
                return redirect()->route('admin.create_rules.index');
            } catch (Exception $e) {
                return response()->json(['success' => false]);
            }
        } else {
            notificationMsg('error', 'You are not able to delete the Rule!');
            return redirect()->route('admin.create_rules.index');
        }
    }

    public function listDataTable(Request $request)
    {
        $data = Rules::select("rules.id", "rules.rules_name", "rules.assign_mid", "middetails.bank_name", "rules.status", "rules.rule_condition_view", "rules.priority")->leftjoin('middetails', function ($join) {
            $join->on('middetails.id', '=', 'rules.assign_mid');
        })->whereNull('rules.user_id')->where("rules.rules_type", $request->data_type)->get();
        return \Yajra\DataTables\DataTables::of($data)
            ->addColumn('checkbox', function ($data) {
                $str = '<div class="custom-control form-check custom-checkbox custom-control-inline mr-0"><input type="checkbox" class="form-check-input multidelete" name="multicheckmail[]" id="customCheckBox_' . $data->id . '"
                    value="' . $data->id . '" required="">
                    <label class="form-check-label" for="customCheckBox_' . $data->id . '"></label></div>';
                return $str;
            })
            ->addColumn('action', function ($data) {
                $str = '<div class="dropdown">';
                $str .= '<button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                        data-bs-toggle="dropdown">
                        <svg width="5" height="17" viewBox="0 0 5 17" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2.36328 4.69507C1.25871 4.69507 0.363281 3.79964 0.363281 2.69507C0.363281 1.5905 1.25871 0.695068 2.36328 0.695068C3.46785 0.695068 4.36328 1.5905 4.36328 2.69507C4.36328 3.79964 3.46785 4.69507 2.36328 4.69507Z"
                                fill="#B3ADAD" />
                            <path
                                d="M2.36328 10.6951C1.25871 10.6951 0.363281 9.79964 0.363281 8.69507C0.363281 7.5905 1.25871 6.69507 2.36328 6.69507C3.46785 6.69507 4.36328 7.5905 4.36328 8.69507C4.36328 9.79964 3.46785 10.6951 2.36328 10.6951Z"
                                fill="#B3ADAD" />
                            <path
                                d="M2.36328 16.6951C1.25871 16.6951 0.363281 15.7996 0.363281 14.6951C0.363281 13.5905 1.25871 12.6951 2.36328 12.6951C3.46785 12.6951 4.36328 13.5905 4.36328 14.6951C4.36328 15.7996 3.46785 16.6951 2.36328 16.6951Z"
                                fill="#B3ADAD" />
                        </svg>
                    </button>';
                $str .= '<div class="dropdown-menu dropdown-menu-end">';
                if (auth()->guard('admin')->user()->can(['update-rule'])) {
                    if ($data->status == 0) {
                        $str .= '<a href="' . route('rules-status', [$data->id, 'status' => 1]) . '" class="dropdown-item">Active</a>';
                    } else {
                        $str .= '<a href="' . route('rules-status', [$data->id, 'status' => 0]) . '" class="dropdown-item">Inactive</a>';
                    }
                }
                if (auth()->guard('admin')->user()->can(['delete-rule'])) {
                    $str .= '<a href="javascript:void(0)" data-id="' . $data->id . '" data-url="' . route('admin.create_rules.destroy', $data->id) . '" class="dropdown-item  delete_modal">Delete</a>';
                    $str .= '<a href=' . route('admin.create_rules.edit', [$data->id]) . ' target="_blank"  data-id="' . $data->id . '"class="dropdown-item">Edit</a>';
                }

                if (auth()->guard('admin')->user()->can(['assign-to-mid-rule'])) {
                    $str .= '<a href="javascript:;" data-bs-target="#assignMIDModal" onclick="fnAssignMID(' . $data->id . ',' . $data->assign_mid . ')" data-id="' . $data->id . '" data-value="' . $data->mid . '" data-bs-toggle="modal" class="dropdown-item">Assign MID</a>';
                }

                $str .= '</div>';
                $str .= '</div>';
                return $str;
            })
            ->addColumn("status", function ($data) {
                $str = "";
                if ($data->status == 0) {
                    $str .= '<span class="badge badge-warning badge-sm">Inactive</span>';
                } else {
                    $str .= '<span class="badge badge-success badge-sm">Active</span>';
                }
                return $str;
            })
            ->setRowAttr([
                'data-id' => function ($data) {
                    return $data->id;
                },
            ])
            // ->setRowAttr([
            //     'data-value' => function($data) {
            //         return $data->assign_mid;
            //     },
            // ])
            ->rawColumns(["checkbox", "action", "status"])
            ->make(true);
    }

    public function sortRules(request $request)
    {
        try {
            $rules = Rules::all();
            foreach ($rules as $task) {
                $id = $task->id;
                foreach ($request->order as $key => $order) {
                    Rules::where("id", $order["id"])->update(['priority' => $order['position']]);
                }
            }
            return response()->json([
                'success' => true,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function merchantRules()
    {
        $payment_gateway_id = DB::table('middetails')->get();
        $CardRules = Rules::where("rules_type", "Card")->whereNotNull("rules.user_id")->whereNull("rules.deleted_at")->count();
        $CryptoRules = Rules::where("rules_type", "Crypto")->whereNotNull("rules.user_id")->whereNull("rules.deleted_at")->count();
        $BankRules = Rules::where("rules_type", "Bank")->whereNotNull("rules.user_id")->whereNull("rules.deleted_at")->count();
        $upiRules = Rules::where("rules_type", "UPI")->whereNotNull("rules.user_id")->whereNull("rules.deleted_at")->count();
        return view('admin.merchantRules.index', compact('payment_gateway_id', 'CardRules', 'CryptoRules', 'BankRules', 'upiRules'));
    }

    public function merchantList($type)
    {
        if (empty($type)) {
            return redirect()->route("admin.merchant_rules.index");
        }
        $rules = Rules::select("rules.id", "rules.rules_name", "rules.assign_mid", "middetails.bank_name", "rules.status", "rules.rule_condition_view", "users.email", "users.name")
            ->leftjoin('middetails', function ($join) {
                $join->on('middetails.id', '=', 'rules.assign_mid');
            })
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'rules.user_id');
            })
            ->where("rules.rules_type", $type)
            ->whereNotNull('rules.user_id')
            ->whereNull("rules.deleted_at")
            ->get();
        if ($type == "Card") {
            $mid_type = 1;
        } elseif ($type == "Crypto") {
            $mid_type = 3;
        } elseif ($type == "Bank") {
            $mid_type = 2;
        } elseif ($type == "UPI") {
            $mid_type = 4;
        }
        $payment_gateway_id = \DB::table('middetails')->where(["mid_type" => $mid_type])->get();
        return view('admin.merchantRules.list', compact('rules', 'payment_gateway_id', 'type'));
    }

    public function listMerchantDataTable(Request $request)
    {
        $data = Rules::select("rules.id", "rules.rules_name", "rules.user_id", "rules.rules_type", "rules.assign_mid", "middetails.bank_name", "rules.status", "rules.rule_condition_view", "rules.priority", "users.email", "users.name", "applications.business_name")
            ->leftjoin('middetails', function ($join) {
                $join->on('middetails.id', '=', 'rules.assign_mid');
            })
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'rules.user_id');
            })
            ->leftjoin('applications', function ($join) {
                $join->on("users.id", "=", "applications.user_id");
            })
            ->whereNotNull('rules.user_id')
            ->where("rules.rules_type", $request->data_type)
            ->get();
        return \Yajra\DataTables\DataTables::of($data)
            ->addColumn('action', function ($data) {

                $str = '<div class="dropdown">';
                $str .= '<button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                            data-bs-toggle="dropdown">
                            <svg width="5" height="17" viewBox="0 0 5 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M2.36328 4.69507C1.25871 4.69507 0.363281 3.79964 0.363281 2.69507C0.363281 1.5905 1.25871 0.695068 2.36328 0.695068C3.46785 0.695068 4.36328 1.5905 4.36328 2.69507C4.36328 3.79964 3.46785 4.69507 2.36328 4.69507Z"
                                    fill="#B3ADAD" />
                                <path
                                    d="M2.36328 10.6951C1.25871 10.6951 0.363281 9.79964 0.363281 8.69507C0.363281 7.5905 1.25871 6.69507 2.36328 6.69507C3.46785 6.69507 4.36328 7.5905 4.36328 8.69507C4.36328 9.79964 3.46785 10.6951 2.36328 10.6951Z"
                                    fill="#B3ADAD" />
                                <path
                                    d="M2.36328 16.6951C1.25871 16.6951 0.363281 15.7996 0.363281 14.6951C0.363281 13.5905 1.25871 12.6951 2.36328 12.6951C3.46785 12.6951 4.36328 13.5905 4.36328 14.6951C4.36328 15.7996 3.46785 16.6951 2.36328 16.6951Z"
                                    fill="#B3ADAD" />
                            </svg>
                        </button>';
                $str .= '<div class="dropdown-menu dropdown-menu-end">';
                if (auth()->guard('admin')->user()->can(['merchant-rule-update'])) {
                    if ($data->status == 0) {
                        $str .= '<a href="' . route('rules-merchant-status', [$data->id, 'status' => 1]) . '" class="dropdown-item">Active</a>';
                    } else {
                        $str .= '<a href="' . route('rules-merchant-status', [$data->id, 'status' => 0]) . '" class="dropdown-item">Inactive</a>';
                    }
                }
                if (auth()->guard('admin')->user()->can(['merchant-rule-delete'])) {
                    $str .= '<a href="javascript:void(0)" data-id="' . $data->id . '" class="dropdown-item deleteMID">Delete</a>';
                    $str .= '<a href=' . route("merchant.edit_rules", ["userId" => $data->user_id, "type" => $data->rules_type, "id" => $data->id]) . ' data-id="' . $data->id . '" class="dropdown-item">Edit</a>';
                }

                if (auth()->guard('admin')->user()->can(['merchant-rule-assign-to-mid'])) {
                    $str .= '<a href="javascript:;" data-bs-target="#assignMIDModal" onclick="fnAssignMID(' . $data->id . ',' . $data->assign_mid . ')" data-id="' . $data->id . '" data-value="' . $data->mid . '" data-bs-toggle="modal" class="dropdown-item">Assign MID</a>';
                }

                $str .= '</div>';
                $str .= '</div>';
                return $str;
            })
            ->addColumn("status", function ($data) {
                $str = "";
                if ($data->status == 0) {
                    $str .= '<span class="badge badge-warning badge-sm">Inactive</span>';
                } else {
                    $str .= '<span class="badge badge-success badge-sm">Active</span>';
                }
                return $str;
            })
            ->setRowAttr([
                'data-id' => function ($data) {
                    return $data->id;
                },
            ])
            // ->setRowAttr([
            //     'data-value' => function($data) {
            //         return $data->assign_mid;
            //     },
            // ])
            ->rawColumns(["action", "status"])
            ->make(true);
    }

    public function changeMerchantStatus(Request $request, $id)
    {
        $status = $request->get('status');
        $update = ['status' => $status];
        Rules::where("id", $id)->update($update);
        if ($status == '1') {
            notificationMsg('success', 'Rule activated successfully');
        } else {
            notificationMsg('success', 'Rule deactivated successfully ');
        }
        return redirect()->back();
    }
}