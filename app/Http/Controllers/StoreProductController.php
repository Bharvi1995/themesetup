<?php

namespace App\Http\Controllers;

use App\StoreProduct;
use Validator;
use Illuminate\Support\Facades\Auth;
use URL;
use Session;
use Exception;
use Redirect;
use Input;
use View;
use Mail;
use DB;
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class StoreProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->moduleTitleS = 'Store Product';
        $this->moduleTitleP = 'front.store.product';

        $this->storeProduct = new StoreProduct;

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($store_id,Request $request)
    {
        $store_id = idDecode($store_id);
        $input = \Arr::except($request->all(), array('_token', '_method'));
        
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $input['store_id'] = $store_id;
        $user = Auth::user()->main_user_id ?  Auth::user()->main_user_id : Auth::user()->id;
        $input["user_id"] = $user;
        // $data = [];
        $data = $this->storeProduct->getStoreProducts($input, $noList);
        return view($this->moduleTitleP . '.index', compact('data','store_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($store_id)
    {
        $store_id = idDecode($store_id);
        return view($this->moduleTitleP . '.create', compact('store_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($store_id,Request $request)
    {
        // dd($store_id);
        $input = \Arr::except($request->all(), array('_token', '_method'));
        
        $this->validate(
            $request,
            [
                'name' => 'required|max:50',
                'price' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:jpg,jpeg,png|max:4096',
            ]
        );
        $user = auth()->user();
        $input['store_id'] = $store_id;
        $input['slug'] = \Str::slug($input['name']).'-'.time();
        if ($request->hasFile('image')) {
            $image = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $image = $image . '.' . $request->file('image')->getClientOriginalExtension();
            $filePath = 'uploads/store-front-' . $user->id . '/' . $image;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('image')->getRealPath()));
            $input['image'] = $filePath;
        }
        
        DB::beginTransaction();
        try {
            $this->storeProduct->storeData($input);
            DB::commit();
            notificationMsg('success', "Product Added Successfully.");
            return redirect()->route('store-product.index',idEncode($store_id));
        } catch (Exception $e) {
            DB::rollBack();
            notificationMsg('error', 'Product not added. Try Again.');
            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StoreProduct  $storeProduct
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StoreProduct  $storeProduct
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $store_id, $id)
    {
        $store_id = idDecode($store_id);
        $id = idDecode($id);
        $product = $this->storeProduct->findData($id);
        if($product->id != $id){
            return redirect()->back();
        }
        return view($this->moduleTitleP.'.edit', compact('product','store_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StoreProduct  $storeProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $store_id, $id)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        // banner dimension 700*500
        $this->validate(
            $request,
            [
                'name' => 'required|max:50',
                'price' => 'required',
                'description' => 'required',
                'image' => 'mimes:jpg,jpeg,png|max:4096',
            ]
        );

        $user = auth()->user();
        
        if ($request->hasFile('image')) {
            $image = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $image = $image . '.' . $request->file('image')->getClientOriginalExtension();
            $filePath = 'uploads/store-front-' . $user->id . '/' . $image;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('image')->getRealPath()));
            $input['image'] = $filePath;
        }
        
        DB::beginTransaction();
        try {
            $this->storeProduct->updateData($id, $input);
            DB::commit();
            notificationMsg('success', "Product Updated Successfully.");
            return redirect()->route('store-product.index', idEncode($store_id));
        } catch (Exception $e) {
            DB::rollBack();
            notificationMsg('error', 'Product not updated. Try Again.');
            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StoreProduct  $storeProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy($store_id,$id)
    {
        $store_id = idDecode($store_id);
        $id = idDecode($id);
        
        $this->storeProduct->destroyData($id);
        notificationMsg('success', 'Product Deleted Successfully!');
        return redirect()->route('store-product.index');
    }

    public function changeStatus(Request $request)
    {
        $this->validate($request,[
            'product_id' => 'required',
            'status' => 'required'
        ]);

        $product = $this->storeProduct->findData($request->product_id);

        $product->status = $request->status;
        if($product->save()){
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
}
