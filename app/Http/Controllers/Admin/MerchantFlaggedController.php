<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\AdminController;
use Hash;
use Validator;
use App\ImageUpload;
use App\Transaction;
use App\MIDDetail;
use App\Merchantapplication;
use App\TransactionsDocumentUpload;
use App\Exports\AdminFlaggedTransactionsExport;
use Maatwebsite\Excel\Facades\Excel;

class MerchantFlaggedController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->Transaction = new Transaction;
        $this->MIDDetail = new MIDDetail;
        $this->merchantapplication = new Merchantapplication;

        $this->moduleTitleS = 'Merchant Flagged';
        $this->moduleTitleP = 'admin.merchantFlagged';

        view()->share('moduleTitleP',$this->moduleTitleP);
        view()->share('moduleTitleS',$this->moduleTitleS);
    }
    
    public function index(Request $request)
    {
        $input = \Arr::except($request->all(),array('_token', '_method'));

        return view($this->moduleTitleP.'.index');                    
    }
}
