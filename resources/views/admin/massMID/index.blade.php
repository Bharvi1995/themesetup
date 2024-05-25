@extends('layouts.admin.default')
@section('title')
    Mass MID
@endsection

@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.technical') }}">Technical &
        Additional</a> / Mass MID
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Mass MID</h4>
                    </div>
                    <div>
                        <form id="noListform" method="GET" style="float:left;" class="me-50 form-dark">
                            <select class="form-control form-control-sm" name="noList" id="noList">
                                <option value="">No of Records</option>
                                <option value="30" {{ request()->get('noList') == '30' ? 'selected' : '' }}>30
                                </option>
                                <option value="50" {{ request()->get('noList') == '50' ? 'selected' : '' }}>50
                                </option>
                                <option value="100" {{ request()->get('noList') == '100' ? 'selected' : '' }}>100
                                </option>
                            </select>
                        </form>
                        @if (auth()->guard('admin')->user()->can(['role-create']))
                            <a href="{{ route('mass-mid.create') }}" class="btn btn-success btn-sm"> Change Another Mass MID
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="validation-errors"></div>
                    <div class="table-responsive custom-table">
                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Change Type</th>
                                    <th>Old MID</th>
                                    <th>New MID</th>
                                    <th>Merchants</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mass_mid as $key => $mid)
                                    <tr>
                                        <th>{{ $mid->id }}</th>
                                        <td>{{ config('midtype.name.' . $mid->change_type) }}</td>
                                        <td>{{ $mid->old_bank_name }}</td>
                                        <td>{{ $mid->new_bank_name ?? '--' }}</td>
                                        <td>{{ count(json_decode($mid->user_id)) }}</td>
                                        <td>{{ $mid->created_at }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                    data-bs-toggle="dropdown">
                                                    <svg width="5" height="17" viewBox="0 0 5 17" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.36328 4.69507C1.25871 4.69507 0.363281 3.79964 0.363281 2.69507C0.363281 1.5905 1.25871 0.695068 2.36328 0.695068C3.46785 0.695068 4.36328 1.5905 4.36328 2.69507C4.36328 3.79964 3.46785 4.69507 2.36328 4.69507Z"
                                                            fill="#B3ADAD" />
                                                        <path
                                                            d="M2.36328 10.6951C1.25871 10.6951 0.363281 9.79964 0.363281 8.69507C0.363281 7.5905 1.25871 6.69507 2.36328 6.69507C3.46785 6.69507 4.36328 7.5905 4.36328 8.69507C4.36328 9.79964 3.46785 10.6951 2.36328 10.6951Z"
                                                            fill="#B3ADAD" />
                                                        <path
                                                            d="M2.36328 16.6951C1.25871 16.6951 0.363281 15.7996 0.363281 14.6951C0.363281 13.5905 1.25871 12.6951 2.36328 12.6951C3.46785 12.6951 4.36328 13.5905 4.36328 14.6951C4.36328 15.7996 3.46785 16.6951 2.36328 16.6951Z"
                                                            fill="#B3ADAD" />
                                                    </svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#" data-id="{{ $mid->id }}" class="dropdown-item mass-mid-revert">Revert Back to old MID</a>
                                                    <a href="{{ route('mass-mid.refresh', $mid->id) }}"
                                                        class="dropdown-item refresh-button">Refresh</a>
                                                    <a href="javascript:void(0)"
                                                        class="dropdown-item view-model"
                                                        data-url="{!! URL::route('mass-mid.viewMerchants', $mid->id) !!}">View Merchants</a>
                                                    <a href="{{ route('mass-mid.edit', $mid->id) }}"
                                                        class="dropdown-item">Edit</a>
                                                    <a href="javascript:void(0)"
                                                        class="dropdown-item delete_modal"
                                                        data-url="{!! URL::route('mass-mid.destroy', $mid->id) !!}"
                                                        data-id="{{ $mid->id }}">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    @if (!empty($mass_mid) && $mass_mid->count())
                        <div class="row">
                            <div class="col-md-8">
                                {!! $mass_mid->appends($_GET)->links() !!}
                            </div>
                            <div class="col-md-4 text-right">
                                Showing {{ $mass_mid->firstItem() }} to {{ $mass_mid->lastItem() }} of total {{ $mass_mid->total() }}
                                entries
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- confirm modal --}}
    <div class="modal right fade" id="mass-mid-modal" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg modal-dialog modal-lg-centered" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('mass-mid.revert') }}" class="form-dark">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Confirm </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body" id="mass-mid-body">
                        <h3 id="mass-mid-message"></h3>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="confirm-mass-submit" class="btn btn-success">Confirm</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- merchant list model --}}
    <div class="modal right fade" id="merchant-model" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
        <div class="modal-dialog modal-lg modal-lg modal-dialog modal-lg-centered" role="document">
            <div class="modal-content" id="merchant-list"></div>
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on("change", "#noList", function() {
                var url = new URL(window.location.href);
                if (url.search) {
                    if (url.searchParams.has("noList")) {
                        url.searchParams.set("noList", $(this).val());
                        location.href = url.href;
                    } else {
                        var newUrl = url.href + "&noList=" + $(this).val();
                        location.href = newUrl;
                    }
                } else {
                    document.getElementById("noListform").submit();
                }
            });
            // revert confirm
            $(document).on('click', '.view-model', function(e) {
                e.preventDefault();

                var url = $(this).attr('data-url');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: url,
                    type: 'get',
                    beforeSend: function() {
                        $('#merchant-list').html('');
                        toastr.clear();
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#merchant-model').modal('show');
                            $('#merchant-list').html(data.html);
                        } else {
                            if (data.errors) {
                                $.each(data.errors, function(key, value) {
                                    $('#validation-errors').append(
                                        '<div class="alert alert-danger">' + value +
                                        '</div');
                                });
                            } else {
                                $('#validation-errors').append(
                                    '<div class="alert alert-danger">' + data.message +
                                    '</div');

                            }
                            toastr.error(data.message);
                        }
                    },
                    fail: function(err) {
                        toastr.error(err);
                    },
                    error: function(jqXHR, exception) {
                        $('#validation-errors').append(
                            '<div class="alert alert-danger">something went wrong, please try again</div'
                        );
                        toastr.error('something went wrong, please try again');
                    }
                });
            });

            // revert confirm
            $(document).on('click', '.mass-mid-revert', function(e) {
                e.preventDefault();

                var id = $(this).data('id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('mass-mid.revertConfirm') }}',
                    type: 'post',
                    data: {
                        id: id
                    },
                    beforeSend: function() {
                        $('#validation-errors').html('');
                        $(document).find('#id').val(id);
                        toastr.clear();
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#mass-mid-modal').modal('show');
                            $('#mass-mid-message').text(data.message);
                        } else {
                            if (data.errors) {
                                $.each(data.errors, function(key, value) {
                                    $('#validation-errors').append(
                                        '<div class="alert alert-danger">' + value +
                                        '</div');
                                });
                            } else {
                                $('#validation-errors').append(
                                    '<div class="alert alert-danger">' + data.message +
                                    '</div');

                            }
                            toastr.error(data.message);
                        }
                    },
                    fail: function(err) {
                        toastr.error(err);
                    },
                    error: function(jqXHR, exception) {
                        $('#validation-errors').append(
                            '<div class="alert alert-danger">something went wrong, please try again</div'
                        );
                        toastr.error('something went wrong, please try again');
                    }
                });
            });

            // confirm
            $('#mass-mid-modal').on('hidden.bs.modal', function(e) {
                $(document).find('#id').val('');
            });
        });
    </script>
@endsection
