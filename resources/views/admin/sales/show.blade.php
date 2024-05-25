@extends('layouts.admin.default')

@section('title')
    RM Show
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / RM Show
@endsection


@section('content')
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header ">
                    <h4>Show RM</h4>
                    <a href="{{ route('sales.index') }}"><button class="btn btn-sm btn-success ">Back</button></a>
                </div>
                <div class="card-body">
                    @if (count($users) > 0)
                        <div class="row">
                            @foreach ($users as $user)
                                <div class="col-lg-4 mt-1">
                                    <div class="merchantTxnCard p-2">
                                        <p>Merchant Name : <strong>{{ $user->name }}</strong></p>
                                        <p>Merchant Email : <strong>{{ $user->email }}</strong></p>

                                        <p>Business Name :
                                            <strong>{{ $user->customApplication->business_name ?? 'N/A' }}</strong>
                                        </p>
                                        <p>Skype Id : <span
                                                class="badge bg-info ">{{ $user->customApplication->skype_id ?? 'N/A' }}</span>
                                        </p>
                                        <p>Phone Numbers : <span
                                                class="badge bg-danger  ">{{ $user->mobile_no ?? 'N/A' }}</span><span
                                                class="badge bg-danger  ms-1">{{ $user->customApplication->phone_no ?? 'N/A' }}</span>
                                        </p>
                                        <div class="d-flex justify-content-end ">
                                            <button class="btn btn-sm btn-danger rmAssignRMBtn"
                                                data-id="{{ $user->id }}"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center ">
                            <p>No Merchant found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <form style="display: none;" action="{{ route('remove.assigned.rm') }}" method="POST" id="removeAssignRmForm">
            @csrf
            <input type="hidden" name="user_id" class="rmUserId" value="" />
        </form>
    </div>
@endsection

@section('customScript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.rmAssignRMBtn', function() {
                var userId = $(this).attr("data-id")
                $(".rmUserId").val(userId);
                Swal.fire({
                    title: "Are you sure?",
                    text: "You want to remove RM from this merchant!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, remove it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#removeAssignRmForm").submit();
                    }
                });
            })
        });
    </script>
@endsection
