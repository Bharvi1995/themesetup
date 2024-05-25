   @if (auth()->guard('admin')->user()->can(['overview-transaction-statistics']))
       @php
           $liveMerchantP = $merchants->totalMerchant ? round($merchants->liveMerchant / $merchants->totalMerchant, 4) * 100 : '0';
           $notLiveMerchantP = $merchants->totalMerchant ? round($merchants->notLiveMerchant / $merchants->totalMerchant, 4) * 100 : '0';
       @endphp
   @endif

   <div class="row">
       <div class="col-lg-4 mb-2">
           <div class="merchantTxnCard">
               <h2>{{ $merchants->totalMerchant }}</h2>
               <p class="mb-1" style="color: #FFFFFF;">Total Merchant</p>
               <p class="total" style="color: #82CD47;">Total Count : <span>
                       {{ $merchants->totalMerchant }}</span></p>
           </div>
       </div>
       <div class="col-lg-4 mb-2">
           <div class="merchantTxnCard">
               <h2>{{ $merchants->liveMerchant }}</h2>
               <p class="mb-1" style="color: #FFFFFF;">Live Merchant</p>
               <p class="total" style="color: #5F9DF7;">Total Percentage : <span> {{ $liveMerchantP }}
                       %</span></p>
           </div>
       </div>
       <div class="col-lg-4 mb-2">
           <div class="merchantTxnCard">
               <h2>{{ $merchants->notLiveMerchant }}</h2>
               <p class="mb-1" style="color: #FFFFFF;">Pending for Live Merchant</p>
               <p class="total" style="color: #C47AFF;">Total Percentage : <span>
                       {{ $notLiveMerchantP }}
                       %</span></p>
           </div>
       </div>
   </div>
