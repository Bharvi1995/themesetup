<?php


namespace App\Exports;

use App\Store;
use App\StoreProduct;
use App\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserProductExport implements FromCollection, WithHeadings
{

    protected $id;
    public function __construct($id = null)
    {
        $this->id = $id;
    }

     /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $input = request()->all();
        $input['store_id'] = $this->id;

        $data = StoreProduct::select("name", "price", DB::raw("(CASE 
            WHEN status = '1'
            THEN 'Active'
            ELSE 'In active'
        END) as status"));
        if (isset($input['store_id']) && $input['store_id'] != null) {
            $data = $data->where('store_id', $input['store_id']);
        }

        if (isset($input['name']) && !empty($input['name']))
        {
            $data = $data->where('name', 'like', '%'.$input['name'].'%');
        }

        if (isset($input['status']) && $input['status'] != null)
        {
            $data = $data->where('status', $input['status']);
        }

        $data = $data->orderBy('id', 'desc')->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Price',
            'Status'
        ];
    }
}
?>