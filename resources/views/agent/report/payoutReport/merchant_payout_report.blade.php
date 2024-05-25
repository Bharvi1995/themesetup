@extends('layouts.agent.default')
@section('title')
    Payout Reports
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / Payout Reports List
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Payout Reports List</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Company Name</th>
                                    <th>From Date</th>
                                    <th>To Date </th>
                                    <th>Status</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $value)
                                    <tr>
                                        <td>
                                            <div class="dropdown ml-auto">
                                                <a href="#" class="btn btn-primary sharp btn-sm rounded-pill"
                                                    data-bs-toggle="dropdown" aria-expanded="true"><svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                                                        height="18px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24"
                                                                height="24"></rect>
                                                            <circle fill="#FFF" cx="5" cy="12"
                                                                r="2"></circle>
                                                            <circle fill="#FFF" cx="12" cy="12"
                                                                r="2"></circle>
                                                            <circle fill="#FFF" cx="19" cy="12"
                                                                r="2"></circle>
                                                        </g>
                                                    </svg></a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li class="dropdown-item"><a
                                                            href="{{ route('rp.merchant_payout_report.show', $value->id) }}"
                                                            target="_blank" class="dropdown-item"><i
                                                                class="fa fa-eye text-primary mr-2"></i>Show</a></li>
                                                    <li class="dropdown-item"><a
                                                            href="{{ route('rp.merchant_payout_report.pdf', $value->id) }}"
                                                            class="dropdown-item"><i
                                                                class="fa fa-file-pdf-o text-warning mr-2"></i>Pdf</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>{{ $value->company_name }}</td>
                                        <td>{{ $value->start_date }}</td>
                                        <td>{{ $value->end_date }}</td>
                                        <td>
                                            @if ($value->status == 1)
                                                <span class="badge badge-primary">Paid</span>
                                            @else
                                                <span class="badge badge-danger">UnPaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <?php
                                    if($value->files == null || $value->files == '') {
                                        $files = 0;
                                        $count = 0;
                                    } else {
                                        $files = json_decode($value->files);
                                        $count = count($files);
                                    }
                                    if($count > 0){
                                        for($i=0;$i<$count;$i++){
                                            ?>
                                            <li style="list-style: none;"><a target="_blank"
                                                    href="{{ getS3Url($files[$i]) }}"> <i
                                                        class="fa fa-file text-info"></i></a></li>
                                            <?php
                                        }
                                    }else{
                                        echo '<span class="badge badge-primary">N/A</span>';
                                    }
                                    ?>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <script src="{{ storage_asset('themeAdmin/custom_js/common.js') }}"></script>
    <script type="text/javascript">
        $("#resetForm").click(function() {
            $('#search-form').find("input[type=text], input[type=email], input[type=number], select").val("");
            $(".select2").val('first').trigger('change.select2');
        });
    </script>
@endsection
