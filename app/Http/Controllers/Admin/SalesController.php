<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Sales;
use App\User;
use Illuminate\Http\Request;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Arr;

class SalesController extends Controller
{

    protected $sales;

    public function __construct()
    {
        $this->sales = new Sales;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = Arr::except($request->all(), array('_token', '_method'));
        $sales = $this->sales->advancedSearch($input)->paginate(15);
        return view("admin.sales.index", compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = config('country');
        return view("admin.sales.create", compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            "name" => "required|min:2|max:60",
            "email" => "required|email|unique:sales,email",
            "country_code" => "required"
        ]);
        try {
            $payload["rm_code"] = Str::random(34);
            Sales::create($payload);
            return redirect()->route("sales.index")->with("success", "RM created successfully!");
        } catch (\Exception $err) {
            Log::info("RM-store-err =>" . $err->getMessage());
            return back()->with("error", "Something went wrong.please try again.");
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = User::select("id", "name", "mobile_no", "email")->with('customApplication')->where("sales_id", $id)->get();
        return view('admin.sales.show', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sale = Sales::find($id);
        $countries = config('country');
        return view('admin.sales.edit', compact('sale', "countries"));
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
        $payload = $request->validate([
            "name" => "required|min:2|max:60",
            "email" => "required|email|unique:sales,email," . $id,
            "country_code" => "required"
        ]);

        try {
            Sales::where("id", $id)->update($payload);
            return redirect()->route("sales.index")->with("success", "RM updated successfully!");
        } catch (\Exception $err) {
            Log::info("RM-update-err =>" . $err->getMessage());
            return back()->with("error", "Something went wrong.please try again.");
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
            Sales::find($id)->delete();
            return redirect()->route("sales.index")->with("success", "RM deleted successfully!");
        } catch (\Exception $err) {
            Log::info("RM-delete-err =>" . $err->getMessage());
            return back()->with("error", "Something went wrong.please try again.");
        }

    }

    // * Change the update status
    public function changeStatus(Request $request)
    {
        $payload = $request->only(["status", "id"]);
        try {
            Sales::where("id", $payload["id"])->update(["status" => $payload["status"]]);
            return redirect()->route("sales.index")->with("success", "RM status updated successfully!");
        } catch (\Exception $err) {
            Log::info("RM-create-err =>" . $err->getMessage());
            return back()->with("error", "Something went wrong.please try again.");
        }

    }

    // * Export Excel
    public function exportExcel()
    {
        return Excel::download(new SalesExport, 'sales.csv');
    }

    // * Remove RM from merchant
    public function removeRmFromMerchant(Request $request)
    {
        $payload = $request->validate(["user_id" => "required"]);
        try {
            User::where("id", $payload["user_id"])->update(["sales_id" => null]);
            return back()->with("success", "Merchant removed from RM successfully!");
        } catch (\Exception $err) {
            Log::info("RM-remove-err =>" . $err->getMessage());
            return back()->with("error", "Something went wrong.please try again.");
        }

    }
}
