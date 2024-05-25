<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PayoutSchedule;

class PayoutScheduleController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->payoutSchedule = new PayoutSchedule;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data = $this->payoutSchedule->getData();
        $data = $this->payoutSchedule->autoGeneratePayoutSchedule();

        return view('admin.payoutSchedule.index', compact('data'));
    }

    public function getPayoutSchedule()
    {
        $data = $this->payoutSchedule->getData();

        return \DataTables::of($data)
            ->addColumn('from_date', function ($row) {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $row->from_date)->format('d/m/Y');
            })
            ->addColumn('to_date', function ($row) {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $row->to_date)->format('d/m/Y');
            })
            ->addColumn('issue_date', function ($row) {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $row->issue_date)->format('d/m/Y');
            })
            ->addColumn('Actions', function ($data) {
                return '<a href=' . \URL::route('payout-schedule-admin.edit', $data->id) . ' class="btn btn-sm btn-success"><i class="fa fa-edit"></i></a>
                        <a class="btn btn-icon btn-danger btn-sm mb-0 remove-record" data-target="#custom-width-modal" data-bs-toggle="modal" data-url="' . \URL::route('payout-schedule-admin.destroy', $data->id) . '" data-id="' . $data->id . '"><i class="fa fa-trash"></i></a>';
            })

            ->rawColumns(['Actions', 'from_date', 'issue_date', 'to_date'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payoutSchedule.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
            'issue_date' => 'required',
            'sequence_number' => 'required',
        ]);

        $input = $request->all();
        if (isset($input['from_date'])) {
            $input['from_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input['from_date'])->format('Y-m-d');
        }

        if (isset($input['to_date'])) {
            $input['to_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input['to_date'])->format('Y-m-d');
        }

        if (isset($input['issue_date'])) {
            $input['issue_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input['issue_date'])->format('Y-m-d');
        }

        PayoutSchedule::create($input);

        notificationMsg('success', 'Payout Schedule created successfully.');

        return redirect()->route('payout-schedule.index');
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
        $data = PayoutSchedule::find($id);
        return view('admin.payoutSchedule.edit', compact('data'));
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
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
            'issue_date' => 'required',
            'sequence_number' => 'required',
        ]);

        $input = $request->all();

        if (isset($input['from_date'])) {
            $input['from_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input['from_date'])->format('Y-m-d');
        }

        if (isset($input['to_date'])) {
            $input['to_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input['to_date'])->format('Y-m-d');
        }

        if (isset($input['issue_date'])) {
            $input['issue_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $input['issue_date'])->format('Y-m-d');
        }

        $this->payoutSchedule->updateData($id, $input);

        notificationMsg('success', 'Payout Schedule updated successfully.');

        return redirect()->route('payout-schedule.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PayoutSchedule::where('id', $id)->delete();

        notificationMsg('success', 'Payout Schedule Deleted Successfully!');

        return redirect()->route('payout-schedule.index');
    }
}
