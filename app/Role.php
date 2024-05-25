<?php

namespace App;

use Spatie\Permission\Models\Role as SpatieRole;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Role extends SpatieRole
{
	use Cachable;

	protected $fillable = [
	    'name',
        'guard_name'
    ];
}
