@extends('layouts.agent.default')

@section('title')
    Referral Partner's Report
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / Reports
@endsection
@section('content')
    <style type="text/css">
        .table:not(.table-bordered) thead th {
            vertical-align: top;
        }
    </style>
    <div class="chatbox">
        <div class="chatbox-close"></div>
        <div class="custom-tab-1">
            <a class="nav-link active" data-bs-toggle="tab" href="#Search">Advanced Search</a>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="Search" role="tabpanel">
                    <input type="hidden" class="getUserId" value="{{ request()->get('user_id') }}" />
                    <form method="" id="search-form">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label>Select Company Name</label>
                                    <select name="user_id" data-size="7" data-live-search="true"
                                        class="select2 btn-primary fill_selectbtn_in own_selectbox agnetCompany"
                                        data-width="100%">
                                        <option selected disabled> -- Select here -- </option>
                                        @foreach ($companyName as $cmp)
                                            <option value="{{ $cmp->user_id }}"
                                                {{ isset($_GET['user_id']) && $_GET['user_id'] == $cmp->user_id ? 'selected' : '' }}>
                                                {{ $cmp->business_name }}</option>
                                        @endforeach
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
                                <div class="col-sm-12 mt-4 submit-buttons-commmon">
                                    <button type="submit" class="btn btn-success" id="extraSearch123"> Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="mr-auto pr-3">
                        <h4 class="card-title">Referral Partner's Report</h4>
                    </div>
                    <div class="btn-group mr-2">
                        <button type="button" class="btn btn-warning bell-link btn-sm"> <i class="fa fa-search-plus"></i>
                            Advanced Search</button>
                        <a href="{{ route('rp.rp-report') }}" class="btn btn-danger btn-sm">Reset</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="payout_Report" class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th class="text-center" style="min-width: 125px;">Merchant </th>
                                    <th class="text-center">Currency</th>
                                    <th class="text-center" style="min-width: 170px;">Success Amount </th>
                                    <th class="text-center" style="min-width: 155px;">Success Count </th>
                                    <th class="text-center" style="min-width: 230px;">Commission Percentage</th>
                                    <th class="text-center" style="min-width: 180px;">Total Commission</th>
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
                                                        {{ $_item->user_name }}</td>
                                                @endif
                                                <td class="text-center">{{ $_item->currency }}</td>
                                                <td class="text-center">{{ $_item->successAmount }}</td>
                                                <td class="text-center">{{ $_item->successCount }}</td>
                                                <td class="text-center">{{ $_item->commission }}%</td>
                                                <td class="text-center">
                                                    {{ ($_item->successAmount * $_item->commission) / 100 }}</td>
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
        $(document).ready(function() {
            //select all checkbox for action
            $('body').on('change', '#selectallcheckbox', function() {
                if ($(this).prop("checked") == true) {
                    $('.multicheckmail').prop("checked", true);
                } else if ($(this).prop("checked") == false) {
                    $('.multicheckmail').prop("checked", false);
                }
            });
        });
    </script>
@endsection
