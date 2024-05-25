<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\AdminController;
use Hash;
use Mail;
use URL;
use Auth;
use Validator;
use App\ImageUpload;
use App\Transaction;
use App\MIDDetail;
use App\Merchantapplication;
use App\RemoveFlaggedTransaction;
use App\UserGenerateReport;
use App\TransactionsDocumentUpload;
use App\Exports\AdminChargebackTransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\chargebacksTransactionMail;

class MerchantChargebacksController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->Transaction = new Transaction;
        $this->RemoveFlaggedTransaction = new RemoveFlaggedTransaction;
        $this->MIDDetail = new MIDDetail;
        $this->merchantapplication = new Merchantapplication;

        $this->moduleTitleS = 'Merchant Chargebacks';
        $this->moduleTitleP = 'admin.merchantChargebacks';

        view()->share('moduleTitleP',$this->moduleTitleP);
        view()->share('moduleTitleS',$this->moduleTitleS);
    }
    
    public function index(Request $request)   
    {
        $input = \Arr::except($request->all(),array('_token', '_method'));
        
        return view($this->moduleTitleP.'.index');                    
    }
}
