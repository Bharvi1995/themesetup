@extends($agentUserTheme)
@section('title')
    Show Application
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('rp.dashboard') }}">Dashboard</a> / <a href="{{ route('rp.user-management') }}">Merchant
        Management</a> / Show Application
@endsection
@section('content')
    {{-- Page-Title --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Merchant Application Details</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @include('partials.application.applicationShow')
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="iq-header-title">
                        <h4 class="card-title">Merchant Documents</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if ($data->licence_document != null)
                            <div class="col-md-8 mt-2">Licence Document</div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ getS3Url($data->licence_document) }}" target="_blank"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $data->licence_document]) }}"
                                    class="btn btn-primary btn-sm">Download</a>
                            </div>
                        @endif

                        @if (isset($data->passport) && $data->passport)
                            <div class="col-md-6 mt-2">Passport</div>
                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    @foreach (json_decode($data->passport) as $key => $passport)
                                        <div class="col-md-4 mt-2">File - {{ $key + 1 }}</div>
                                        <div class="col-md-8 mt-2">
                                            <a href="{{ getS3Url($passport) }}" target="_blank"
                                                class="btn btn-info btn-sm">View</a>
                                            <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $passport]) }}"
                                                class="btn btn-primary btn-sm">Download</a>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endif

                        @if (isset($data->latest_bank_account_statement))
                            <div class="col-md-6 mt-2">Company's Bank Statement (last 180 days)</div>
                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    @foreach (json_decode($data->latest_bank_account_statement) as $key => $bankStatement)
                                        <div class="col-md-4 mt-2">File - {{ $key + 1 }}</div>
                                        <div class="col-md-8 mt-2">
                                            <a href="{{ getS3Url($bankStatement) }}" target="_blank"
                                                class="btn btn-info btn-sm">View</a>
                                            <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $bankStatement]) }}"
                                                class="btn btn-primary btn-sm">Download</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (isset($data->utility_bill))
                            <div class="col-md-6 mt-2">Utility Bill</div>
                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    @foreach (json_decode($data->utility_bill) as $key => $utilityBill)
                                        <div class="col-md-4 mt-2">File - {{ $key + 1 }}</div>
                                        <div class="col-md-8 mt-2">
                                            <a href="{{ getS3Url($utilityBill) }}" target="_blank"
                                                class="btn btn-info btn-sm">View</a>
                                            <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $utilityBill]) }}"
                                                class="btn btn-primary btn-sm">Download</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (isset($data->company_incorporation_certificate) && $data->company_incorporation_certificate)
                            <div class="col-md-8 mt-2">Articles Of Incorporation</div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ getS3Url($data->company_incorporation_certificate) }}" target="_blank"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $data->company_incorporation_certificate]) }}"
                                    class="btn btn-primary btn-sm">Download</a>
                            </div>
                        @endif

                        @if (isset($data->domain_ownership))
                            <div class="col-md-8 mt-2">Domain Ownership</div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ getS3Url($data->domain_ownership) }}" target="_blank"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $data->domain_ownership]) }}"
                                    class="btn btn-primary btn-sm">Download</a>
                            </div>
                        @endif

                        @if (isset($data->owner_personal_bank_statement))
                            <div class="col-md-8 mt-2">UBO's Bank Statement (last 90 days)</div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ getS3Url($data->owner_personal_bank_statement) }}" target="_blank"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $data->owner_personal_bank_statement]) }}"
                                    class="btn btn-primary btn-sm">Download</a>
                            </div>
                        @endif

                        @if (isset($data->previous_processing_statement) && $data->previous_processing_statement != null)
                            <div class="col-md-6 mt-2">
                                Processing History (if any)
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    @php
                                        $previous_processing_statement_files = json_decode($data->previous_processing_statement);
                                    @endphp
                                    <div class="col-md-12">
                                        <div class="row">
                                            @php
                                                $count = 1;
                                            @endphp
                                            @foreach ($previous_processing_statement_files as $key => $value)
                                                <div class="col-md-4 mt-2">File - {{ $count }}</div>
                                                <div class="col-md-8 mb-2">
                                                    <a href="{{ getS3Url($value) }}" target="_blank"
                                                        class="btn btn-info btn-sm">View</a>
                                                    <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $value]) }}"
                                                        class="btn btn-primary btn-sm">Download</a>
                                                </div>
                                                @php
                                                    $count++;
                                                @endphp
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($data->moa_document != null)
                            <div class="col-md-8 mt-2">MOA(Memorandum of Association) Document</div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ getS3Url($data->moa_document) }}" target="_blank"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $data->moa_document]) }}"
                                    class="btn btn-primary btn-sm">Download</a>
                            </div>
                        @endif
                        @if (isset($data->extra_document) && $data->extra_document != null)
                            <div class="col-md-6 mt-2">
                                Additional Document
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="row">
                                    @php
                                        $extra_document_files = json_decode($data->extra_document);
                                    @endphp
                                    <div class="col-md-12">
                                        <div class="row">
                                            @php
                                                $count = 1;
                                            @endphp
                                            @foreach ($extra_document_files as $key => $value)
                                                <div class="col-md-4 mt-2">File - {{ $count }}</div>
                                                <div class="col-md-8 mb-2">
                                                    <a href="{{ getS3Url($value) }}" target="_blank"
                                                        class="btn btn-info btn-sm">View</a>
                                                    <a href="{{ route('downloadDocumentsUploadeUser', ['file' => $value]) }}"
                                                        class="btn btn-primary btn-sm">Download</a>
                                                </div>
                                                @php
                                                    $count++;
                                                @endphp
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
