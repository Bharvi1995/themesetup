@extends('layouts.admin.default')

@section('title')
    Referral Partner's Report
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Referral Partner's Report
@endsection
@section('customeStyle')
    <style type="text/css">
        .table:not(.table-bordered) thead th {
            vertical-align: top;
        }
    </style>
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
                        <div class="basic-form">
                            <div class="row ">
                                <div class="form-group col-lg-6">
                                    <label>Select Referral Partner Name</label>
                                    <select name="agent_id" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox" data-width="100%"
                                        onchange="getAgentId(this.value , null)">
                                        <option selected disabled> -- Select here -- </option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}"
                                                {{ isset($_GET['agent_id']) && $_GET['agent_id'] == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Select Company Name</label>
                                    <select name="user_id" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox agnetCompany"
                                        data-width="100%">
                                        <option selected disabled> -- Select here -- </option>

                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="text">Select Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Enter here..." id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                    @if ($errors->has('start_date'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('start_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="end_date">End Date</label>
                                    <div class="date-input">
                                        <input type="text" id="end_date" class="form-control"
                                            placeholder="Enter here..." name="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                    @if ($errors->has('end_date'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
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
        <div class="col-xl-12 col-xxl-12">
            <div class="card  mt-1">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Referral Partner's Report</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#searchModal"> Advanced
                                Search &nbsp; <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg>
                            </button>
                            <a href="{{ route('agent-report') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive custom-table">
                        <table id="payout_Report" class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th class="text-center" style="min-width: 170px;">Referral Partner's <br> Name </th>
                                    <th class="text-center" style="min-width: 125px;">Merchant </th>
                                    <th class="text-center">Currency</th>
                                    <th class="text-center" style="min-width: 230px;">Master Commission Percentage</th>
                                    <th class="text-center" style="min-width: 170px;">Master Success Amount </th>
                                    <th class="text-center" style="min-width: 155px;">Master Success Count </th>
                                    <th class="text-center" style="min-width: 180px;">Master Total Commission</th>
                                    <th class="text-center" style="min-width: 230px;">VISA Commission Percentage</th>
                                    <th class="text-center" style="min-width: 170px;">VISA Success Amount </th>
                                    <th class="text-center" style="min-width: 155px;">VISA Success Count </th>
                                    <th class="text-center" style="min-width: 180px;">VISA Total Commission</th>
                                    <th class="text-center" style="min-width: 230px;">Amex Commission Percentage</th>
                                    <th class="text-center" style="min-width: 170px;">Amex Success Amount </th>
                                    <th class="text-center" style="min-width: 155px;">Amex Success Count </th>
                                    <th class="text-center" style="min-width: 180px;">Amex Total Commission</th>
                                    <th class="text-center" style="min-width: 230px;">Discover Commission Percentage</th>
                                    <th class="text-center" style="min-width: 170px;">Discover Success Amount </th>
                                    <th class="text-center" style="min-width: 155px;">Discover Success Count </th>
                                    <th class="text-center" style="min-width: 180px;">Discover Total Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($arr_t_data) > 0)
                                    @foreach ($arr_t_data as $item)
                                        <?php $rowspan = count((array) $item); ?>
                                        @foreach ($item as $k => $_item)
                                            <tr>
                                                @if ($k == 0)
                                                    <td rowspan="{{ $rowspan }}" class="text-center">
                                                        {{ $_item->agent_name }}</td>
                                                    <td rowspan="{{ $rowspan }}" class="text-center">
                                                        {{ $_item->user_name }}</td>
                                                @endif
                                                <td class="text-center">{{ $_item->currency }}</td>
                                                <td class="text-center">{{ $_item->master_commission }}%</td>
                                                <td class="text-center">{{ $_item->MasterSuccessAmount }}</td>
                                                <td class="text-center">{{ $_item->MasterSuccessCount }}</td>
                                                <td class="text-center">
                                                    {{ round($_item->MasterSuccessAmount * ($_item->master_commission / 100), 2) }}
                                                </td>
                                                <td class="text-center">{{ $_item->commission }}%</td>
                                                <td class="text-center">{{ $_item->OtherSuccessAmount }}</td>
                                                <td class="text-center">{{ $_item->OtherSuccessCount }}</td>
                                                <td class="text-center">
                                                    {{ round($_item->OtherSuccessAmount * ($_item->commission / 100), 2) }}
                                                </td>
                                                <td class="text-center">{{ $_item->amex_commission }}%</td>
                                                <td class="text-center">{{ $_item->AmexSuccessAmount }}</td>
                                                <td class="text-center">{{ $_item->AmexSuccessCount }}</td>
                                                <td class="text-center">
                                                    {{ round($_item->AmexSuccessAmount * ($_item->amex_commission / 100), 2) }}
                                                </td>
                                                <td class="text-center">{{ $_item->discover_commission }}%</td>
                                                <td class="text-center">{{ $_item->DiscoverSuccessAmount }}</td>
                                                <td class="text-center">{{ $_item->DiscoverSuccessCount }}</td>
                                                <td class="text-center">
                                                    {{ round($_item->DiscoverSuccessAmount * ($_item->discover_commission / 100), 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="7">No record found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('customScript')
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            //select all checkbox for action
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multicheckmail').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multicheckmail').prop("checked", false);
                }
            });

            var id = $('.getAgentId').val();
            var userId = $('.getUserId').val();
            if (id) {
                getAgentId(id, userId)
            }
        });

        function getAgentId(id, userId) {
            $.ajax({
                type: 'POST',
                url: "{{ route('agent.company') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(res) {
                    if (res.status == 200) {
                        var html = ``;
                        html += '<option selected disabled> -- Select here -- </option>';
                        res.companyName.forEach(function(item, index) {
                            html +=
                                `<option value="${item.user_id}" ${userId && item.user_id == userId ? 'selected' :''}> ${item.business_name}</option>`
                        });

                        $('.agnetCompany').empty().append(html)
                    }
                }
            });
        }
    </script>
@endsection
