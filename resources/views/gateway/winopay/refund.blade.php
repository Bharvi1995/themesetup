@extends('layouts.admin.default')
@section('title')
    Winopay Refunds
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Winopay Refund
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h1>Winopay Refund</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('winopay.refund') }}" method="POST" class="form-dark">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 mb-2">
                                <label>Order Id</label>
                                <input type="text" class="form-control" name="order_id"
                                    placeholder="Enter transaction order id" value="{{ old('order_id') }}" />
                                @error('order_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Gateway Id</label>
                                <input type="text" class="form-control" name="gateway_id"
                                    placeholder="Enter transaction gateway id" value="{{ old('gateway_id') }}" />
                                @error('gateway_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mb-2">
                                <button type="submit" class="btn btn-danger">Process Refund</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
@endsection
