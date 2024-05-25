<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use Cachable;
    use SoftDeletes;

    protected $fillable = [
        'title', 'body', 'files', 'user_id', 'status', 'department'
    ];
    protected $table = 'tickets';

    // status = 0 panding, 1 done, 2 in review, 3 close, 4 other

    public function storeData($input)
    {
        return static::create($input);
    }

    public function destroyWithUserId($id)
    {
        return static::where('user_id', $id)->delete();
    }

    public function getData($input, $noList)
    {
        if (\Auth::user()->main_user_id != '0')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;
        return static::orderBy('created_at', 'desc')->where('user_id', $userID)->paginate($noList);
    }

    public function getTicketsDash()
    {
        if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

        return static::where('user_id', $userID)
            ->latest()
            ->take(5)
            ->get();
    }

    public function getTicketsAdminDash()
    {
        return static::latest()
            ->take(5)
            ->get();
    }

    public function getAdminTickets($input, $noList)
    {
        $data = static::with(['user','user.application'])->orderBy('created_at', 'desc');
        $data = $data->whereHas('user', function($query) use($input) {
                $query->whereNull('deleted_at');
            });

        if(isset($input['user_id']) && $input['user_id']){
            $data = $data->where('user_id', $input['user_id']);
        }

        if(isset($input['status']) && $input['status']){
            $data = $data->where('status', $input['status']);
        }

        if(isset($input['department']) && $input['department']){
            $data = $data->where('department', $input['department']);
        }

        if(isset($input['email']) && $input['email']){
            $data = $data->whereHas('user', function($query) use($input) {
                $query->where('email', $input['email']);
            });
        }

        if(isset($input['business_name']) && $input['business_name']){
            $data = $data->whereHas('user.Application', function($query) {
                $query->where('business_name', $input['business_name']);
            });
        }

        if(isset($input['start_date']) && $input['start_date']){
            $start_date = date('Y-m-d', strtotime($input['start_date']));

            $data = $data->where(DB::raw('DATE(created_at)'),'>=', $start_date);
        }

        if(isset($input['end_date']) && $input['end_date']){
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(created_at)'), '<=', $end_date);
        }
        
        return $data->paginate($noList);
    }

    public function findData($id)
    {
        return static::find($id);
    }

    public function replies()
    {
        return $this->hasMany('App\TicketReply', 'ticket_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function updateStatus($id, $status)
    {
        return static::where('id', $id)->update(['status' => $status]);
    }
}

// user create

// list, status , action - delete, show

// admin,

// listing, action - assign to opretor, bank, -re-assign, close replay
