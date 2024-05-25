<?php
namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->moduleTitleS = 'Orders';
        $this->moduleTitleP = 'front.order';

        $this->order = new Order();

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function index(Request $request) {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $user = Auth::user()->main_user_id ?  Auth::user()->main_user_id : Auth::user()->id;
        $input["user_id"] = $user;
        $data = $this->order->getAllOrders($input, $noList);
        return view($this->moduleTitleP . '.index', compact('data'));
    }
}
?>