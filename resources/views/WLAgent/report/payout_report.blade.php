@extends($WLAgentUserTheme)
@section('title')
    Transactions List
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('dashboardPage') }}">Dashboard</a> / Generated Reports
@endsection

@section('content')
    @include('requestDate')

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Generated Reports List</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-ellipsis-h"></i></th>
                                    <th>Id</th>
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
                                                    <a href="{{ route('wl-payout_report.show', $value->id) }}"
                                                        target="_blank" class="dropdown-item"><i
                                                            class="fa fa-eye text-info mr-2"></i>View</a>
                                                    <a href="{{ route('wl-payout_report.pdf', $value->id) }}"
                                                        class="dropdown-item"><i
                                                            class="fa fa-file-pdf-o text-primary mr-2"></i>Pdf</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $value->id }}</td>
                                        <td>{{ $value->company_name }}</td>
                                        <td>{{ $value->start_date }}</td>
                                        <td>{{ $value->end_date }}</td>
                                        <td>{{ $value->status == 1 ? 'Paid' : '' }}</td>
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
                                            <li><a target="_blank" href="{{ getS3Url($files[$i]) }}"> <i
                                                        class="fa fa-file text-warning"></i></a></li>
                                            <?php
                                        }
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
@endsection
