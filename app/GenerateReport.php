<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class GenerateReport extends Model
{
    // use Cachable;
    protected $table = 'generatereports';
    protected $guarded = array();

    public function getData()
    {
        $data = static::select("generatereports.*")
            ->where('is_download', '0')
            ->orderBy("generatereports.id","DESC")
            ->get();

        return $data;
    }

    public function getAllPDFData()
    {
        $data = static::select("generatereports.*")
            // ->where('is_download', '0')
            ->orderBy("generatereports.id","DESC")
            ->get();

        return $data;
    }

    public function getAllPDFFolderData()
    {
        $data = static::distinct()
            ->where('is_pdf', '0')
            ->get(['mid_no', 'company_name']);

        return $data;
    }

    public function getAllExcelData()
    {
        $data = static::select("generatereports.*")
            ->where('is_excel', '0')
            ->get()
            ->toArray();

        return $data;
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
        return static::where('id',$id)->update(array('ban' => 1));
    }

    public function userRevoke($id)
    {
        return static::where('id',$id)->update(array('ban' => 0));
    }
}
