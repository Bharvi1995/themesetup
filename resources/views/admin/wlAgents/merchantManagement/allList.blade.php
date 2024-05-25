@extends('layouts.admin.default')

@section('title')
    Merchant List Of White Label RP
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Merchant List Of White Label RP
@endsection
@section('customeStyle')
    <link rel="stylesheet" href="{{ storage_asset('themeAdmin/custom_css/sweetalert2.min.css') }}" />
@endsection

@section('content')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form method="" id="search-form" class="form-dark">
                    <div class="modal-body">
                        @if (isset($_GET) && $_GET != '')
                            @foreach ($_GET as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        @endif
                        <div class="basic-form">
                            <div class="row ">
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
                                    <label for="">Main MID</label>
                                    <select class="select2" name="payment_gateway_id" data-size="7" data-live-search="true"
                                        data-title="Select Main MID" id="payment_gateway_id" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($payment_gateway_id as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($_GET['payment_gateway_id']) && $_GET['payment_gateway_id'] == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">Category</label>
                                    <select class="select2" name="category" data-size="7" data-live-search="true"
                                        data-title="Select Category" id="category" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ isset($_GET['category']) && $_GET['category'] == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="">White Label RP</label>
                                    <select class="select2" name="wl_agents" data-size="7" data-live-search="true"
                                        data-title="Select RP" id="wl_agents" data-width="100%">
                                        <option selected disabled>Select here</option>
                                        @foreach ($WLAgent as $rp)
                                            <option value="{{ $rp->id }}"
                                                {{ isset($_GET['wl_agents']) && $_GET['wl_agents'] == $rp->id ? 'selected' : '' }}>
                                                {{ $rp->name }}
                                            </option>
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
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <h4 class="card-title">Merchant List Of White Label RP</h4>
        </div>
        <div class="col-lg-6 text-right">
            @if (auth()->guard('admin')->user()->can(['export-wl-rp-merchant']))
                <?php
                $url = Request::fullUrl();
                $parsedUrl = parse_url($url);
                $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
                $subQueryString = $query != '' ? $query . '&type=xlsx' : '';
                ?>

                @if (!empty($subQueryString))
                    <a href="{{ route('wl-agent-merchant-all-csv-export', [$subQueryString]) }}"
                        class="btn btn-primary btn-sm" id="ExcelLink"><i class="fa fa-download"></i> Export Excel
                    </a>
                @else
                    <a href="{{ route('wl-agent-merchant-all-csv-export') }}" class="btn btn-primary btn-sm"
                        id="ExcelLink">
                        <i class="fa fa-download"></i>
                        Export Excel
                    </a>
                @endif
            @endif
            @if (auth()->guard('admin')->user()->can(['delete-wl-rp-merchant']))
                <button type="button" class="btn btn-danger btn-shadow btn-sm" id="deleteSelected"
                    data-link="{{ route('delete.wl.agent.merchant') }}">
                    <i class="fa fa-trash"></i> Delete Selected Record
                </button>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div>
                    </div>
                    <div>
                        <form style="float:left;" class="me-50 form-dark" id="noListform" method="GET">
                            <select class="form-control-sm form-control" name="noList" id="noList">
                                <option value="">--No of Records--</option>
                                <option value="30" {{ request()->get('noList') == '30' ? 'selected' : '' }}>30
                                </option>
                                <option value="50" {{ request()->get('noList') == '50' ? 'selected' : '' }}>50
                                </option>
                                <option value="100" {{ request()->get('noList') == '100' ? 'selected' : '' }}>100
                                </option>
                            </select>
                        </form>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-primary  btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg></button>
                            <a href="{!! route('wl-agent-merchant-all') !!}" class="btn btn-danger btn-sm"
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
                                        <div class="custom-control form-check custom-control-inline mr-0">
                                            <input class="form-check-input" id="selectallcheckbox" name=""
                                                type="checkbox">
                                            <label class="form-check-label" for="selectallcheckbox"></label>
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
                                @if (count($dataT) > 0)
                                    @foreach ($dataT as $key => $data)
                                        <tr>
                                            <td>
                                                <div class="custom-control form-check custom-control-inline mr-0">
                                                    <input type="checkbox"
                                                        class="form-check-input multicheckmail multidelete"
                                                        name="multicheckmail[]" id="{{ $data->id }}_checkbox"
                                                        value="{{ $data->id }}">
                                                    <label class="form-check-label"
                                                        for="{{ $data->id }}_checkbox"></label>
                                                </div>
                                            </td>

                                            <td>
                                                {{ $data->business_name }}
                                                <br><label class="badge badge-danger badge-sm">WL RP -
                                                    {{ getWLAgentName($data->white_label_agent_id) }}</label>
                                            </td>
                                            <td>{{ $data->name }}<br><label
                                                    class="badge badge-primary badge-sm">{{ $data->bank_name }}</label>
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
                                                    <button type="button"
                                                        class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                        data-bs-toggle="dropdown">
                                                        <svg width="5" height="17" viewBox="0 0 5 17"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                        @if (auth()->guard('admin')->user()->can(['update-wl-rp-merchant']))
                                                            <a href="{{ \URL::route('wl-agent-merchant-edit', $data->id) }}"
                                                                class="dropdown-item"><i
                                                                    class="fa fa-edit text-primary me-2"></i>
                                                                Edit</a>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['delete-wl-rp-merchant']))
                                                            <a href="javascript:void(0)"
                                                                class="dropdown-item delete_modal"
                                                                data-url="{!! URL::route('wl-agent-merchant-destroy', $data->id) !!}"
                                                                data-id="{{ $data->id }}"><i
                                                                    class="fa fa-trash text-danger me-2"></i>
                                                                Delete</a>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['view-wl-rp-merchant']))
                                                            <a href="{{ \URL::route('wl-agent-merchant-show', $data->id) }}"
                                                                class="dropdown-item"><i
                                                                    class="fa fa-eye text-success me-2"></i>
                                                                View</a>
                                                        @endif
                                                        @if (auth()->guard('admin')->user()->can(['update-wl-rp-merchant']))
                                                            <a href="{{ \URL::route('assign-mid', $data->id) }}"
                                                                title="Assign MID" class="dropdown-item"><i
                                                                    class="fa fa-cog text-info me-2"></i>
                                                                Assign MID</a>
                                                        @endif
                                                    </div>
                                                </div>
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
                    <div class="pagination-wrap">
                        {!! $dataT->appends($_GET)->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on("change", "#noList", function() {
                var url = new URL(window.location.href);
                if (url.search) {
                    if (url.searchParams.has("noList")) {
                        url.searchParams.set("noList", $(this).val());
                        location.href = url.href;
                    } else {
                        var newUrl = url.href + "&noList=" + $(this).val();
                        location.href = newUrl;
                    }
                } else {
                    document.getElementById("noListform").submit();
                }
            });
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
