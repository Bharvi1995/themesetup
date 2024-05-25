<?php

namespace App\Http\Controllers\Admin;

use App\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class SubgatewayController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($gateway_id)
    {
        $gateway = Gateway::find($gateway_id);
        $subgateways = \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->get();
        return view('admin.subgateway.index', compact('gateway', 'subgateways'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($gateway_id)
    {
        $gateway = Gateway::find($gateway_id);
        return view('admin.subgateway.create', compact('gateway'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($gateway_id, Request $request)
    {
        $request->validate([
            'name' => 'required|min:2|max:30'
        ]);
        $gateway = Gateway::find($gateway_id);
        // $this->validate($request,[''])
        $inputs = \Arr::except($request->all(), ['_token']);
        // $inputs['is_active']
        \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->insert($inputs);
        return redirect()->route('admin.subgateway.index', $gateway_id);
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
    public function edit($gateway_id, $id)
    {
        $gateway = Gateway::find($gateway_id);
        $subgateway = \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->where('id', $id)->first();
        return view('admin.subgateway.edit', compact('gateway', 'subgateway'));
    }
    public function subGatewayEdit($gateway_id, $id)
    {
        $gateway = Gateway::find($gateway_id);
        $subgateway = \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->where('id', $id)->first();
        return view('admin.subgateway.edit', compact('gateway', 'subgateway'));
    }
    public function subGatewayDelete($gateway_id, $id)
    {
        $gateway = Gateway::find($gateway_id);
        \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->delete($id);

        notificationMsg('success', 'Record Deleted Successfully!');

        return redirect()->route('admin.subgateway.index', $gateway_id);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $gateway_id, $id)
    {
        $request->validate([
            'name' => 'required|min:2|max:30'
        ]);
        $gateway = Gateway::find($gateway_id);
        $inputs = \Arr::except($request->all(), ['_token', '_method']);
        $subgateway = \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->where('id', $id)->update($inputs);
        notificationMsg('success', 'Record updated Successfully!');
        return redirect()->route('admin.subgateway.index', $gateway_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($gateway_id, $id)
    {
        $gateway = Gateway::find($gateway_id);
        \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->delete($id);

        notificationMsg('success', 'Record Deleted Successfully!');

        return redirect()->route('admin.subgateway.index', $gateway_id);
    }

    public function getSubgateway(Request $request)
    {
        $gateway = Gateway::find($request->id);
        $subgateways = \DB::table('gateway_' . \Str::slug($gateway->title, "_"))->pluck('name', 'id');
        // dd($subgateways);
        return view('admin.subgateway.dropdown-options', compact('subgateways'))->render();
    }
}
