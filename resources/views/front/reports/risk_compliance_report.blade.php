@extends('layouts.user.default')

@section('title')
    Risk/Compliance Report
@endsection

@section('breadcrumbTitle')
    Risk/Compliance Report
@endsection

@section('customeStyle')
    <style type="text/css">
        .bg-success-c {
            background-image: linear-gradient(#709f74a3, #34383E) !important;
        }

        .bg-warning-c {
            background-image: linear-gradient(#ffd956b5, #34383E) !important;
        }

        .bg-danger-c {
            background-image: linear-gradient(#F44336, #3D3D3D) !important;
        }

        .progress-bar {
            background: linear-gradient(to right, rgb(244, 67, 54), rgb(173 79 70));
            color: #FFFFFF;
        }

        .progress {
            background: linear-gradient(to right, rgb(244, 67, 54), rgb(173 79 70));
            box-shadow: rgb(0 0 0 / 70%) 10px 10px 15px -5px, rgb(0 0 0 / 60%) 5px 5px 5px -10px;
        }
    </style>
@endsection

@section('content')
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <form method="" id="search-form" class="form-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advanced Search</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="text">Start Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="start_date"
                                            placeholder="Start Date" id="start_date"
                                            value="{{ isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="text">End Date</label>
                                    <div class="date-input">
                                        <input class="form-control" type="text" name="end_date" placeholder="End Date"
                                            id="end_date"
                                            value="{{ isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : '' }}"
                                            autocomplete="off">
                                    </div>
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
        <div class="col-md-6">
            <h4 class="mt-50">Risk/Compliance Report</h4>
        </div>
        <div class="col-xl-12 col-lg-12">
            <div class="card mt-2">
                <div class="card-header">
                    <div></div>
                    <div>
                        <div class="btn-group box-sh">
                            <button class="btn btn-primary btn-sm searchModelOpen" data-bs-toggle="modal"
                                data-bs-target="#searchModal">
                                Advance Search &nbsp;
                                <svg width="13" height="10" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.7936 6.1856C12.4541 6.18549 13.097 5.97225 13.6266 5.57759C14.1563 5.18292 14.5444 4.62788 14.7334 3.99498H17.0511C17.2835 3.99498 17.5064 3.90266 17.6707 3.73833C17.835 3.574 17.9273 3.35112 17.9273 3.11873C17.9273 2.88633 17.835 2.66345 17.6707 2.49913C17.5064 2.3348 17.2835 2.24248 17.0511 2.24248H14.7334C14.5441 1.60989 14.1558 1.05524 13.6262 0.660909C13.0966 0.266574 12.4539 0.0535889 11.7936 0.0535889C11.1333 0.0535889 10.4906 0.266574 9.96099 0.660909C9.43137 1.05524 9.04308 1.60989 8.85378 2.24248H1.27859C1.0462 2.24248 0.82332 2.3348 0.658991 2.49913C0.494663 2.66345 0.402344 2.88633 0.402344 3.11873C0.402344 3.35112 0.494663 3.574 0.658991 3.73833C0.82332 3.90266 1.0462 3.99498 1.27859 3.99498H8.85378C9.04276 4.62788 9.43093 5.18292 9.96057 5.57759C10.4902 5.97225 11.1331 6.18549 11.7936 6.1856ZM1.27859 11.005C1.0462 11.005 0.82332 11.0973 0.658991 11.2616C0.494663 11.426 0.402344 11.6488 0.402344 11.8812C0.402344 12.1136 0.494663 12.3365 0.658991 12.5008C0.82332 12.6652 1.0462 12.7575 1.27859 12.7575H3.15815C3.34745 13.3901 3.73575 13.9447 4.26536 14.339C4.79498 14.7334 5.43767 14.9464 6.09797 14.9464C6.75827 14.9464 7.40096 14.7334 7.93057 14.339C8.46019 13.9447 8.84849 13.3901 9.03779 12.7575H17.0511C17.2835 12.7575 17.5064 12.6652 17.6707 12.5008C17.835 12.3365 17.9273 12.1136 17.9273 11.8812C17.9273 11.6488 17.835 11.426 17.6707 11.2616C17.5064 11.0973 17.2835 11.005 17.0511 11.005H9.03779C8.84849 10.3724 8.46019 9.81775 7.93057 9.42341C7.40096 9.02907 6.75827 8.81609 6.09797 8.81609C5.43767 8.81609 4.79498 9.02907 4.26536 9.42341C3.73575 9.81775 3.34745 10.3724 3.15815 11.005H1.27859Z"
                                        fill="#FFFFFF" />
                                </svg>
                            </button>
                            <a href="{{ route('risk-compliance-report') }}" class="btn btn-danger btn-sm"
                                style="border-radius: 0px 5px 5px 0px !important;">Reset</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar {{ $data['text'] == 'Low' ? 'bg-success-c' : '' }} {{ $data['text'] == 'Mid' ? 'bg-warning-c' : '' }} {{ $data['text'] == 'High' ? 'bg-danger-c' : '' }} progress-bar-animated"
                            role="progressbar" style="width: 33.3%" aria-valuenow="15" aria-valuemin="0"
                            aria-valuemax="100">{{ $data['text'] == 'Low' ? 'Low Risk' : '' }}</div>
                        <div class="progress-bar  {{ $data['text'] == 'Mid' ? 'bg-warning-c' : '' }} {{ $data['text'] == 'High' ? 'bg-danger-c' : '' }}"
                            role="progressbar" style="width:33.3%"
                            aria-valuenow="{{ $data['text'] == 'Mid' || $data['text'] == 'High' ? '100' : '0' }}"
                            aria-valuemin="0" aria-valuemax="100">{{ $data['text'] != 'High' ? 'Mid Risk' : '' }}</div>
                        <div class="progress-bar {{ $data['text'] == 'High' ? 'bg-danger-c' : '' }}" role="progressbar"
                            style="width: 33.3%;" aria-valuenow="{{ $data['text'] == 'High' ? '100' : '0' }}"
                            aria-valuemin="0" aria-valuemax="100">{{ $data['text'] == 'High' ? 'High Risk' : '' }}</div>
                    </div>
                    <h5 class="pull-right mt-2"><span class="text-danger">
                            @if (isset($_GET['start_date']))
                                {{ date('Y-m-d', strtotime($_GET['start_date'])) }}
                            @else
                                {{ date('Y-m-d', strtotime('-30 days')) }}
                                @endif To @if (isset($_GET['end_date']))
                                    {{ date('Y-m-d', strtotime($_GET['end_date'])) }}
                                @else
                                    {{ date('Y-m-d') }}
                                @endif
                        </span>
                        @if (!isset($_GET['start_date']) && !isset($_GET['end_date']))
                            <span class="text-center">(Last 30 days)</span>
                        @endif
                    </h5>
                    <div class="table-responsive custom-table mt-5 tableFixHead">
                        <table class="table table-borderless table-striped ">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-center">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Chargeback</td>
                                    <td class="text-center">{{ $data['chargebacks']->chargebacks_count ?? '0' }}</td>
                                    <td class="text-center">{{ $data['chargebacks_percentage'] }}%</td>
                                </tr>
                                <tr>
                                    <td>Suspicious</td>
                                    <td class="text-center">{{ $data['flagged']->flagged_count ?? '0' }}</td>
                                    <td class="text-center">{{ $data['flagged_percentage'] }}%</td>
                                </tr>
                                <tr>
                                    <td>Refunds</td>
                                    <td class="text-center">{{ $data['refund']->refund_count ?? '0' }}</td>
                                    <td class="text-center">{{ $data['refund_percentage'] }}%</td>
                                </tr>
                                <tr>
                                    <td>Retrievals</td>
                                    <td class="text-center">{{ $data['retrieval']->retrieval_count ?? '0' }}</td>
                                    <td class="text-center">{{ $data['retrieval_percentage'] }}%</td>
                                </tr>
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
            var height = $(window).height();
            height = height - 300;
            $('.tableFixHead').css('height', height + 'px');
        });
    </script>
@endsection
