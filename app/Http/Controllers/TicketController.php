<?php

namespace App\Http\Controllers;

use App\Events\AdminNotification;
use URL;
use Auth;
use View;
use File;
use Mail;
use Input;
use Session;
use Redirect;
use Validator;
use Illuminate\Support\Facades\Storage;
use App\Ticket;
use App\Admin;
use App\User;
use App\Application;
use App\TicketReply;
use App\ImageUpload;
use App\Merchantapplication;
use App\Mail\ticketMail;
use App\Notifications\TicketClose;
use App\Notifications\TicketClosed;
use App\Notifications\TicketCreate;
use App\Notifications\TicketComment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TicketController extends HomeController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->moduleTitleS = 'Ticket';
        $this->moduleTitleP = 'front.ticket';

        $this->ticket = new Ticket;
        $this->ticketreply = new TicketReply;
        $this->Application = new Application;
        $this->User        = new User;

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->ticket->getData($input, $noList);
        return view($this->moduleTitleP . '.index', compact('data'));
    }

    public function create()
    {
        return view($this->moduleTitleP . '.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            // 'title' => 'required',
            'body' => 'required',
            // 'department' => 'required',
            'files.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840'
        ], [
            'title.required' => 'This field is required.',
            'body.required' => 'This field is required.',
            // 'department.required' => 'This field is required.'
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (\Auth::user()->main_user_id != '0')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;
        $user =  $this->User::select('email', 'name')->where('id', $userID)->first();
        \DB::beginTransaction();
        try {
            if (isset($input['files'])) {
                if (count($input['files'])) {
                    $files = $request->file('files');
                    foreach ($input['files'] as $key => $value) {
                        $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                        $imageName = $imageName . '.' . $files[$key]->getClientOriginalExtension();
                        $filePath = '/uploads/tickets/' . $imageName;
                        Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                        $input['files'][$key] = $imageName;
                    }
                    $input['files'] = json_encode($input['files']);
                }
            }
            $input["title"] = "HELPDESK". rand(0, 999999999);;
            $input['user_id'] = $userID;
            $input['status'] =  '0';

            $ticket = $this->ticket->storeData($input);
            Mail::to('info.paylaksa@gmail.com')->send(new \App\Mail\TicketCreate($ticket, auth()->user()));
            // Mail::to($user->email)->queue(new \App\Mail\TicketCreateUser($ticket));

            // $notification = [
            //     'user_id' => '1',
            //     'sendor_id' => $userID,
            //     'type' => 'admin',
            //     'title' => $ticket->title . ' - Ticket Create',
            //     'body' => 'Ticket Created by ' . $user->name,
            //     'url' => '/paylaksa/ticket/' . $ticket->id,
            //     'is_read' => '0'
            // ];

            // $realNotification = addNotification($notification);
            // $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
            // event(new AdminNotification($realNotification->toArray()));
        } catch (Exception $e) {
            \DB::rollBack();
            notificationMsg('error', 'Something Went Wrong. Please try again!');
            return redirect()->back();
        }
        \DB::commit();
        notificationMsg('success', 'Your helpdesk request has been successfully submitted. Our team will reach out to you shortly.');
        addToLog('Ticket created successfully.', $input, 'general');
        return redirect()->route('ticket');
    }

    public function show($id)
    {
        $ticket = $this->ticket->findData($id);
        // $disk = Storage::disk('s3');
        // $url = $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), 'uploads/tickets/'.$arr[0], Carbon::now()->addMinutes(5), []);
        return view($this->moduleTitleP . '.show', compact('ticket'));
    }

    public function close($id)
    {

        try {
            if (\Auth::user()->main_user_id != '0')
                $userID = \Auth::user()->main_user_id;
            else
                $userID = \Auth::user()->id;
            $user =  $this->User::select('email', 'name')->where('id', $userID)->first();
            if ($this->ticket->updateStatus($id, '3')) {
                try {
                    $ticket = Ticket::find($id);
                    Mail::to('info.paylaksa@gmail.com')->send(new \App\Mail\TicketCloseByUser($ticket));
                    $notification = [
                        'user_id' => '1',
                        'sendor_id' => $userID,
                        'type' => 'admin',
                        'title' => $ticket->title . ' - Ticket Closed',
                        'body' => 'Ticket Closed by ' . $user->name,
                        'url' => '/paylaksa/ticket/' . $ticket->id,
                        'is_read' => '0'
                    ];
                    $realNotification = addNotification($notification);
                    $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                    event(new AdminNotification($realNotification->toArray()));
                    \Session::put('success', 'Ticket closed successfully.');
                } catch (Exception $e) {
                    \Session::put('error', 'Something Went Wrong. Please try again!');
                }
                return redirect()->route('ticket');
            }
        } catch (Exception $e) {
            \Session::put('error', 'Something Went Wrong. Please try again!');
            return redirect()->back();
        }
    }

    public function reopen($id)
    {
        try {
            if (\Auth::user()->main_user_id != '0')
                $userID = \Auth::user()->main_user_id;
            else
                $userID = \Auth::user()->id;
            $user =  $this->User::select('email', 'name')->where('id', $userID)->first();

            if ($this->ticket->updateStatus($id, '2')) {
                $ticket = Ticket::find($id);
                $notification = [
                    'user_id' => '1',
                    'sendor_id' => $userID,
                    'type' => 'admin',
                    'title' => $ticket->title . ' - Ticket reopened',
                    'body' => 'Ticket Reopened by ' . $user->name,
                    'url' => '/paylaksa/ticket/' . $ticket->id,
                    'is_read' => '0'
                ];
                $realNotification = addNotification($notification);
                $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                event(new AdminNotification($realNotification->toArray()));
                \Session::put('success', 'Ticket reopened successfully.');
                return redirect()->route('ticket');
            }
        } catch (Exception $e) {
            \Session::put('error', 'Something Went Wrong. Please try again!');
            return redirect()->back();
        }
    }

    // ================================================
    /* method : downloadTicketFiles
    * @param  :
    * @description : download ticket files
    */ // ==============================================
    public function downloadTicketFiles($id, $number)
    {
        $data = Ticket::where('id', $id)
            ->first();

        if ($data == null) {
            notificationMsg('error', 'File not exist.');

            return redirect()->route('ticket');
        }

        if (Auth::user()->main_user_id != '0') {
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
                addToLog('Ticket docs download.', $data, 'general');
                return response()->download($request_file);
            }
        }
        notificationMsg('error', 'File not exist.');

        return redirect()->route('ticket');
    }

    public function destroy($id)
    {
        Ticket::find($id)->delete();

        notificationMsg('success', 'Ticket deleted successfully!');

        addToLog('Ticket deleted successfully.', NULL, 'general');

        return redirect()->route('ticket');
    }
}
