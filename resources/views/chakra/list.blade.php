@extends('layouts.admin.default')
@section('title')
Admin Users
@endsection
@section('breadcrumbTitle')
<a href="{{ route('admin.dashboard') }}">Dashboard</a> / CHAKRA Transactions
@endsection
@section('content')
<div class="chatbox">
  <div class="chatbox-close"></div>
  <div class="custom-tab-1">
    <a class="nav-link active" data-toggle="tab" href="#Search">Advanced Search</a>
    <div class="tab-content">
      <div class="tab-pane fade active show" id="Search" role="tabpanel">
        <form action="{{route('chakra-transactionlist')}}" method="get" id="search-form" name="paymentForm">
         <input type="hidden" name="records" value="{{$records}}" id="records">
          <div class="basic-form">
            <div class="form-row">
              <div class="form-group col-lg-12">
               <label for="email">Select MID</label>
                <select class="form-control" name="mid" required="">
                  <option value="">Select Mid</option>
                  @foreach($midId as $data)
                  <option value="{{$data->id}}" @if(isset($_GET['mid']) && $_GET['mid'] == $data->id) selected @endif>{{$data->bank_name}}</option>
                  @endforeach
               </select>
              </div>
              <div class="form-group col-lg-12">
                  <hr> </hr>
              </div>
              <div class="form-group col-lg-6">
                <label for="email">Start Date</label>
                 <div class="date-input">
                  <input class="form-control" type="text" name="start_date" placeholder="Start Date"
                     id="start_date"
                     value="{{ (isset($_GET['start_date']) && $_GET['start_date'] != '') ? Carbon\Carbon::parse($_GET['start_date'])->format('d-m-Y') : '' }}"
                     autocomplete="off">
                  </div>
              </div>
               <div class="form-group col-lg-6">
                <label for="email">End Date</label>
                 <div class="date-input">
                  <input type="text" id="end_date" class="form-control"
                     data-multiple-dates-separator=" - " data-language="en" placeholder="End Date"
                     name="end_date"
                     value="{{ (isset($_GET['end_date']) && $_GET['end_date'] != '') ? Carbon\Carbon::parse($_GET['end_date'])->format('d-m-Y') : '' }}"
                     autocomplete="off">
                  </div>
              </div>
               <div class="col-lg-12 text-center">
                  <span> OR </span>
              </div>
              <div class="form-group col-lg-6">
                <label for="email">Reference No</label>
                 <div>
                  <input type="text" id="reference_id" class="form-control"
                     data-multiple-dates-separator=" - " data-language="en" placeholder="Reference ID"
                     name="reference_id"
                     value="{{ (isset($_GET['reference_id']) && $_GET['reference_id'] != '') ? $_GET['reference_id']  : '' }}"
                     autocomplete="off" >
                  </div>
              </div>
              <div class="col-sm-12 mt-4 submit-buttons-commmon">
                <button type="submit" class="btn btn-success" id="extraSearch123">Search</button>
                <button type="button" class="btn btn-danger" id="resetForm"></i>Clear</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="row">
   <div class="col-lg-12 col-xl-12">
      <div class="card">
         <div class="card-header">
            <div class="mr-auto pr-3">
               <h4 class="card-title">CHAKRA Transactions</h4>
            </div>
            @if(empty($err) && ! empty($responseData['data']))
               <select class="custom-select form-control" name="noList" id="noList" style="float: left; width: 15%; margin-right: 20px;">
                     <option value="">--No of Records--</option>
                     <option value="30" {{$records == '30' ? 'selected' : '' }}>30</option>
                     <option value="50" {{$records == '50' ? 'selected' : '' }}>50</option>
                     <option value="100" {{$records == '100' ? 'selected' : '' }}>100</option>
               </select>
            @endif
             <div class="btn-group mr-2">
            <button type="button" class="btn btn-warning bell-link btn-sm" id="filter"> <i class="fa fa-search-plus"></i>
            Advanced
            Search</button>
            <a href="{{route('chakra-transactionlist')}}" class="btn btn-danger btn-sm">Reset</a>
         </div>
           
         </div>
         <div class="card-body">
            @if(!empty($err) && empty($err1))
            <div class="row">
               <div class="col-md-12" >
                  <div class="alert alert-danger">
                      Please select MID from <a href="#" id="searchmid">search</a>.
                  </div>
               </div>
            </div>
            @elseif(!empty($err1))
             <div class="row">
               <div class="col-md-12" >
                  <div class="alert alert-danger">
                     {{$err1}}
                  </div>
               </div>
            </div>
            @else
            <div class="table-responsive">
               <table class="table table-responsive-md table-hover" style=" white-space: nowrap;">
                  <thead>
                     <tr>
                        <th>Payment Date</th>
                        <th>Order ID</th>
                        <th>Card Details</th>
                        <th>Refernce No</th>
                        <th>Response Message</th>
                        <th>Amount</th>
                        <th>Date</th>
                     </tr>
                  </thead>
                  <tbody>
                     @if(!empty($responseData) && $responseData['responseCode'] == '00') 
                     @foreach($responseData['data'] as $data)

                     <tr>
                        <td>
                           {{ $data['paymentDate'] ?? ' - '}}
                        </td>
                        <td>{{ isset($data['narration'])?$data['narration']:'' }}</td>
                        <td>{{ isset($data['pan'])?$data['pan']:'' }}</td>
                        <td>{{ $data['transactionReference'] }} <br/></td>
                        <td>
                           [{{ $data['responseCode'] }}] {{ $data['transactionStatus'] }}: {{ $data['responseMessage'] }}
                        </td>
                        <td>
                           {{ $data['currency'] . ' ' . $data['amount'] }} <br/>
                           <b>Fees: </b>{{ $data['currency'] . ' ' . $data['totalFee'] }}
                        </td>
                        <td>
                           <b>Created: </b>{{ $data['dateCreated'] ?? ' - '}} <br/>
                           <b>Updated: </b>{{ $data['dateUpdated'] ?? ' - '}}
                        </td>
                     </tr>
                     @endforeach
                     @else
                     <tr>
                        <td colspan="7">
                           <p class="text-center"><strong>No record found.</strong></p>
                        </td>
                     </tr>
                     @endif
                  </tbody>
               </table>
            </div>
            @endif
         </div>
         
         <div class="card-footer">
            <div class="pagination-wrap">
               <ul class="pagination" role="navigation" style="float: right;">
                  @if($page > 1)
                     <li class="page-item">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" rel="prev" aria-label="« Previous">‹</a>
                     </li>
                  @endif
                  
                  @if(isset($responseData['data']) && count($responseData['data']) == $records)
                  <li class="page-item">
                     <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" rel="next" aria-label="Next »">›</a>
                  </li>
                  @endif
               </ul>
            </div>
         </div>
         
      </div>
   </div>
</div>
@endsection
@section('customScript')
<script type="text/javascript">
   $(document).on("change", "#noList", function () {
      $('#records').val($(this).val());
      document.getElementById("search-form").submit();
   });
   $(document).on("click", "#searchmid", function () {
      $('#filter').trigger('click');
   });

   $(".date-input").change(function(){
      $("#reference_id").val("");
   });
   $("#reference_id").keyup(function(){
      $("#start_date").val("");
      $("#end_date").val("");
   });
</script>
@endsection