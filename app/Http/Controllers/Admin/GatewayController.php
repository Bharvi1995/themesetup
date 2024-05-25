<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\RequiredField;

class GatewayController extends AdminController
{
    public function create()
    {
        //$required_fields = config('required_field.total_fields');
        $required_fields = RequiredField::all();
        return view('admin.gateway.create', compact('required_fields'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'credential_title.*' => 'required',
            'name.*' => 'required',
            'type' => 'required',
            'is_required' => 'required',
        ]);

        $input = \Arr::except($request->all(), ['_token']);
        $fields = [];
        foreach ($input['name'] as $key => $value) {
            $fields[$value] = $input['credential_title'][$key];
        }
        $gateway_title = \Str::slug($input['title'], "_");
        DB::beginTransaction();
        try {
            $gateway = Gateway::create([
                'title' => $input['title'],
                'credential_titles' => json_encode($fields),
                'required_fields' => json_encode($input['required_fields'])
            ]);
            Schema::create('gateway_' . $gateway_title, function (Blueprint $table) use ($input) {
                $table->increments('id');
                $table->string('name')->nullable();
                foreach ($input['name'] as $key => $value) {
                    $table->{$input['type'][$key]}($value)->nullable();
                }
                $table->enum('is_active', ['0', '1'])->default(1);
                $table->timestamps();
            });
            notificationMsg('success', 'Gateway created successfully!.');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            notificationMsg('error', 'Gateway not created!.');
        }
        if ($gateway) {
            return redirect()->route('admin.gateway.index');
        }
        return redirect()->back();
    }

    public function index()
    {
        $gateways = Gateway::all();
        return view('admin.gateway.index', compact('gateways'));
    }

    public function edit($id)
    {
        $data = Gateway::where('id', $id)->first();
        $required_fields = RequiredField::all();
        return view('admin.gateway.edit', compact('required_fields', 'data'));
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'required_fields' => 'required',
        ]);
        $input = \Arr::except($request->all(), ['_token']);

        $gateway = Gateway::where('id', $id)->update([
            'required_fields' => json_encode($input['required_fields'])
        ]);

        if ($gateway) {
            notificationMsg('success', 'Gateway edited successfully.');
            return redirect()->route('admin.gateway.index');
        }
        return redirect()->back();
    }
}