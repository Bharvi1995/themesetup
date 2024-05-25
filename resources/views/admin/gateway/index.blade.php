@extends('layouts.admin.default')
@section('title')
    Gateway
@endsection
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> /Gateway List
@endsection

@section('content')
    <div class="row">
        @if (auth()->guard('admin')->user()->can(['create-gateway']))
            <div class="col-xl-12 col-lg-12 col-sm-12 mb-2">
                <a href="{{ route('admin.gateway.create') }}" class="btn btn-success pull-right">
                    Create Gateway</a>
            </div>
        @endif
        @if (auth()->guard('admin')->user()->can(['list-gateway']))
            @foreach ($gateways as $gateway)
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body gateway-card">
                            <div class="row">
                                <div class="col-md-9">
                                    <h4 class="card-title mb-50">
                                        {{ $gateway->title }}
                                    </h4>
                                    <p class="card-text">Total Sub Gateway - {{ $gateway->subgateway()->count() }}</p>


                                    @if (auth()->guard('admin')->user()->can(['create-sub-gateway']))
                                        <a href="{{ route('admin.subgateway.index', ['gateway_id' => $gateway->id]) }}"
                                            class="btn btn-primary btn-sm mt-1"><i class="fa fa-plus"></i> Add Sub Gateways</a>
                                    @endif
                                    @if (auth()->guard('admin')->user()->can(['update-gateway']))
                                        <a href="{{ route('admin.gateway.edit', $gateway->id) }}"
                                            class="btn btn-primary btn-sm mt-1"><i class="fa fa-edit"></i> Edit</a>
                                    @endif
                                </div>
                                <div class="col-md-3 text-right">
                                    <svg width="53" height="41" viewBox="0 0 53 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M44.3752 13.2608H4.85809C4.46389 13.2424 4.08481 13.0983 3.77718 12.8484C3.446 12.5794 3.21803 12.2043 3.13146 11.7865C3.04488 11.3686 3.10559 10.9338 3.30256 10.5554C3.49978 10.177 3.82175 9.87839 4.21364 9.70995L25.9122 0.307963C26.1443 0.207521 26.3945 0.155701 26.6473 0.155701C26.9004 0.155701 27.1506 0.207521 27.3827 0.307963L49.0813 9.7066C49.4732 9.87505 49.7951 10.1737 49.9921 10.5521C50.1893 10.9304 50.2498 11.3653 50.1635 11.7831C50.0769 12.2009 49.8489 12.5761 49.5177 12.8451C49.1866 13.1141 48.7726 13.2605 48.3459 13.2595H44.3752V13.2608ZM13.8832 9.55954H39.4151L26.6473 4.02464L13.8832 9.55954Z" fill="#7D7D7D"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.91918 14.7458L8.91969 30.9267H44.3757L44.3747 14.7458H8.91918ZM19.5011 27.9569H12.6224V18.384H19.5011V27.9569ZM23.2076 27.9535V18.3907L30.0863 18.3874V27.9535H23.2076ZM40.6715 18.3874V27.9535H33.7895V18.3874H40.6715Z" fill="#7D7D7D"/>
                                    <path d="M4.46671 34.1527C4.46671 33.1913 5.24614 32.4118 6.20769 32.4118H47.0875C48.049 32.4118 48.8285 33.1913 48.8285 34.1527C48.8285 35.1142 48.049 35.8937 47.0875 35.8937H6.20769C5.24614 35.8937 4.46671 35.1142 4.46671 34.1527Z" fill="#7D7D7D"/>
                                    <path d="M2.11305 37.3788C1.1515 37.3788 0.37207 38.1582 0.37207 39.1197C0.37207 40.0812 1.1515 40.8607 2.11305 40.8607H51.1819C52.1434 40.8607 52.9229 40.0812 52.9229 39.1197C52.9229 38.1582 52.1434 37.3788 51.1819 37.3788H2.11305Z" fill="#7D7D7D"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
