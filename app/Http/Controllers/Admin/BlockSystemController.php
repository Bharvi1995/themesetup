<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use App\BlockData;
use DB;
class BlockSystemController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->BlockData = new BlockData;
        $this->moduleTitleP = 'admin.blockSystem';
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
        $data = $this->BlockData->getData($input, $noList);
        return view($this->moduleTitleP . '.index', compact('data'));
    }

    public function add(){
     	return view($this->moduleTitleP . '.add');
    }

    public function store(Request $request){
    	$this->validate($request, [
            'type' => 'required',
            'field_value' => 'required'
        ]);
    	try {
            $blockData = new BlockData();
            $blockData->type = $request->type;
            $blockData->field_value = \Str::of($request->field_value)->trim();
            $blockData->save();
            DB::commit();
            notificationMsg('success', 'Block Card/Email inserted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            notificationMsg('error', 'Something went wrong!!');
        }
        return redirect()->route('block-system');
    }

    public function edit($id){
    	$data = BlockData::find($id);
        return view($this->moduleTitleP . '.edit', compact('data'));
    }

    public function update(Request $request,$id){
    	$this->validate($request, [
            'type' => 'required',
            'field_value' => 'required'
        ]);
    	try {
            $blockData = BlockData::find($id);
            $blockData->type = $request->type;
            $blockData->field_value = \Str::of($request->field_value)->trim();
            $blockData->save();
            DB::commit();
            notificationMsg('success', 'Block Card/Email updated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            notificationMsg('error', 'Something went wrong!!');
        }
        return redirect()->route('block-system');
    }

    public function destroy($id){
        try {
            BlockData::find($id)->delete();
            DB::commit();
            notificationMsg('success', 'Block Card/Email deleted Successfully!'); 
        } catch (Exception $e) {
            DB::rollback();
            notificationMsg('error', 'Something went wrong!!');
        }
        return redirect()->route('block-system');
    }
}
