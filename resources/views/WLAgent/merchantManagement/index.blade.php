@extends($WLAgentUserTheme)

@section('title')
    Merchant Management
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('wl-dashboard') }}">Dashboard</a> / Merchant Management
@endsection

@section('content')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <form method="" id="search-form" class="form-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body">
                        @if (isset($_GET) && $_GET != '')
                            @foreach ($_GET as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        @endif
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label for="">Email</label>
                                    <input class="form-control" name="email" type="email" placeholder="Enter here"
                                        value="{{ isset($_GET['email']) && $_GET['email'] != '' ? $_GET['email'] : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Company</label>
                                    <select class="select2" name="company" data-size="7" data-live-search="true"
                                        data-title="Select here" id="company" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($companyName as $company)
                                            <option value="{{ $company->business_name }}"
                                                {{ isset($_GET['company']) && $_GET['company'] == $company->business_name ? 'selected' : '' }}>
                                                {{ $company->business_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Website</label>
                                    <input class="form-control" name="website" type="text" placeholder="Enter here"
                                        value="{{ isset($_GET['website']) && $_GET['website'] != '' ? $_GET['website'] : '' }}">
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
        <div class="col-lg-6">
            <h4 class="me-50">Merchant Management</h4>
        </div>
        <div class="col-lg-6 text-right">
            <a href="{{ route('wl-merchant-create') }}" class="btn btn-primary  btn-sm" id="new_merchant"><i
                    class="fa fa-plus"></i> Create Merchant </a>
            <a href="{{ route('wl-merchant-export', request()->all()) }}" class="ms-2 btn btn-primary btn-sm"><i
                    class="fa fa-download mr-2"></i> Export Excel </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-1">
                <div class="card-header">
                    <div></div>
                    <div>
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg>
                            </button>
                            <a href="{{ url('wl/rp/merchant-management') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>

                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="common-check-main">
                                            <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                                <input type="checkbox" class="form-check-input" id="selectallcheckbox">
                                                <label class="custom-control-label" for="selectallcheckbox"></label>
                                            </div>
                                        </div>
                                    </th>
                                    <th>Business Name</th>
                                    <th>Merchant Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Creation Date</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataT as $key => $data)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                                <input type="checkbox" class="form-check-input multicheckmail"
                                                    name="multicheckmail[]" id="customCheckBox_{{ $data->id }}"
                                                    value="{{ $data->id }}" required="">
                                                <label class="custom-control-label"
                                                    for="customCheckBox_{{ $data->id }}"></label>
                                            </div>
                                        </td>

                                        <td>
                                            {{ $data->business_name }}
                                        </td>
                                        <td>{{ $data->name }}
                                        </td>
                                        <td>{{ $data->email }}</td>
                                        <td>
                                            +{{ $data->country_code }} {{ $data->mobile_no }}
                                        </td>
                                        <td>
                                            {{ convertDateToLocal($data->created_at, 'd-m-Y') }}
                                        </td>
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
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ \URL::route('wl-merchant-edit', $data->id) }}"
                                                        class="dropdown-item"><i class="fa fa-edit text-primary me-2"></i>
                                                        Edit
                                                    </a>


                                                    <a href="javascript:void(0)" class="dropdown-item delete_modal"
                                                        data-url="{!! URL::route('wl-merchant-destroy', $data->id) !!}"
                                                        data-id="{{ $data->id }}"><i
                                                            class="fa fa-trash text-danger me-2"></i>
                                                        Delete
                                                    </a>


                                                    <a href="{{ \URL::route('wl-merchant-show', $data->id) }}"
                                                        class="dropdown-item"><i class="fa fa-eye text-success me-2"></i>
                                                        View
                                                    </a>

                                                    <a href="" class="user-show dropdown-item"
                                                        data-bs-toggle="modal" data-id="{{ $data->id }}"
                                                        data-bs-target="#user_list"><i
                                                            class="fa fa-tasks text-info me-2"></i>
                                                        Action
                                                    </a>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    @if (!empty($dataT) && $dataT->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $dataT->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $dataT->firstItem() }} to {{ $dataT->lastItem() }} of total
                                {{ $dataT->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal right fade" id="user_list" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Details </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body" id="userDetailsContent">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('customScript')
    <script type="text/javascript">
        // show user details
        $('body').on('click', '.user-show', function() {
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: '{{ route('show-wl-user-details') }}',
                data: {
                    'id': id,
                    '_token': "{{ csrf_token() }}"
                },
                context: $(this),
                beforeSend: function() {
                    $('#userDetailsContent').html(
                        '<i class="fa fa-spinner fa-spin"></i>  Please Wait...');
                },
                success: function(data) {
                    $('#userDetailsContent').html(data.html);
                },
            });
        });

        $('body').on('change', 'input[name="isBinRemove"]', function() {
            var id = $(this).data('id');
            var is_bin_remove = '0';

            // change the value based on check / uncheck
            if ($(this).prop("checked") == true) {
                var is_bin_remove = '1';
            }
            $.ajax({
                type: 'POST',
                context: $(this),
                url: '{{ route('user-bin-remove') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'is_bin_remove': is_bin_remove,
                    'id': id
                },
                success: function(data) {
                    if (data.success == true) {
                        toastr.success('Merchant BIN remove changed successfully!!');
                    } else {
                        toastr.error('Something went wrong!!');
                    }
                },
            });
        })
        $(document).ready(function() {
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multicheckmail').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multicheckmail').prop("checked", false);
                }
            });
        });
    </script>

    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
@endsection
