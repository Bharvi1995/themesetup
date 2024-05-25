<?php

namespace App\Exports;

use App\Agent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AgentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $input = request()->all();
        $data = Agent::select('name', 'email', 'referral_code', 'commission')->where('main_agent_id','0');

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
        if (isset($input['agent_id']) && $input['agent_id'] != '') {
            $data = $data->where('agents.id',$input['agent_id']);
        }
        $data = $data->where('main_agent_id','0')->orderBy('id', 'desc')->get();
        return $data;
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Email',
            'Referral Code',
            'Commission',
        ];
    }
}
