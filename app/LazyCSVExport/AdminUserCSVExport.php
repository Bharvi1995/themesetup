<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\Admin;
use DB;

class AdminUserCSVExport
{
    
    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function download(Request $request)
    {
        $input = $request->all();
        $columns = [
            'User Name',
            'Email',
            'Role',
            'Otp Status',
            'Status'
        ];
        // dd('Yes');

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = Admin::select("admins.name","admins.email",'roles.name as role',"admins.is_otp_required","admins.is_active")
                ->leftJoin('model_has_roles','model_has_roles.model_id','admins.id')
                ->leftJoin('roles','roles.id','model_has_roles.role_id')
                ->orderBy("admins.id","DESC");
        if (! empty($input)) {
            if (isset($input['name']) && $input['name'] != '') {
                $data = $data->where('admins.name',  'like', '%' . $input['name'] . '%');
            }
            if (isset($input['email']) && $input['email'] != '') {
                $data = $data->where('admins.email',  'like', '%' . $input['email'] . '%');
            }
            if (isset($input['is_active']) && $input['is_active'] != '') {
                $data = $data->where('admins.is_active',   $input['is_active']);
            }
        }
        $data = $data->orderBy('admins.id', 'desc');
        $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                fputcsv($file, $data);
            });

            fclose($file);
        }, 'Admin_user_Excel_'.date('d-m-Y').'.csv');
    }
}
