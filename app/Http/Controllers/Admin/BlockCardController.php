<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use App\BlockCard;
use App\AdminAction;
use App\Application;

class BlockCardController extends AdminController
{

   
    public function __construct()
    {
        parent::__construct();
        
        $this->blockedsysytem = new BlockCard;
        $this->application = new Application;
        $this->moduleTitleP = 'admin.blockedSystem';

        view()->share('moduleTitleP', $this->moduleTitleP);
       
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->blockedsysytem->getData($input, $noList);
        $companyList = $this->application->getCompanyName();
        return view($this->moduleTitleP . '.index', compact('data','companyList'));
    }

    public function destroy($id)
    {

        $this->blockedsysytem->destroyData($id);
        notificationMsg('success', 'BlockCard deleted Successfully!');
        $ArrRequest = [];
        addAdminLog(AdminAction::DELETE_REFERRAL_PARTNER, $id, $ArrRequest, "BlockCard deleted Successfully!");
        return redirect()->route('blocked-system');
    }

    public function deleteCard(Request $request)
    {

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $card = BlockCard::where('id', $request->get('id'))->first();

        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {

                $this->blockedsysytem->destroyData($value);
            }
            return response()->json([
                'success' => true,
            ]);
        }
        if ($this->blockedsysytem->destroyData($request->get('id'))) {
            $card = BlockCard::where('id', $request->get('id'))->first();
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
