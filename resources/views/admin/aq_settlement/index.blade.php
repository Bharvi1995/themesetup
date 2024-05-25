@extends('layouts.admin.default')

@section('title')
    AQ Settlement
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / AQ Settlement
@endsection
@section('customeStyle')
    <style>
        .copyToClipboard,
        .changeStatus {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    @include('requestDate')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog modal-lg-scrollable" role="document">
            <form method="" id="search-form" class="form-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label for="name">AQ Name</label>
                                    <select name="middetail_id" class="form-control select2">
                                        <option value="">-- Select AQ --</option>
                                        @foreach ($mids as $mid)
                                            <option value="{{ $mid->id }}"
                                                {{ request('middetail_id') == $mid->id ? 'selected' : '' }}>
                                                {{ $mid->bank_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">From Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="from_date"
                                            placeholder="Select Date.." id="from_date"
                                            value="{{ isset($_GET['from_date']) && $_GET['from_date'] != '' ? $_GET['from_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">To Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="to_date"
                                            placeholder="Select Date.." id="to_date"
                                            value="{{ isset($_GET['to_date']) && $_GET['to_date'] != '' ? $_GET['to_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label>Hash</label>
                                    <input type="text" name="txn_hash" value="{{ request('txn_hash') }}"
                                        class="form-control" placeholder="Enter here.." />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
                        <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header ">
                    <h4>RM List</h4>
                    <div>
                        <div class="btn-group">
                            <button class="btn btn-primary btn-sm searchModelOpen" data-bs-toggle="modal"
                                data-bs-target="#searchModal">
                                Advance Search &nbsp;
                                <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#00C2FF" />
                                </svg>
                            </button>
                            <a href="{{ route('aq-settlement.index') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                        <a href="{{ route('aq-settlement.create') }}"><button
                                class="btn btn-sm btn-success ">Create</button></a>
                        <a href="{{ route('aq-settlement.export') }}" class="btn btn-sm btn-warning text-white ">Export
                            Excel</a>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <tr>
                                <th>Aq Name</th>
                                <th>from Date</th>
                                <th>To Date</th>
                                <th>Transaction Hash</th>
                                <th>Paid Date</th>
                                <th>Payment Receipt</th>
                                <th>Action</th>
                            </tr>
                            <tbody>
                                @foreach ($settlements as $item)
                                    <tr>
                                        <td>{{ $item->mid->bank_name }}</td>
                                        <td>{{ $item->from_date->format('d-m-Y') }}</td>
                                        <td>{{ $item->to_date->format('d-m-Y') }}</td>
                                        <td><span class="badge bg-success">{{ $item->txn_hash }}</span> </td>
                                        <td>{{ $item->paid_date ? $item->paid_date->format('d-m-Y') : $item->created_at->format('d-m-Y') }}
                                        </td>
                                        <td><a href="{{ getS3Url($item->payment_receipt) }}" target="_blank"><span
                                                    class="badge badge-sm bg-warning ">View</span> </a></td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                    data-bs-toggle="dropdown">
                                                    <svg width="5" height="17" viewBox="0 0 5 17" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.36328 4.69507C1.25871 4.69507 0.363281 3.79964 0.363281 2.69507C0.363281 1.5905 1.25871 0.695068 2.36328 0.695068C3.46785 0.695068 4.36328 1.5905 4.36328 2.69507C4.36328 3.79964 3.46785 4.69507 2.36328 4.69507Z"
                                                            fill="#B3ADAD" />
                                                        <path
                                                            d="M2.36328 10.6951C1.25871 10.6951 0.363281 9.79964 0.363281 8.69507C0.363281 7.5905 1.25871 6.69507 2.36328 6.69507C3.46785 6.69507 4.36328 7.5905 4.36328 8.69507C4.36328 9.79964 3.46785 10.6951 2.36328 10.6951Z"
                                                            fill="#B3ADAD" />
                                                        <path
                                                            d="M2.36328 16.6951C1.25871 16.6951 0.363281 15.7996 0.363281 14.6951C0.363281 13.5905 1.25871 12.6951 2.36328 12.6951C3.46785 12.6951 4.36328 13.5905 4.36328 14.6951C4.36328 15.7996 3.46785 16.6951 2.36328 16.6951Z"
                                                            fill="#B3ADAD" />
                                                    </svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route('aq-settlement.edit', $item->id) }}"
                                                        class="dropdown-item">Edit</a>
                                                    <a href="" class="dropdown-item delete_modal"
                                                        data-bs-toggle="modal" data-bs-target="#delete_modal"
                                                        data-url="{{ route('aq-settlement.destroy', $item->id) }}"
                                                        data-id="{{ $item->id }}">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>
                <div class="card-footer">
                    @if (!empty($settlements) && $settlements->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $settlements->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $settlements->firstItem() }} to {{ $settlements->lastItem() }} of total
                                {{ $settlements->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
