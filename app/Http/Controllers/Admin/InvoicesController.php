<?php

namespace App\Http\Controllers\Admin;

use App\Invoices;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Application;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{

    protected $application, $invoice, $moduleTitleP;

    public function __construct()
    {

        $this->application = new Application;
        $this->invoice = new Invoices;

        $this->moduleTitleP = 'admin.invoices';

        view()->share('moduleTitleP', $this->moduleTitleP);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $invoices = $this->invoice->getData($input, $noList);
        $companyName = Application::select('id', 'business_name')->get();
        return view($this->moduleTitleP . '.index', compact('invoices', 'companyName'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companyName = Application::select('id', 'business_name')->get();
        return view($this->moduleTitleP . '.create', compact('companyName'));
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
            'company_id' => 'required',
            'business_name' => 'required',
            'agent_name' => 'required',
            'email' => 'required',
            'company_address' => 'required',
            'phone_no' => 'required',
            'amount_deducted_value' => 'required',
            'description.0.amount' => 'required',
            'description.1.amount' => 'required'
        ], [
            'company_id.required' => 'This field is required.',
            'business_name.required' => 'This field is required.',
            'agent_name.required' => 'This field is required.',
            'email.required' => 'This field is required.',
            'company_address.required' => 'This field is required.',
            'phone_no.required' => 'This field is required.',
            'amount_deducted_value.required' => 'This field is required.',
            'description.0.amount.required' => 'This field is required.',
            'description.1.amount.required' => 'This field is required.'
        ]);
        $input = $request->all();
        $input['logopath'] = storage_asset('NewTheme/images/logo.png');
        $invoice_no = time() . rand(1111, 9999);
        $input["invoice_no"] = $invoice_no;
        $input['usdt_erc'] = config('app.usdt_erc');
        $input['usdt_trc'] = config('app.usdt_trc');
        $input['btc'] = config('app.btc');
        view()->share('input', $input);
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.invoices.invoice_PDF'));
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');
        $dompdf->render();
        $filePath = 'uploads/invoice/invoice_' . $input['company_id'] . date("YmdHis") . '.pdf';
        Storage::disk('s3')->put($filePath, $dompdf->output());
        $data['admin_id'] = auth()->guard('admin')->user()->id;
        $data['company_id'] = $input["company_id"];
        $data['agent_name'] = $input["agent_name"];
        $data['invoice_no'] = $input["invoice_no"];
        $data['usdt_erc'] = config('app.usdt_erc');
        $data['usdt_trc'] = config('app.usdt_trc');
        $data['btc_value'] = config('app.btc');
        $data["amount_deducted_value"] = $input["amount_deducted_value"];
        $totalAmount = 0;
        foreach ($input['description'] as $key => $value) {
            $totalAmount += $value["amount"];
        }
        $data['total_amount'] = $totalAmount;
        unset($data["_token"]);
        $data["request_data"] = json_encode($input);
        $data["invoice_url"] = $filePath;
        $data["created_at"] = date("Y-m-d H:i:s");
        $data["updated_at"] = date("Y-m-d H:i:s");
        DB::table("invoices")->insert($data);
        notificationMsg('success', 'Invoice generated successfully.');
        return redirect()->route('invoices.index');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Invoices::where("id", $id)->delete();
        return back()->with("success", "Invoices deleted successfully!");
    }

    public function getApplicationInvoice(Request $request)
    {
        $id = $request->get('id');
        $data = Application::select("applications.id", "applications.country_code", "applications.phone_no", "applications.business_address1", "applications.business_name", "u.email")
            ->join("users as u", "u.id", "applications.user_id")
            ->where('applications.id', $id)
            ->first();
        return response()->json([
            'success' => '1',
            'data' => $data
        ]);
    }

    // * Download the Invoice

    public function downloadInvoice(Request $request)
    {
        return Storage::disk('s3')->download($request->file);
    }

    public function makeInvoicePaid(Request $request)
    {

        $input = Arr::except($request->all(), array('_token', '_method'));

        DB::table("invoices")->where("id", $request->get('is_paid'))->update(['is_paid' => 1]);

        notificationMsg('success', 'Paid status updated Successfully!');

        return redirect()->route('invoices.index');
    }

    public function updatedTransactionHash(Request $request)
    {

        $input = Arr::except($request->all(), array('_token', '_method'));

        DB::table("invoices")->where('id', $input['invoice_id'])->update(['transaction_hash' => $input['transaction_hash']]);

        notificationMsg('success', 'Transaction Hash updated successfully.');

        return redirect()->route('invoices.index');
    }

    public function massDelete(Request $request)
    {
        // $input = Arr::except($request->all(), array('_token', '_method'));
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                $this->invoice->destroyData($value);
            }
            return response()->json([
                'success' => true,
            ]);
        }
    }
}
