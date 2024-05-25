@extends('layouts.admin.default')

@section('title')
    RM Edit
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / RM Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header ">
                    <h4>Edit RM</h4>
                    <a href="{{ route('sales.index') }}"><button class="btn btn-sm btn-success ">Back</button></a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sales.update', $sale->id) }}" class="form-dark">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter here..."
                                    name="name" value="{{ $sale->name }}">
                                @error('name')
                                    <span class="text-danger ">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="form-group col-lg-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Enter here..."
                                    name="email" value="{{ $sale->email }}">
                                @error('email')
                                    <span class="text-danger ">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="form-group col-lg-6">
                                <label for="email">Country code</label>
                                <select class="form-control select2" name="country_code">
                                    <option value="">-- Select Country --</option>
                                    @foreach ($countries as $key => $country)
                                        <option value="{{ $key }}"
                                            {{ $sale->country_code == $key ? 'selected' : '' }}>{{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('country_code')
                                    <span class="text-danger ">{{ $message }}</span>
                                @enderror
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
