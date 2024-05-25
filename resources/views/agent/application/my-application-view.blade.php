@extends($agentUserTheme)

@section('title')
    My Application Create
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / My Application Detail
@endsection


@section('content')
    <div class="row">
        <div class="col-xl-8 col-xxl-8">
            <div class="card  mt-1 height-auto">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">My Application</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Entity Name</strong>
                                        <p>{{ $data->company_name }}</p>
                                    </td>
                                    <td>
                                        <strong>Your Website URL</strong>
                                        <p>{{ $data->website_url }}</p>
                                    </td>
                                    <td>
                                        <strong>Address</strong>
                                        <p>{{ $data->company_address }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Company Email</strong>
                                        <p>{{ $data->company_email }}</p>
                                    </td>
                                    <td>
                                        <strong>Tax Id</strong>
                                        <p>{{ $data->company_registered_number }}</p>
                                    </td>
                                    <td>
                                        <strong>Date Of Birth/Incorporation</strong>
                                        <p>{{ $data->company_registered_number_year }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Average No. of Applications Per Month</strong>
                                        <p>{{ $data->avg_no_of_app }}</p>
                                    </td>
                                    <td>
                                        <strong>Average Volume Commited Per Month (In USD)</strong>
                                        <p>{{ $data->commited_avg_volume_per_month }}</p>
                                    </td>
                                    <td>
                                        <strong>Payment Solutions Needed</strong>
                                        @if ($data->payment_solutions_needed != null)
                                            <?php
                                            $payment_solution = json_decode($data->payment_solutions_needed);
                                            if (is_array($payment_solution) && !empty($payment_solution)) {
                                                foreach ($payment_solution as $key => $value) {
                                                    echo "<span class='badge badge-sm badge-primary'>" . \App\TechnologyPartner::find($value)->name . '</span> ';
                                                }
                                            }
                                            ?>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Industries Referred</strong>
                                        <p>
                                            @if ($data->industries_reffered != null)
                                                <?php
                                                $indutry_types = json_decode($data->industries_reffered);
                                                if (is_array($indutry_types) && !empty($indutry_types)) {
                                                    foreach ($indutry_types as $key => $value) {
                                                        echo "<span class='badge badge-sm badge-primary'>" . \App\Categories::find($value)->name . '</span> ';
                                                    }
                                                }
                                                ?>
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <strong>Major Regions</strong>
                                        <p>
                                            @if ($data->major_regious != null)
                                                <?php
                                                $a = json_decode($data->major_regious);
                                                if (is_array($a)) {
                                                    if (!empty($a)) {
                                                        foreach ($a as $key => $value) {
                                                            echo "<span class='badge badge-sm badge-primary'>" . $value . '</span> ';
                                                        }
                                                    }
                                                }
                                                ?>
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <strong>How are the leads generated?</strong>
                                        <p>{{ $data->generated_lead }}</p>
                                    </td>
                                </tr>
                                @if ($data->authorised_individual != null)
                                    @php
                                        $b = json_decode($data->authorised_individual);
                                    @endphp
                                    @if (is_array($b) && !empty($b))
                                        @foreach ($b as $key => $record)
                                            <tr>
                                                <td colspan="3">
                                                    <div class="col-md-6 mb-2"><strong>Authorised Individual
                                                            {{ $key + 1 }}</strong>
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Name</strong>
                                                    <p> {{ $record->name }}</p>
                                                </td>
                                                <td>
                                                    <strong>Phone Number</strong>
                                                    <p>{{ $record->phone_number }}</p>
                                                </td>
                                                <td>
                                                    <strong>Email</strong>
                                                    <p>{{ $record->email }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-xxl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Status</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mt-2">
                            @if ($data->status == '0')
                                <i class="fa fa-circle text-primary mr-1"></i>
                                Pending
                            @elseif($data->status == '1')
                                <i class="fa fa-circle text-success mr-1"></i>
                                Approved
                            @elseif($data->status == '2')
                                <i class="fa fa-circle text-danger mr-1"></i>
                                Rejected
                            @elseif($data->status == '3')
                                <i class="fa fa-circle text-primary mr-1"></i>
                                Reassigned
                            @endif
                        </div>
                        @if ($data->status == '0' || $data->status == '3')
                            <div class="col-md-6 mt-2">
                                <div class="col-sm-12">
                                    <a href="{{ route('rp.my-application.edit') }}"
                                        class="btn btn-warning btn-sm btn-block" title="Edit">Edit</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @if ($data->status == 1)
                <div class="card">
                    <div class="card-header">

                        <h4 class="card-title">Agreement</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6 col-xxl-5">
                                <a href="{{ $data->agent->agreementDocument->sent_files ? getS3Url($data->agent->agreementDocument->sent_files) : '#' }}"
                                    target="_blank" class="btn badge-primary"><i class="fa fa-eye"></i> View</a>
                            </div>
                            <div class="col-xl-6 col-xxl-5">
                                <a href="{{ $data->agent->agreementDocument->sent_files ? route('downloadDocumentsUploade', ['file' => $data->agent->agreementDocument->sent_files]) : '#' }}"
                                    class="btn badge-success"><i class="fa fa-download"></i> Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="card">
                <div class="card-header">

                    <h4 class="card-title">Application Documents</h4>

                </div>
                <div class="card-body">
                    <div class="row">
                        @if (isset($data->passport) && $data->passport != null)
                            <div class="col-md-4 mt-2">Passport</div>
                            <div class="col-md-8 mb-2">
                                <div class="row">

                                    @foreach (json_decode($data->passport) as $key => $passport)
                                        <div class="col-md-4 mt-2">File - {{ $key + 1 }}</div>
                                        <div class="col-md-8 mt-2 pl-0 pr-0">
                                            <a href="{{ getS3Url($passport) }}" target="_blank"
                                                class="btn badge-primary btn-sm"> <i class="fa fa-eye"></i> View</a>
                                            <a href="{{ route('downloadDocumentsUploadRpApplication', ['file' => $passport]) }}"
                                                class="btn badge-success btn-sm"><i class="fa fa-download"></i> Download</a>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        @if (isset($data->utility_bill) && $data->utility_bill != null)
                            @if (isset($data->utility_bill))
                                <div class="col-md-4 mt-2">Utility Bill</div>
                                <div class="col-md-8 mb-2">
                                    <div class="row">
                                        @foreach (json_decode($data->utility_bill) as $key => $utilityBill)
                                            <div class="col-md-4 mt-2">File - {{ $key + 1 }}</div>
                                            <div class="col-md-8 mt-2 pl-0 pr-0">
                                                <a href="{{ getS3Url($utilityBill) }}" target="_blank"
                                                    class="btn badge-primary btn-sm"><i class="fa fa-eye"></i> View</a>
                                                <a href="{{ route('downloadDocumentsUploadRpApplication', ['file' => $utilityBill]) }}"
                                                    class="btn badge-success btn-sm"><i class="fa fa-download"></i>
                                                    Download</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="row">
                        @if (isset($data->company_incorporation_certificate) && $data->company_incorporation_certificate != null)
                            <div class="col-md-4 mt-2">Articles Of Incorporation</div>
                            <div class="col-md-8 mb-2">
                                <div class="row">
                                    <div class="col-md-4 mt-2"></div>
                                    <div class="col-md-8 mt-2 pl-0 pr-0">
                                        <a href="{{ getS3Url($data->company_incorporation_certificate) }}"
                                            target="_blank" class="btn badge-primary btn-sm"><i class="fa fa-eye"></i>
                                            View</a>
                                        <a href="{{ route('downloadDocumentsUploadRpApplication', ['file' => $data->company_incorporation_certificate]) }}"
                                            class="btn badge-success btn-sm"><i class="fa fa-download"></i> Download</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (isset($data->tax_id) && $data->tax_id != null)
                            <div class="col-md-4 mt-2">Tax ID</div>
                            <div class="col-md-8 mb-2">
                                <div class="row">
                                    <div class="col-md-4 mt-2"></div>
                                    <div class="col-md-8 mt-2 pl-0 pr-0">
                                        <a href="{{ getS3Url($data->tax_id) }}" target="_blank"
                                            class="btn badge-primary btn-sm"><i class="fa fa-eye"></i> View</a>
                                        <a href="{{ route('downloadDocumentsUploadRpApplication', ['file' => $data->tax_id]) }}"
                                            class="btn badge-success btn-sm"><i class="fa fa-download"></i> Download</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @if (isset($data->agent->agreementDocument->cross_signed_agreement) &&
                    !empty($data->agent->agreementDocument->cross_signed_agreement))
                <div class="card">
                    <div class="card-header">

                        <h4 class="card-title">Cross Signed Agreement</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6 col-xxl-3">
                                <a href="{{ getS3Url($data->agent->agreementDocument->cross_signed_agreement) }}"
                                    target="_blank" class="btn badge-primary btn-sm"><i class="fa fa-eye"></i> View</a>
                            </div>
                            <div class="col-xl-6 col-xxl-4">
                                <a href="{{ route('downloadDocumentsUploadRpApplication', ['file' => $data->agent->agreementDocument->cross_signed_agreement]) }}"
                                    class="btn btn-success btn-sm"><i class="fa fa-download"></i> Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-xl-12 col-xxl-12">

        </div>
    </div>
@endsection
