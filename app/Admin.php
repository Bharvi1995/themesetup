<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasRoles, Cachable, Notifiable;

    protected $table = 'admins';
    protected $guarded = array();

    protected $fillable = [
        'name',
        'email',
        'country_code',
        'mobile_no',
        'is_active',
        'is_otp_required',
        'is_password_expire',
        'token',
        'password',
        'email_changes'
    ];

    protected $hidden = [
        'password',
        'token',
        'remember_token'
    ];

    public function getData($input = [], $noList = 0)
    {
        $data = static::select("admins.*", 'roles.name as role')
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', 'admins.id')
            ->leftJoin('roles', 'roles.id', 'model_has_roles.role_id')
            ->orderBy("admins.id", "DESC");
        if (!empty($input)) {
            if (isset($input['name']) && $input['name'] != '') {
                $data = $data->where('admins.name', 'like', '%' . $input['name'] . '%');
            }
            if (isset($input['email']) && $input['email'] != '') {
                $data = $data->where('admins.email', 'like', '%' . $input['email'] . '%');
            }
            if (isset($input['is_active']) && $input['is_active'] != '') {
                $data = $data->where('admins.is_active', $input['is_active']);
            }
        }
        if ($noList > 0) {
            return $data->paginate($noList);
        }
        return $data->get();
    }

    public function findData($id)
    {
        return static::find($id);
    }

    public function storeData($input)
    {
        return static::create($input);
    }

    public function destroyData($id)
    {
        return static::find($id)->delete();
    }

    public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }

    public function userBan($id)
    {
        return static::where('id', $id)->update(array('ban' => 1));
    }

    public function userRevoke($id)
    {
        return static::where('id', $id)->update(array('ban' => 0));
    }
}