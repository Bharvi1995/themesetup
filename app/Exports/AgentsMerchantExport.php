<?php

namespace App\Exports;

use DB;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Crypt;

class AgentsMerchantExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){

        $input = request()->all();

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $userIds = \DB::table('users')->where('agent_id',$agentId)->pluck('id');

        $data = DB::table("users")->select('applications.business_name', 'middetails.bank_name','users.*')
            ->join('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid_list');

        if(isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email',  'like', '%' . $input['email'] . '%');
        }

        if(isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name',  'like', '%' . $input['company_name'] . '%');
        }

        if(isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use($input){
                    $query->orWhere('applications.business_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('users.id', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('users.email', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('users.token', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('users.otp', 'like', '%'.$input['global_search'].'%');
                });
        }
        $data = $data->whereNull('users.deleted_at')
                    ->whereIn('users.id', $userIds)
                    ->orderBy('users.id', 'desc')
                    ->get();
        return $data;
    }

    public function headings(): array{
        return [
            'Email',
            'Company Name'
        ];
    }

    public function map($user): array{

        return [
            $user->email,
            $user->business_name
        ];
    }
}
