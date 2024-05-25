<?php
namespace App\Http\Controllers\Admin;

use App\Exports\UserProductExport;
use App\Exports\UserStoreExport;
use App\Http\Controllers\AdminController;
use App\Store;
use App\StoreProduct;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserStoresController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->User = new User;
        $this->store = new Store;
        $this->storeProduct = new StoreProduct;
        $this->moduleTitleS = 'User Stores';
        $this->moduleTitleP = 'admin.userStores';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $dataT = $this->store->getAllStores($input, $noList);
        $merchants = $this->user::select('id','name')->whereNull('deleted_at')->get();
        return view($this->moduleTitleP . '.index', compact('dataT', 'merchants'))->with('i', ($request->input('page', 1) - 1) * $noList);
    }

    public function products(Request $request, $id)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $input['store_id'] = $id;
        $dataT = $this->storeProduct->getStoreProducts($input, $noList);
        return view($this->moduleTitleP . '.products', compact('dataT', 'id'))->with('i', ($request->input('page', 1) - 1) * $noList);
    }

    public function export(Request $request) 
    {
        return Excel::download(new UserStoreExport, 'UserStoreList_Excel_' . date('d-m-Y') . '.xlsx');
    }
    public function productExport(Request $request, $id)
    {
        return Excel::download(new UserProductExport($id), 'UserProductList_Excel_' . date('d-m-Y') . '.xlsx');
    }
}
?>