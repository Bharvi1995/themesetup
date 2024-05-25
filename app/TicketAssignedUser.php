<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\TicketAssign;
use Notification;

class TicketAssignedUser extends Model
{
    protected $table = "ticket_assigned_user";
    protected $guarded = [];

    public function storeData($input)
    {
    	\DB::beginTransaction();
    	try {
                $users_id = $input['user_id'];
                $old = static::where('ticket_id', $input['ticket_id'])->where('user_type',$input['type'])->get();
                static::where('ticket_id', $input['ticket_id'])->where('user_type',$input['type'])->delete();
                foreach ($users_id as $key => $value) {
                    $data['user_id'] = $value;
                    $data['ticket_id'] = $input['ticket_id'];
                    $data['user_type'] = $input['type'];
                    static::create($data);
                    //send notification if already not sent
                    if(!$old->where('ticket_id',$input['ticket_id'])->where('user_type',$input['type'])->where('user_id',$value)->first()){
                        $ticket = Ticket::find($input['ticket_id']);
                        if($input['type'] == 'operator'){
                            $user = Operator::find($value);
                        }elseif($input['type'] == 'bankUser'){
                            $user = BankUser::find($value);
                        }
                        $user->notify(new TicketAssign($ticket,$input['type']));
                    }
                }
            } catch(Exception $e) {
                \DB::rollBack();
                return false;
            }
            \DB::commit();
            return true;
    }

    public function getUsers($id,$type)
    {
    	return static::where('ticket_id',$id)->where('user_type',$type)->get();
    }
}
