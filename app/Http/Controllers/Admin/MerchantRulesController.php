<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Categories;
use App\Rules;
use App\RulesList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Arr;
use Exception;

class MerchantRulesController extends Controller
{

    protected $Categories;

    public function __construct()
    {
        $this->Categories = new Categories;
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
        $CardRules = Rules::where("rules_type", "Card")->whereNull("rules.deleted_at")->count();
        $CryptoRules = Rules::where("rules_type", "Crypto")->whereNull("rules.deleted_at")->count();
        $BankRules = Rules::where("rules_type", "Bank")->whereNull("rules.deleted_at")->count();
        $upiRules = Rules::where("rules_type", "UPI")->whereNull("rules.deleted_at")->count();
        return view('admin.createRules.index', compact('rules', 'payment_gateway_id', 'CardRules', 'CryptoRules', 'BankRules'));
    }

    public function list($id, $type)
    {
        if (empty($type)) {
            return redirect()->route("merchant-rules", $id);
        }
        $rules = Rules::select("rules.id", "rules.rules_name", "rules.assign_mid", "middetails.bank_name", "rules.status", "rules.rule_condition_view")->leftjoin('middetails', function ($join) {
            $join->on('middetails.id', '=', 'rules.assign_mid');
        })->where("rules.rules_type", $type)->where('rules.user_id', $id)->whereNull("rules.deleted_at")->get();
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
        return view('admin.userManagement.createRules.list', compact('rules', 'payment_gateway_id', 'type', 'id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id, $type)
    {
        $categories = $this->Categories->getData();
        if (empty($type)) {
            return redirect()->route("merchant-rules", $id);
        }
        return view('admin.userManagement.createRules.create', compact('categories', 'type', "id"));
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
            'type' => 'required'
        ]);
        $input = \Arr::except($request->all(), ['_token']);
        DB::beginTransaction();
        try {
            $is_success = false;
            $rules = new Rules();
            $rules->rules_name = $input["title"];
            $rules->rules_type = $input["type"];
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
                                if ($input["selector"][$i] == "card_type") {
                                    $fieldOperator = "cardtypeoperator_" . $index;
                                    $fieldValue = "cardtype_" . $index;
                                }
                                if ($input["selector"][$i] == "card_wl") {
                                    $fieldOperator = "cardwloperator_" . $index;
                                    $fieldValue = "cardwl_" . $index;
                                }
                                $str .= $input["selector"][$i] . " " . $input[$fieldOperator] . " ";
                                //$qryStr .= $input["selector"][$i]." ";
                                if ($input[$fieldOperator] == "=") {
                                    $str .= "'" . $input[$fieldValue]["0"] . "'";
                                    $qryStr .= $input["selector"][$i] . " == " . "'" . $input[$fieldValue]["0"] . "'";
                                } elseif ($input[$fieldOperator] == "In") {
                                    $str .= json_encode($input[$fieldValue]);
                                    $qryStr .= "in_array(" . $input["selector"][$i] . ", " . json_encode($input[$fieldValue]) . ")";
                                } elseif ($input[$fieldOperator] == "NotIn") {
                                    $str .= json_encode($input[$fieldValue]);
                                    $qryStr .= "!in_array(" . $input["selector"][$i] . ", " . json_encode($input[$fieldValue]) . ")";
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
            $rules->user_id = $request->id;
            $rules->rule_condition = rtrim($qryStr, " && ");
            $rules->rule_condition_view = rtrim($str, " AND ");
            if ($rules->save()) {
                Rules::where("id", $rules->id)->update(["priority" => $rules->id]);
                DB::commit();
                Session::put('success', 'Rule has been created successfully.');
                return redirect()->route('merchant-rules', $request->id);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::put('error', 'Something went wrong.Try Again.');
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
        $input = \Arr::except($request->all(), array('_token', '_method'));
        try {
            Rules::where("id", $request->get('id'))->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }
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
    public function edit($userId, $type, $id)
    {
        $categories = $this->Categories->getData();
        $rule = Rules::find($id);
        $conditions = explode('AND', $rule->rule_condition_view);
        if ($rule) {
            return view('admin.userManagement.createRules.edit', compact('rule', 'categories', 'conditions'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'type' => 'required'
        ]);
        $input = Arr::except($request->all(), ['_token']);
        // dd($input);
        DB::beginTransaction();
        try {
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
                                $str .= $input["selector"][$i] . " " . $input[$fieldOperator] . " ";
                                //$qryStr .= $input["selector"][$i]." ";
                                if ($input[$fieldOperator] == "=") {
                                    $str .= "'" . $input[$fieldValue]["0"] . "'";
                                    $qryStr .= $input["selector"][$i] . " == " . "'" . $input[$fieldValue]["0"] . "'";
                                } elseif ($input[$fieldOperator] == "In") {
                                    $str .= json_encode($input[$fieldValue]);
                                    $qryStr .= "in_array(" . $input["selector"][$i] . ", " . json_encode($input[$fieldValue]) . ")";
                                } elseif ($input[$fieldOperator] == "NotIn") {
                                    $str .= json_encode($input[$fieldValue]);
                                    $qryStr .= "!in_array(" . $input["selector"][$i] . ", " . json_encode($input[$fieldValue]) . ")";
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
            $rules->user_id = $userId;
            $rules->rule_condition = rtrim($qryStr, " && ");
            $rules->rule_condition_view = rtrim($str, " AND ");
            if ($rules->save()) {
                // Rules::where("id", $rules->id)->update(["priority" => $rules->id]);
                DB::commit();
                Session::put('success', 'Rule has been updated successfully.');
                return redirect()->back();
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
        try {
            Rules::where("id", $id)->delete();
            notificationMsg('success', 'Rule deleted Successfully!');
            return redirect()->route('admin.create_rules.index');
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    public function listDataTable(Request $request)
    {
        $data = Rules::select("rules.id", "rules.rules_name", "rules.user_id", "rules.rules_type", "rules.assign_mid", "middetails.bank_name", "rules.status", "rules.rule_condition_view", "rules.priority")->leftjoin('middetails', function ($join) {
            $join->on('middetails.id', '=', 'rules.assign_mid');
        })->where('rules.user_id', $request->id)->where("rules.rules_type", $request->data_type)->get();
        return \Yajra\DataTables\DataTables::of($data)
            ->addColumn('action', function ($data) {
                $str = ' <div class="dropdown ">';
                $str .= '  <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
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
                $str .= '<ul class="dropdown-menu dropdown-menu-right">';
                if (auth()->guard('admin')->user()->can(['update-rule'])) {
                    if ($data->status == 0) {
                        $str .= '<li class="dropdown-item">';
                        $str .= '<a href="' . route('rules-status', [$data->id, 'status' => 1]) . '" class="dropdown-a"> Active</a>';
                        $str .= '</li>';
                    } else {
                        $str .= '<li class="dropdown-item">';
                        $str .= '<a href="' . route('rules-status', [$data->id, 'status' => 0]) . '" class="dropdown-a">Inactive</a>';
                        $str .= '</li>';
                    }
                }
                if (auth()->guard('admin')->user()->can(['delete-rule'])) {
                    $str .= '<li class="dropdown-item">';
                    $str .= '<a href="javascript:void(0)" data-id="' . $data->id . '" data-url="' . route('admin.create_rules.destroy', $data->id) . '" class="dropdown-a  deleteMID"> Delete</a>';
                    $str .= '</li>';
                }
                $str .= '<a href=' . route("merchant.edit_rules", ["userId" => $data->user_id, "type" => $data->rules_type, "id" => $data->id]) . ' data-id="' . $data->id . '" class="dropdown-item">Edit</a>';

                $str .= '<a href="javascript:;" data-bs-target="#assignMIDModal" onclick="fnAssignMID(' . $data->id . ',' . $data->assign_mid . ')" data-id="' . $data->id . '" data-value="' . $data->mid . '" data-bs-toggle="modal" class="dropdown-item">Assign MID</a>';

                $str .= '</ul>';
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
}