<?php

namespace App\Exports;

use App\WLAgent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WLAgentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $input = request()->all();
        $data = WLAgent::select('name', 'email', 'referral_code');

        if (!empty($input['ids'])) {
            if (is_string($input['ids'])) {
                $input['ids'] = explode(',', $input['ids']);
            }
            $data = $data->whereIn('id', $input['ids']);
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['name']) && $input['name'] != '') {
            $data = $data->where('name', 'like', '%' . $input['name'] . '%');
        }
        $data = $data->orderBy('id', 'desc')->get();
        return $data;
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Email',
            'Referral Code'
        ];
    }
}
