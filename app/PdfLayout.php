<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class PdfLayout extends Model
{
    // use Cachable;

    protected $fillable = [
        'name','display_name','blade_file_name'
    ];

    protected $table = 'pdf_layouts';

    public function storeData($input)
    {
    	return static::create($input);
    }
}
