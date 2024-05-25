<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class BlockCard extends Model
{
    
    protected $table = 'block_cards';
    protected $guarded = array();

    protected $fillable = [
        'user_id', 
        'order_id',
        'country',
        'card_type',
        'amount',
        'amount_in_usd',
        'currency',
        'card_no',
        'status',
        'reason'
    ];

    public function getData($input = [], $noList = 0)
    {
        $data = static::select('block_cards.*',"applications.business_name as userName", 'transactions.id AS transaction_id')
                    ->join('transactions', 'transactions.order_id', 'block_cards.order_id')
                    ->join('applications', 'applications.user_id', 'block_cards.user_id')
                    ->orderBy("block_cards.id");
        
        if (! empty($input)) {

            if (isset($input['card_no']) && $input['card_no'] != '') {
                $data = $data->where('block_cards.card_no',  'like', '%' . $input['card_no'] . '%');
            }
            if (isset($input['order_id']) && $input['order_id'] != '') {
                $data = $data->where('block_cards.order_id', $input['order_id']);
            }
            if (isset($input['card_type']) && $input['card_type'] != '') {
                $data = $data->where('block_cards.card_type', $input['card_type']);
            }
            if (isset($input['greater_then']) && $input['greater_then'] != '') {
                $data = $data->where('block_cards.amount', '>=', $input['greater_then']);
            }
            if (isset($input['less_then']) && $input['less_then'] != '') {
                $data = $data->where('block_cards.amount', '<=', $input['less_then']);
            }
            if (isset($input['reason']) && $input['reason'] != '') {
                $data = $data->where('block_cards.reason',  'like', '%' . $input['reason'] . '%');
            }
            if (isset($input['status']) && $input['status'] != '') {
                $data = $data->where('block_cards.status',  'like', '%' . $input['status'] . '%');
            }
            if (isset($input['currency']) && $input['currency'] != '') {
                $data = $data->where('block_cards.currency',  'like', '%' . $input['currency'] . '%');
            }
            if (isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('block_cards.user_id', $input['user_id']);
            }
        }
        
        if ($noList > 0) {
            return $data->paginate($noList);
        }
        return $data->get();
    }

    public function destroyData($id)
    {
        return static::find($id)->delete();
    }
}
