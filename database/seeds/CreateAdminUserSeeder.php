<?php

use Illuminate\Database\Seeder;
use App\Admin;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = Admin::create([
            'name' => 'Main Admin',
            'email' => 'testpay@gmail.com',
            'password' => bcrypt('testpay#$2024$#'),
            'is_otp_required' => '0'
        ]);
        $role = Role::create(['name' => 'Supper Admin', 'guard_name' => 'admin']);
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
    }
}
