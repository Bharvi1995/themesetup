<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserNotification;
use URL;
use Input;
use View;
use File;
use Session;
use Redirect;
use Validator;
use App\User;
use App\Ticket;
use App\ImageUpload;
use App\Application;
use App\TicketAssignedUser;
use App\Notifications\TicketClosed;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;

class TicketController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->moduleTitleS = 'Ticket';
        $this->moduleTitleP = 'admin.ticket';

        $this->ticket = new Ticket;
        $this->ticketAssignedUser = new TicketAssignedUser;

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

        // dd($input);
        $tickets = $this->ticket->getAdminTickets($input, $noList);

        $businessNames = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();

        // dd($tickets->first()->user->application);

        return view($this->moduleTitleP . '.index', compact('tickets', 'businessNames'));
    }

    public function getTickets()
    {
        $data = $this->ticket->getAdminTickets();

        return \DataTables::of($data)
            ->addColumn('created_at', function ($data) {
                return convertDateToLocal($data->created_at, 'd-m-Y');
            })
            ->addColumn('Status', function ($data) {
                if ($data->status == '0') {
                    return '<span class="badge badge-danger">Pending</span>';
                } elseif ($data->status == '1') {
                    return '<span class="badge badge-warning">In Progress</span>';
                } elseif ($data->status == '2') {
                    return '<span class="badge badge-success">Closed</span>';
                } elseif ($data->status == '3') {
                    return '<span class="badge badge-success">Reopened</span>';
                }
            })
            ->addColumn('department', function ($data) {
                if ($data->department == '1') {
                    return '<span class="badge badge-primary">Technical</span>';
                } elseif ($data->department == '2') {
                    return '<span class="badge badge-danger">Finance</span>';
                } else {
                    return '<span class="badge badge-success">Customer Service</span>';
                }
            })
            ->addColumn('Actions', function ($data) {
                $html = '<a href="' . \URL::route('admin.ticket.show', [$data->id]) . '" class="btn btn-sm btn-icon waves-effect waves-light btn-info"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-icon text-white btn-sm btn-danger waves-effect waves-light remove-record" data-target="#custom-width-modal" data-bs-toggle="modal" data-url="' . \URL::route('admin.ticket.destroy', $data->id) . '" data-id="' . $data->id . '"><i class="fa fa-trash"></i></a>';
                if ($data->status != '2') {
                    $html = $html . ' <a href="' . \URL::route('admin.ticket.close', [$data->id]) . '" class="btn btn-icon btn-sm waves-effect waves-light btn-dark" title="Close Ticket"><i class="fa fa-lock"></i></a>';
                } else {
                    $html = $html . ' <a href="' . \URL::route('admin.ticket.reopen', [$data->id]) . '" class="btn btn-icon btn-sm waves-effect waves-light btn-success" title="Reopen Ticket"><i class="fa fa-unlock"></i></a>';
                }
                return $html;
            })
            ->rawColumns(['Actions', 'Status', 'department'])
            ->make(true);
    }
    public function show($id)
    {
        $ticket = $this->ticket->findData($id);
        if ($ticket->user) {
            return view($this->moduleTitleP . '.show', compact('ticket'));
        }
        return  back()->with('warning', 'There is no user account with this ticket !');
    }

    public function assignUser(Request $request)
    {
        $input = $request->all();
        $input['type'] = 'user';
        if ($this->ticketAssignedUser->storeData($input)) {
            $this->ticket->updateStatus($input['ticket_id'], '1');
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function getTicketUsers(Request $request)
    {
        $users = User::all()->pluck('name', 'id');
        $id = $request->id;
        $assigned_users = $this->ticketAssignedUser->getUsers($id, 'user')->pluck('user_id')->toArray();
        $html = view('partials.ticket-operators', compact('users', 'id', 'assigned_users'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function close($id)
    {
        try {
            if ($this->ticket->updateStatus($id, '3')) {
                try {
                    $ticket = Ticket::find($id);
                    // $notification = [
                    //     'user_id' => $ticket->user_id,
                    //     'sendor_id' => auth()->guard('admin')->user()->id,
                    //     'type' => 'user',
                    //     'title' => $ticket->title . ' - Ticket Closed',
                    //     'body' => 'Ticket Closed by ' . auth()->guard('admin')->user()->name,
                    //     'url' => '/ticket/' . $ticket->id,
                    //     'is_read' => '0'
                    // ];
                    // $realNotification = addNotification($notification);
                    // $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                    // event(new UserNotification($realNotification->toArray()));

                    // $user = User::find($ticket->user_id);
                    // Mail::to($user->email)->send(new \App\Mail\TicketCloseByAdmin($ticket));
                    \Session::put('success', 'Ticket closed successfully.');
                } catch (Exception $e) {
                    \Session::put('error', 'Something Went Wrong. Please try again!');
                }
                return redirect()->route('admin.ticket');
            }
        } catch (Exception $e) {
            \Session::put('error', 'Something Went Wrong. Please try again!');
            return redirect()->back();
        }
    }

    public function reopen($id)
    {
        try {
            if ($this->ticket->updateStatus($id, '2')) {
                $ticket = Ticket::find($id);
                $notification = [
                    'user_id' => $ticket->user_id,
                    'sendor_id' => auth()->guard('admin')->user()->id,
                    'type' => 'user',
                    'title' => $ticket->title . ' - Ticket Reopened',
                    'body' => 'Ticket Reopened by ' . auth()->guard('admin')->user()->name,
                    'url' => '/ticket/' . $ticket->id,
                    'is_read' => '0'
                ];
                $realNotification = addNotification($notification);
                $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                event(new UserNotification($realNotification->toArray()));
                \Session::put('success', 'Ticket reopened successfully.');
                return redirect()->route('admin.ticket');
            }
        } catch (Exception $e) {
            \Session::put('error', 'Something Went Wrong. Please try again!');
            return redirect()->back();
        }
    }

    public function downloadTicketFiles($id, $number)
    {
        $data = Ticket::where('id', $id)
            ->first();

        if ($data == null) {
            notificationMsg('error', 'File not exist.');

            return redirect()->route('admin.ticket');
        }

        $file_array = json_decode($data->files);

        if ($file_array == null) {
            notificationMsg('error', 'File not exist.');

            return redirect()->route('admin.ticket');
        }

        // download file
        if (array_key_exists($number - 1, $file_array)) {

            $request_file = ltrim($file_array[$number - 1], '/');

            if (file_exists($request_file)) {
                return response()->download($request_file);
            }
        }
        // dd($file_array);
        notificationMsg('error', 'File not exist.');

        return redirect()->route('admin.ticket');
        // return response()->download(public_path($request->file));
    }

    public function destroy($id)
    {
        Ticket::find($id)->delete();

        notificationMsg('success', 'Ticket Delete Successfully!');

        return redirect()->route('admin.ticket');
    }
}
