<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserNotification;
use App\Http\Controllers\Controller;
use App\ImageUpload;
use App\Notifications\TicketReplyByAdmin;
use App\Ticket;
use App\TicketReply;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TicketReplyController extends Controller
{
    public function __construct()
    {
        $this->ticketreply = new TicketReply;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        //echo"<pre>";print_r($request->toArray());exit();
        $this->validate($request, [
            'body' => 'required'
        ], ['body.required' => 'This fied is required.']);
        $input = \Arr::except($request->all(), array('_token', '_method'));
        try {
            if (isset($input['files'])) {
                if (count($input['files'])) {
                    $files = $request->file('files');
                    $path = storage_path() . "/uploads/tickets/";
                    foreach ($input['files'] as $key => $file) {
                        $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                        $imageName = $imageName . '.' . $files[$key]->getClientOriginalExtension();
                        $filePath = '/uploads/tickets/' . $imageName;
                        //$files[$key]->move($path,$imageName);
                        Storage::disk('s3')->put($filePath, file_get_contents($file->getRealPath()), 'public');
                        $input['files'][$key] = $imageName;
                    }
                }
                $input['files'] = json_encode($input['files']);
            }
            $input['user_id'] = auth()->guard(get_guard())->user()->id;
            $input['user_type'] = get_guard();
            $ticketreply = $this->ticketreply->storeData($input);
            $ticket = Ticket::find($input['ticket_id']);
            try {
                // $notification = [
                //     'user_id' => $ticket->user_id,
                //     'sendor_id' => auth()->guard(get_guard())->user()->id,
                //     'type' => 'user',
                //     'title' => $ticket->title . ' - Ticket Reply',
                //     'body' => 'Ticket Reply by ' . auth()->guard(get_guard())->user()->name,
                //     'url' => '/ticket/' . $ticket->id,
                //     'is_read' => '0'
                // ];
                // $realNotification = addNotification($notification);
                // $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                // event(new UserNotification($realNotification->toArray()));
                // Mail::to($ticket->user)->queue(new \App\Mail\TicketReplyByAdmin($ticket));
                \Session::put('success', 'Replied successfully.');
                return redirect()->route('admin.ticket.show', $input['ticket_id']);
            } catch (Exception $e) {
                \Session::put('error', 'Something Went Wrong. Please try again!');
                return redirect()->back();
            }
        } catch (Exception $e) {
            \Session::put('error', 'Something Went Wrong. Please try again!');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TicketReply  $ticketReply
     * @return \Illuminate\Http\Response
     */
    public function show(TicketReply $ticketReply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TicketReply  $ticketReply
     * @return \Illuminate\Http\Response
     */
    public function edit(TicketReply $ticketReply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TicketReply  $ticketReply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TicketReply $ticketReply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TicketReply  $ticketReply
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketReply $ticketReply)
    {
        //
    }
}
