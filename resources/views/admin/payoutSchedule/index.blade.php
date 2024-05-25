@extends('layouts.admin.default')
@section('title')
    Payout Schedule
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Payout Schedule
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Payout Schedule</h4>
                    </div>
                    {{-- <a href="{{ route('payout-schedule.create') }}" class="btn btn-primary btn-sm">
               Generate New Payout Schedule</a> --}}
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Issue Date</th>
                                    {{-- <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @if (count($data) > 0) --}}
                                @foreach ($data as $key => $payout)
                                    <tr>
                                        <th>{{ $key + 1 }}</th>
                                        <td>{{ $payout['from_date'] }}</td>
                                        <td>{{ $payout['to_date'] }}</td>
                                        <td>{{ $payout['issue_date'] }}</td>
                                        {{-- <td class="w-15">
                          <div class="dropdown">
                             <a href="#" class="btn btn-primary sharp rounded-pill" data-bs-toggle="dropdown"
                                aria-expanded="true"><svg xmlns="http://www.w3.org/2000/svg"
                                   xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                   viewBox="0 0 24 24" version="1.1">
                                   <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                      <rect x="0" y="0" width="24" height="24"></rect>
                                      <circle fill="#FFF" cx="5" cy="12" r="2"></circle>
                                      <circle fill="#FFF" cx="12" cy="12" r="2"></circle>
                                      <circle fill="#FFF" cx="19" cy="12" r="2"></circle>
                                   </g>
                                </svg></a>
                             <ul class="dropdown-menu dropdown-menu-end">
                                <li class="dropdown-item"><a
                                      href="{!! URL::route('payout-schedule.edit',$payout->id) !!}"
                                      class="dropdown-item"><i class="fa fa-edit text-primary me-2"></i> Edit</a></li>
                                <li class="dropdown-item">
                                   <a href="" class="dropdown-item delete_modal" data-bs-toggle="modal"
                                      data-bs-target="#delete_modal"
                                      data-url="{{ URL::route('payout-schedule.destroy', $payout->id)}}"
                                      data-id="{{ $payout->id }}"><i class="fa fa-trash text-danger me-2"></i>
                                      Delete</a>
                                </li>   
                             </ul>
                          </div>
                       </td> --}}
                                    </tr>
                                @endforeach
                                {{-- @endif --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
