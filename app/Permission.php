<?php

namespace App;

use Spatie\Permission\Models\Permission as SpatiePermission;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Permission extends SpatiePermission
{
	use Cachable;

	protected $fillable = [
	    'module',
        'sub_module',
        'name',
        'guard_name'
    ];
}
