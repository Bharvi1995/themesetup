<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RoleController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(10);
        return view('admin.roles.index', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get()->groupBy('module')->map(function ($per) {
            return $per->groupBy('sub_module');
        });
        $moduleList = Permission::distinct()->pluck('module')->toArray();
        return view('admin.roles.create', compact('permission', 'moduleList'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name|regex:/^[a-z\d\-_\s\.]+$/i',
            'permission' => 'required',
        ],
        [
            'name.regex' => 'Please Enter Only Alphanumeric Characters.',
        ]);

        $role = new Role();
        $role->name = $request->input('name');
        $role->guard_name = 'admin';

        DB::beginTransaction();
        try {
            $role->save();
            $role->syncPermissions($request->input('permission'));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('roles.index')
                ->with('error', 'Something wrong!Try Again.');
        }
        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully!');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get()->groupBy('module')->map(function ($per) {
                return $per->groupBy('sub_module');
            });

        return view('admin.roles.show', compact('role', 'rolePermissions'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get()->groupBy('module')->map(function ($per) {
            return $per->groupBy('sub_module');
        });
        $moduleList = Permission::distinct()->pluck('module')->toArray();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')->toArray();

        return view('admin.roles.edit', compact('role', 'permission', 'rolePermissions', 'moduleList'));
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
        $this->validate($request, [
            'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
            'permission' => 'required',
        ],
        [
            'name.regex' => 'Please Enter Only Alphanumeric Characters.',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->guard_name = 'admin';
        DB::beginTransaction();
        try {
            $role->save();
            $role->syncPermissions($request->input('permission'));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('roles.index')
                ->with('error', 'Role not created');
        }
        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully!');
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
            DB::table("roles")->where('id', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('roles.index')
                ->with('error', 'Role not deleted');
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully!');
    }
}
