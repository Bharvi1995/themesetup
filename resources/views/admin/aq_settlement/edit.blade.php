@extends('layouts.admin.default')

@section('title')
    Edit Settlement
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Edit Settlement
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header ">
                    <h4>Edit Settlement</h4>
                    <a href="{{ route('aq-settlement.index') }}"><button class="btn btn-sm btn-success ">Back</button></a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('aq-settlement.update', $settlement->id) }}" class="form-dark"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="name">AQ Name</label>
                                <select name="middetail_id" class="form-control select2">
                                    <option value="">-- Select AQ --</option>
                                    @foreach ($mids as $mid)
                                        <option value="{{ $mid->id }}"
                                            {{ $settlement->middetail_id == $mid->id ? 'selected' : '' }}>
                                            {{ $mid->bank_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('middetail_id')
                                    <span class="text-danger ">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="form-group col-lg-6">
                                <label for="from_date">From Date</label>
                                <input type="text" class="form-control flatpickr" id="from_date"
                                    placeholder="Choose Date.." name="from_date" value="{{ $settlement->from_date }}">
                                @error('from_date')
                                    <span class="text-danger ">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="form-group col-lg-6">
                                <label for="to_date">To Date</label>
                                <input type="text" class="form-control flatpickr" id="to_date"
                                    placeholder="Choose Date.." name="to_date" value="{{ $settlement->to_date }}">
                                @error('to_date')
                                    <span class="text-danger ">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="form-group col-lg-6">
                                <label for="txn_hash">Transaction Hash</label>
                                <input type="text" class="form-control" name="txn_hash" id="txn_hash"
                                    placeholder="Enter Txn hash.." value="{{ $settlement->txn_hash }}" />
                                @error('txn_hash')
                                    <span class="text-danger ">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="paid_date">Paid Date</label>
                                <input type="text" class="form-control flatpickr" id="paid_date"
                                    placeholder="Choose Date.." name="paid_date" value="{{ $settlement->paid_date }}">
                                @error('paid_date')
                                    <span class="text-danger ">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-6">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <label for="payment_receipt">Payment Receipt</label>
                                        <input type="file" class="form-control" name="payment_receipt"
                                            id="payment_receipt" placeholder="Enter Txn hash.." />
                                        @error('payment_receipt')
                                            <span class="text-danger ">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="{{ getS3Url($settlement->payment_receipt) }}" target="_blank"><button
                                                class="btn btn-sm btn-warning  mt-2" type="button">View</button> </a>
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-12">
                                <button class="btn btn-success ">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('customScript')
    <script>
        $(".flatpickr").flatpickr({
            dateFormat: "Y-m-d",
        });
    </script>
@endsection
