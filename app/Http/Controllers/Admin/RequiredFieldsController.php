<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RequiredField;
use DB;

class RequiredFieldsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $RequiredFields = RequiredField::all();
        return view('admin.requiredFields.index')->with(['RequiredFields' => $RequiredFields]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.requiredFields.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = \Arr::except($request->all(), ['_token']);
            $totalFields = $input["txtCount"] + 1;
            for ($i = 1; $i <= $totalFields; $i++) {
                $required_fields = new RequiredField();
                // $required_fields->field = \Str::slug($input["txtFieldTitle"][$i], '_');
                $required_fields->field = $input["txtField"][$i];
                $required_fields->field_title = $input["txtFieldTitle"][$i];
                $required_fields->field_type = $input["lstType"][$i];
                $required_fields->field_validation = $input["txtValidation"][$i];
                $required_fields->save();
            }
            DB::commit();
            \Session::put('success', 'Fields have been created successfully.');
            return redirect()->route('required_fields.index');
        } catch (Exception $e) {
            DB::rollBack();
            \Session::put('error', 'Something went wrong.Try Again.');
            return redirect()->back();
        }
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
        $data = RequiredField::findOrFail($id);
        return view('admin.requiredFields.edit', compact('data'));
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
        try {
            $required_fields = RequiredField::findOrFail($id);
            $required_fields->field_title = $request->field_title;
            // $required_fields->field = \Str::slug($request->field_title, '_');
            $required_fields->field = $request->field;
            $required_fields->field_type = $request->field_type;
            $required_fields->field_validation = $request->field_validation;
            $required_fields->save();
            DB::commit();
            \Session::put('success', 'Fields have been updated successfully.');
            return redirect()->route('required_fields.index');
            //echo $id;echo "<pre>";print_r($request->toArray());exit();
        } catch (Exception $e) {
            DB::rollBack();
            \Session::put('error', 'Something went wrong.Try Again.');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequiredField $RequiredFields, $id)
    {
        $RequiredFields->destroyData($id);
        notificationMsg('success', 'Fields Delete Successfully!');
        return redirect()->route('required_fields.index');
    }
}
