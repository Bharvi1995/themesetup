@extends($agentUserTheme)
@section('title')
    Dashboard
@endsection

@section('breadcrumbTitle')
    Dashboard
@endsection
@section('customStyle')
<style type="text/css">
    .merchantTxnCardMain{
        width: 20%;
    }
</style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 mt-2 mb-2">
            <div class="row">
                @if (Auth::guard('agentUser')->user()->main_agent_id == 0)
                    @if (auth()->guard('agentUser')->user()->referral_code != null ||
                            auth()->guard('agentUser')->user()->referral_code != '')
                        <div class="col-xl-12 col-lg-12">
                            <div class="bg-white merchantTxnCard">
                                <div class="rounded">
                                    <div class="row">
                                        <div class="col-md-12 mb-2 text-center">
                                            <h4>Your Invitation Link</h4>
                                            <p class="mb-1">Your unique invitation link for merchant sign-up</p>

                                            <span class="badge badge-primary px-3 py-1" id="Copy"
                                                data-link="{{ config('app.url') }}/register?RP={{ auth()->guard('agentUser')->user()->referral_code }}">{{ config('app.url') }}/register?RP={{ auth()->guard('agentUser')->user()->referral_code }}</span>
                                        </div>
                                        <!-- <div class="col-md-12 mt-2 mb-2 text-center">
                                            <span class="btn btn-danger btn-sm" id="Copy"
                                                style="cursor: pointer;">Copy</span>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-sm-6 col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="subheader">Successful</div>
                      </div>
                      <div class="d-flex align-items-baseline">
                        <div class="h2 mb-0 me-2">$ {{ $transaction->successfullV }}</div>
                        <div class="me-auto">
                          <span class="text-success d-inline-flex align-items-center lh-1">
                            {{ round($transaction->successfullP,2) }} %
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                          </span>
                        </div>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Total Count: {{ $transaction->successfullC }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="subheader">Declined</div>
                      </div>
                      <div class="d-flex align-items-baseline">
                        <div class="h2 mb-0 me-2">$ {{$transaction->declinedV}}</div>
                        <div class="me-auto">
                          <span class="text-danger d-inline-flex align-items-center lh-1">
                            {{round($transaction->declinedP,2)}} %
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
                          </span>
                        </div>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Total Count: {{$transaction->declinedC}}
                        </div>
                      </div>
                    </div>
                    <div id="chart-revenue-bg" class="chart-sm"></div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="subheader">Refund</div>
                      </div>
                      <div class="d-flex align-items-baseline">
                        <div class="h2 mb-0 me-2">$  {{ $transaction->refundV }}</div>
                        <div class="me-auto">
                          <span class="text-warning d-inline-flex align-items-center lh-1">
                            {{ round($transaction->refundP,2) }} %  
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                          </span>
                        </div>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Total Count:  {{ $transaction->refundC }}
                        </div>
                      </div>
                      <div id="chart-new-clients" class="chart-sm"></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="subheader">Chargebacks</div>
                      </div>
                      <div class="d-flex align-items-baseline">
                        <div class="h2 mb-0 me-2">$  {{ $transaction->chargebackV }}</div>
                        <div class="me-auto">
                          <span class="text-warning d-inline-flex align-items-center lh-1">
                            {{ round($transaction->chargebackP,2) }} %  
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                          </span>
                        </div>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Total Count:  {{ $transaction->chargebackC }}
                        </div>
                      </div>
                      <div id="chart-new-clients" class="chart-sm"></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="subheader">Dispute</div>
                      </div>
                      <div class="d-flex align-items-baseline">
                        <div class="h2 mb-0 me-2">$  {{ $transaction->suspiciousV }}</div>
                        <div class="me-auto">
                          <span class="text-primary d-inline-flex align-items-center lh-1">
                            {{ round($transaction->suspiciousP,2) }} %  
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                          </span>
                        </div>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Total Count:  {{ $transaction->suspiciousC }}
                        </div>
                      </div>
                      <div id="chart-new-clients" class="chart-sm"></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                        <div class="subheader">Retrieval</div>
                      </div>
                      <div class="d-flex align-items-baseline">
                        <div class="h2 mb-0 me-2">$  {{ $transaction->retrievalV }}</div>
                        <div class="me-auto">
                          <span class="text-info d-inline-flex align-items-center lh-1">
                            {{ round($transaction->retrievalP,2) }} %  
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                          </span>
                        </div>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Total Count:  {{ $transaction->retrievalC }}
                        </div>
                      </div>
                      <div id="chart-new-clients" class="chart-sm"></div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>    
@endsection
@section('customScript')
    <script>
        function Clipboard_CopyTo(value) {
            var tempInput = document.createElement("input");
            tempInput.value = value;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
        }
        document.querySelector('#Copy').onclick = function() {
            var code = $('#Copy').attr("data-link");
            Clipboard_CopyTo(code);
            toastr.success("Referral link copied successfully!");
        }
    </script>
@endsection
