<?php
namespace App\Http\Controllers;

use Auth;
use Hash;
use File;
use Validator;
use App\User;
use App\ImageUpload;
use App\Transaction;
use App\TransactionsDocumentUpload;
use App\Exports\RetrievalTransactionExport;
use App\Http\Requests;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RetrievalController extends HomeController
{

    public function __construct()
    {
        parent::__construct();
        $this->Transaction = new Transaction;

        $this->moduleTitleS = 'Merchant Retrieval';
        $this->moduleTitleP = 'front.retrieval';

        view()->share('moduleTitleP',$this->moduleTitleP);
        view()->share('moduleTitleS',$this->moduleTitleS);
    }
    
    public function index(Request $request)
    {
        $input = \Arr::except($request->all(),array('_token', '_method'));
        if(isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new RetrievalTransactionExport, 'Retrieval_Transaction_Excel_'.date('d-m-Y').'.xlsx');
        }
        return view($this->moduleTitleP.'.index');                    
    }
}
