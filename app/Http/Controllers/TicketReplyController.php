<?php

namespace App\Http\Controllers;

use App\Events\AdminNotification;
use Mail;
use Auth;
use Notification;
use App\Admin;
use App\User;
use App\Ticket;
use App\TicketReply;
use App\ImageUpload;
use App\Mail\TicketReplyByUser;
use App\Notifications\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class TicketReplyController extends Controller
{
    public function __construct()
    {
        $this->moduleTitleS = 'Ticket';
        $this->moduleTitleP = 'ticket';

        $this->ticketreply = new TicketReply;
        $this->User        = new User;

        view()->share('moduleTitleP',$this->moduleTitleP);
        view()->share('moduleTitleS',$this->moduleTitleS);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'body'=>'required',
            'files.*' =>'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840'
        ],
        ['body.required'=>'This fied is required.']);
        $input = \Arr::except($request->all(),array('_token', '_method'));

        \DB::beginTransaction();
        try {
            if(\Auth::user()->main_user_id != '0')
            $userID = \Auth::user()->main_user_id;
            else
                $userID = \Auth::user()->id;
            $user =  $this->User::select('email','name')->where('id',$userID)->first();

            if(isset($input['files'])){
                if(count($input['files'])){
                    $files = $request->file('files');
                    foreach ($input['files'] as $key => $file) {
                        $imageName = time().rand(0, 10000000000000).pathinfo(rand(111111111111,999999999999), PATHINFO_FILENAME);
                        $imageName = $imageName.'.'.$files[$key]->getClientOriginalExtension();
                        $filePath = '/uploads/tickets/' . $imageName;
                        Storage::disk('s3')->put($filePath,file_get_contents($file->getRealPath()));
                        //$filename = ImageUpload::upload('/uploads/tickets/',$files[$key]);
                        $input['files'][$key] = $imageName;
                    }
                }
                $input['files'] = json_encode($input['files']);
            }
            $input['user_id'] = $userID;
            $input['user_type'] = 'user';

            $input['title'] = 'Reply';
            $input['title'] = 'Reply From <a href="'.config('app.url').'/admin/ticket/'.$input['ticket_id'].'" target="_blank">'.config('app.url').'/admin/ticket/'.$input['ticket_id'].'</a>';

            $department = Ticket::where('id',$input['ticket_id'])->pluck('department')->first();

            try {
                Mail::to('sales@testpay.com')->send(new TicketReplyByUser($ticket,$user));
            } catch (\Exception $e) {

            }

            unset($input['title']);

            $ticketreply = $this->ticketreply->storeData($input);
            $ticket = Ticket::find($input['ticket_id']);

            $notification = [
                'user_id'=>'1',
                'sendor_id'=>$userID,
                'type'=>'admin',
                'title'=>$ticket->title .' - Ticket Reply',
                'body'=>'Ticket Reply by '.$user->name,
                'url'=>'/admin/ticket/'.$ticket->id,
                'is_read'=>'0'
            ];

            $realNotification = addNotification($notification);
            $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
            event(new AdminNotification($realNotification->toArray()));
        } catch(Exception $e) {
            \DB::rollBack();
            notificationMsg('error','Something Went Wrong. Please try again!');
            return redirect()->back();
        }
        \DB::commit();
        notificationMsg('success','Replied successfully.');
        addToLog('Ticket Replied successfully.', $input, 'general');
        return redirect()->route('ticket.show',$input['ticket_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // ================================================
    /* method : downloadTicketReplyFiles
    * @param  :
    * @description : download ticket reply files
    */// ==============================================
    public function downloadTicketReplyFiles($id, $number)
    {
        $data = TicketReply::where('id', $id)
            ->first();

        if ($data == null) {
            notificationMsg('error', 'File not exist.');

            return redirect()->route('ticket');
        }

        if(Auth::user()->main_user_id != '0') {
            $user_id = Auth::user()->main_user_id;
        } else {
            $user_id = Auth::user()->id;
        }

        if ($data->user_id != $user_id) {
            notificationMsg('error', 'File not exist.');

            return redirect()->route('ticket');
        }

        $file_array = json_decode($data->files);

        if ($file_array == null) {
            notificationMsg('error', 'File not exist.');

            return redirect()->route('ticket');
        }

        // download file
        if (array_key_exists($number - 1, $file_array)) {
            $request_file = ltrim($file_array[$number - 1], '/');

            if (file_exists($request_file)) {
                return response()->download($request_file);
            }
        }
        notificationMsg('error', 'File not exist.');

        return redirect()->route('ticket');
    }
}
