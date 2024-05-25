@extends('layouts.user.default')

@section('title')
    Helpdesk
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / Helpdesk
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4 class="mt-50">Helpdesk List</h4>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('ticket.create') }}" class="btn btn-primary">Create Helpdesk</a>
        </div>
        <div class="col-xl-12 col-xxl-12">
            <div class="card mt-2">
                <div class="card-header">
                    <div></div>
                    <div>
                        <form id="noListform" method="GET" style="float:left;" class="me-50 form-dark">
                            <select class="form-control form-control-sm" name="noList" id="noList">
                                <option value="">No of Records</option>
                                <option value="30" {{ request()->get('noList') == '30' ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request()->get('noList') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request()->get('noList') == '100' ? 'selected' : '' }}>100
                                </option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Unique Number</th>
                                    <th>Description</th>
                                    <th>Date created</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($data) && $data->count())
                                    @foreach ($data as $ticket)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $ticket->title }}</td>
                                            <td>{{ Str::limit($ticket->body, 20) }}</td>
                                            <td>{{ convertDateToLocal($ticket->created_at, 'd-m-Y') }}</td>
                                            <td>
                                                @if ($ticket->status == '0')
                                                    <span class="badge badge-sm badge-danger">Pending</span>
                                                @elseif($ticket->status == '1')
                                                    <span class="badge badge-sm badge-warning">In Progress</span>
                                                @elseif($ticket->status == '3')
                                                    <span class="badge badge-sm badge-danger">Closed</span>
                                                @elseif($ticket->status == '2')
                                                    <span class="badge badge-sm badge-success">Reopened</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">
                                            <p class="text-center"><strong>No record found</strong></p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    @if (!empty($data) && $data->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $data->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of total {{ $data->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
