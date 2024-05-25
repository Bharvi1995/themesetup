<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AgreementContent;
use DB;

class AgreementContentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = AgreementContent::all();
        return view('admin.AgreementContent.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.AgreementContent.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'body' => 'required'
        ]);

        AgreementContent::create($input); 

        notificationMsg('success','Agreement Content Store Successfully!');

        return redirect()->route('agreement_content.index');
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
        $data = AgreementContent::findOrFail($id);
        return view('admin.AgreementContent.edit', compact('data'));
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
        $input = $request->all();

        $this->validate($request, [
            'body' => 'required'
        ]);

        AgreementContent::find($id)->update($input); 

        notificationMsg('success','Agreement Content Update Successfully!');

        return redirect()->route('agreement_content.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	DB::beginTransaction();
        try {
        	AgreementContent::find($id)->delete();

            DB::commit();
            notificationMsg('success', 'Agreement Content Delete Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            notificationMsg('error', 'Agreement not deleted!');
        }
        return redirect()->route('agreement_content.index');
    }
}
