<?php

namespace App\Http\Controllers\Admin;

use DB;
use Log;
use Hash;
use Validator;
use App\Gateway;
use App\Bank;
use App\MIDDetail;
use App\ImageUpload;
use App\Http\Requests;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class MIDDetailsController extends AdminController
{

    protected $MIDDetail, $moduleTitleS, $moduleTitleP;
    public function __construct()
    {
        parent::__construct();
        $this->MIDDetail = new MIDDetail;

        $this->moduleTitleS = 'MID Details';
        $this->moduleTitleP = 'admin.middetails';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function index(Request $request)
    {
        return view($this->moduleTitleP . '.index');
    }

    public function getMIDData(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $data = $this->MIDDetail->getData();

        return \DataTables::of($data)
            ->addColumn('Actions', function ($data) {
                if (auth()->guard('admin')->user()->can(['update-mid', 'list-mid'])) {
                    if (auth()->guard('admin')->user()->can(['update-mid'])) {
                        $action = '<li><a
                                href="' . route('admin.middetails.edit', $data->id) . '"
                                class="dropdown-item">Edit</a></li>';
                    } else {
                        $action = '';
                    }

                    if (auth()->guard('admin')->user()->can(['list-mid'])) {
                        $action1 = '
                            <li><a href="' . route('admin.middetails.show', $data->id) . '"
                                class="dropdown-item">Show
                            </a>
                        </li>';
                    } else {
                        $action1 = '';
                    }

                    return '<div class="dropdown">
                                <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink2"></a><ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                    ' . $action . ' ' . $action1 . '
                                </div>
                        </div>';
                } else {
                    return '---';
                }
            })
            ->addColumn('mid_type', function ($data) {
                switch ($data->mid_type) {
                    case (1):
                        return 'Card';
                    case (2):
                        return 'Bank';
                    case (3):
                        return 'Crypto';
                    case (4):
                        return 'UPI';
                    case (5):
                        return 'APM <span class="badge badge-primary">' . getAPMType($data->apm_type) . '</span>';
                    default;
                }
            })
            ->addIndexColumn()
            ->rawColumns(['Actions', 'mid_type'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $bank = Bank::pluck('bank_name', 'id')->toArray();
        $countries = getCountry();
        $industries = DB::table('categories')->select("id", "name")->get();
        $gateways = Gateway::pluck('title', 'id')->toArray();

        return view($this->moduleTitleP . '.create', compact('gateways', 'countries', 'bank', 'industries'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'bank_name' => 'required',
            'is_gateway_mid' => 'required',
            'mid_type' => 'required',
            'main_gateway_mid_id' => 'required_if:is_gateway_mid,==,1',
            'assign_gateway_mid' => 'required_if:is_gateway_mid,==,1',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        $input["apm_mdr"] = $input["apm_mdr"] ?? 0.00;
        $input['mid_no'] = rand(11111111, 99999999);

        $main_gateway = \DB::table('main_gateway')
            ->where('id', $input['main_gateway_mid_id'])
            ->first();

        // store gateway_table name in middetails table
        $input['gateway_table'] = 'gateway_' . \Str::slug($main_gateway->title, '_');

        $input['blocked_country'] = !empty($request->blocked_country) ? json_encode($request->blocked_country) : NULL;
        $input["accepted_industries"] = !empty($request->accepted_industries) ? json_encode($request->accepted_industries) : null;

        // dd($input);
        $this->MIDDetail->storeData($input);

        notificationMsg('success', 'MID Feature Created Successfully!');

        return redirect()->route('mid-feature-management.index');
    }

    public function show($id)
    {
        $data = $this->MIDDetail->findData($id);

        $gateway = Gateway::find($data->main_gateway_mid_id);

        $countries = getCountry();
        if ($data->blocked_country == null || $data->blocked_country == '' || $data->blocked_country == 'null') {
            $blocked_country = [];
        } else {
            $blocked_country = json_decode($data->blocked_country);
        }

        foreach ($countries as $key => $value) {
            if (in_array($key, $blocked_country)) {
                $country[] = $value;
            }
        }

        $blocked_country = !empty($country) ? implode(", ", $country) : '';

        // if no main gateway found or deleted then
        if ($gateway != null) {

            if ($data->is_gateway_mid == '1') {
                $subgateways = \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->pluck('name', 'id')->toArray();
            } else {
                $subgateways = [];
            }
        } else {
            $subgateways = [];
        }

        $subgateways = !empty($subgateways) ? $subgateways[1] : '';

        return view($this->moduleTitleP . '.show', compact('data', 'subgateways', 'gateway', 'blocked_country'));
    }

    public function edit($id)
    {
        // dd($id);
        $gateways = Gateway::pluck('title', 'id')->toArray();
        $data = $this->MIDDetail->findData($id);
        $bank = Bank::pluck('bank_name', 'id')->toArray();
        $gateway = Gateway::find($data->main_gateway_mid_id);
        $industries = DB::table('categories')->select("id", "name")->get();

        // if no main gateway found or deleted then
        if ($gateway != null) {
            if ($data->is_gateway_mid == '1') {
                $subgateways = \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->pluck('name', 'id')->toArray();
            } else {
                $subgateways = [];
            }
        } else {
            $subgateways = [];
        }

        return view($this->moduleTitleP . '.edit', compact('bank', 'data', 'gateways', 'subgateways', 'industries'));
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'bank_name' => 'required',
            'is_gateway_mid' => 'required|in:1',
            // 'mid_type' => 'required',
            'main_gateway_mid_id' => 'required_if:is_gateway_mid,==,1',
            // 'assign_gateway_mid' => 'required_if:is_gateway_mid,==,1',
        ]);

        try {
            $input = \Arr::except($request->all(), array('_token', '_method'));

            $main_gateway_title = DB::table('main_gateway')
                ->where('id', $input['main_gateway_mid_id'])
                ->value('title');

            $input['gateway_table'] = 'gateway_' . \Str::slug($main_gateway_title, '_');

            $input['blocked_country'] = !empty($request->blocked_country) ? json_encode($request->blocked_country) : NULL;
            $input["accepted_industries"] = !empty($request->accepted_industries) ? json_encode($request->accepted_industries) : null;

            // * Unset non update variables
            unset($input['main_gateway_mid_id']);
            unset($input['mid_type']);
            unset($input['assign_gateway_mid']);
            unset($input['is_gateway_mid']);
            $this->MIDDetail->updateData($id, $input);

            notificationMsg('success', 'MID Feature Updated Successfully!');

            return redirect()->route('mid-feature-management.index');
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. please try again.');
        }


    }

    public function destroy($id)
    {
        $this->MIDDetail->destroyData($id);

        notificationMsg('success', 'MID Deleted Successfully!');

        return redirect()->route('mid-feature-management.index');
    }

    public function getSubMID(Request $request)
    {
        $id = $request->get('id');

        $gateway = Gateway::find($id);

        $subgateways = \DB::table('gateway_' . \Str::slug($gateway->title, '_'))->get();

        $htmlData = '<option selected disabled> -- Assign Gateway MID -- </option>';

        if ($subgateways) {
            foreach ($subgateways as $key => $value) {
                $htmlData .= '<option value="' . $value->id . '">' . $value->name . '</option>';
            }
        }

        return response()->json([
            'html' => $htmlData,
        ]);
    }

}