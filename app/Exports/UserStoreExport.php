<?php
namespace App\Exports;

use App\Store;
use App\StoreProduct;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserStoreExport implements FromCollection, WithHeadings
{
     /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $input = request()->all();

        $data = Store::select("name", "contact_us_email as email", "currency"/* , "status" */);
            
        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('stores.user_id', $input['user_id']);
        }

        if (isset($input['name']) && !empty($input['name'])) {
            $data = $data->where('stores.name' ,'like', '%'.$input['name'].'%');
        }
        
        if (isset($input['email']) && !empty($input['email'])) {
            $data = $data->where('stores.contact_us_email' ,'like', '%'.$input['email'].'%');
        }
        
        $data = $data->orderBy('id', 'desc')->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Store Name',
            'Email',
            'Currency'
        ];
    }
}