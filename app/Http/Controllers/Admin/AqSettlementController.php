<?php

namespace App\Http\Controllers\Admin;

use App\AQSettlement;
use App\Exports\AqSettlementExport;
use App\Http\Controllers\Controller;
use App\MIDDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Storage;
use Log;
use Arr;
use Maatwebsite\Excel\Facades\Excel;

class AqSettlementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $aqSettlement;

    public function __construct()
    {
        $this->aqSettlement = new AQSettlement;
    }

    public function index(Request $request)
    {
        $mids = MIDDetail::select("id", "bank_name")->where("is_active", "1")->get();
        $input = Arr::except($request->all(), array('_token', '_method'));
        $settlements = $this->aqSettlement->advacnedSearch($input)->paginate(15);
        return view("admin.aq_settlement.index", compact('mids', 'settlements'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mids = MIDDetail::select("id", "bank_name")->where("is_active", "1")->get();
        return view("admin.aq_settlement.create", compact('mids'));
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
            "middetail_id" => "required",
            "from_date" => "required",
            "to_date" => "required",
            "txn_hash" => "required",
            "paid_date" => "nullable",
            "payment_receipt" => "required|file|max:5024|mimes:png,jpg,svg,zip,pdf,jpeg,webp",
        ]);

        try {
            // * Upload file first
            $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageDocument = $imageDocument . '.' . $payload["payment_receipt"]->getClientOriginalExtension();
            $filePath = 'uploads/aq-settlement' . '/' . $imageDocument;
            Storage::disk('s3')->put($filePath, file_get_contents($payload["payment_receipt"]->getRealPath()));

            $payload["payment_receipt"] = $filePath;
            $payload["from_date"] = Carbon::parse($payload["from_date"])->toDateTimeString();
            $payload["to_date"] = Carbon::parse($payload["to_date"])->toDateTimeString();
            $payload["paid_date"] = isset($payload["paid_date"]) ? Carbon::parse($payload["paid_date"])->toDateTimeString() : null;

            AQSettlement::create($payload);
            return redirect()->route("aq-settlement.index")->with("success", "Settlement added successfully!");
        } catch (\Exception $err) {
            Log::info("settlement_create_err =>" . $err->getMessage());
            return back()->with("error", "Something went wrong.Please try again!");
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
        $mids = MIDDetail::select("id", "bank_name")->where("is_active", "1")->get();
        $settlement = AQSettlement::find($id);
        return view("admin.aq_settlement.edit", compact('mids', "settlement"));
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
            "middetail_id" => "required",
            "from_date" => "required",
            "to_date" => "required",
            "txn_hash" => "required",
            "paid_date" => "nullable",
            "payment_receipt" => "nullable|file|max:5024|mimes:png,jpg,svg,zip,pdf,jpeg,webp",
        ]);

        try {
            // * Upload file first
            if (isset($payload["payment_receipt"])) {

                // * Delete old image
                $settlement = AQSettlement::find($id);
                Storage::disk("s3")->delete($settlement->payment_receipt);

                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $payload["payment_receipt"]->getClientOriginalExtension();
                $filePath = 'uploads/aq-settlement' . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($payload["payment_receipt"]->getRealPath()));
                $payload["payment_receipt"] = $filePath;
            }

            $payload["from_date"] = Carbon::parse($payload["from_date"])->toDateTimeString();
            $payload["to_date"] = Carbon::parse($payload["to_date"])->toDateTimeString();
            $payload["paid_date"] = isset($payload["paid_date"]) ? Carbon::parse($payload["paid_date"])->toDateTimeString() : null;

            AQSettlement::where("id", $id)->update($payload);
            return redirect()->route("aq-settlement.index")->with("success", "Settlement updated successfully!");
        } catch (\Exception $err) {
            Log::info("settlement_update_err =>" . $err->getMessage());
            return back()->with("error", "Something went wrong.Please try again!");
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
            $settlement = AQSettlement::find($id);
            // * Delete old file
            if (isset($settlement->payment_receipt)) {
                Storage::disk("s3")->delete($settlement->payment_receipt);
            }
            $settlement->delete();
            return back()->with("success", "Settlement Report deleted successfully!");
        } catch (\Exception $err) {
            Log::info("aq_settlement_delete_err =>" . $err->getMessage());
            return back()->with("error", "Something went wrong.Please try again!");
        }


    }

    public function exportExcel()
    {
        return Excel::download(new AqSettlementExport, 'aq_settlement.csv');
    }
}
