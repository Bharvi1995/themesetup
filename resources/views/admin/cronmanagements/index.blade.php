@extends('layouts.admin.default')

@section('title')
    Admin Logs
@endsection
@section('customeStyle')
@endsection
@section('content')
@section('breadcrumbTitle')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a> / Cron Management
@endsection
<div class="card  mt-1">
    <div class="card-body p-0 pt-0">
        <div id="allcron_div"></div>
    </div>
    <div class="pagination-wrap">
    </div>
</div>
<div class="modal fade bs-example-modal-center" id="add_cron_modal" tabindex="-1" role="reassignModel" aria-hidden="true">
</div>
@endsection
@section('customScript')
<script type="text/javascript">
    var changeTransactionUnRefund = "{{ route('change-transaction-unRefund') }}";
    var changeTransactionUnChargeback = "{{ route('change-transaction-unChargeback') }}";
    var changeTransactionUnRetrieval = "{{ route('change-transaction-unRetrieval') }}";
    var changeTransactionUnFlagged = "{{ route('change-transaction-unflagged') }}";
    var changeTransactionStatus = "{{ route('change-transaction-status') }}";

    // * Custom JS
    $(document).on('click', '.closeChatBox', function() {
        $('.chatbox').removeClass('active')
    });


    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    var end = new Date(date.getFullYear(), date.getMonth(), date.getDate());

    $(function() {
        var data = [
            [{
                id: 0,
                text: 'black'
            }, {
                id: 1,
                text: 'blue'
            }],
            [{
                id: 0,
                text: '9'
            }, {
                id: 1,
                text: '10'
            }]
        ];
        $('.form-select').select2({
            allowClear: true,
            placeholder: "Select an attribute"
        }).on('change', function() {
            $('#value').removeClass('select2-offscreen').select2({
                data: data[$(this).val()],
                allowClear: true,
                placeholder: "Select a value"
            });
        }).trigger('change');
    });

    function showAllCronForm() {
        $.post("{{ route('show.all.cron') }}", {
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                $('#allcron_div').html(response);
            });
    }
    $(document).ready(function() {
        showAllCronForm();
    });

    function startStopCron(id) {
        var currentStatus = $("#updateButton_" + id).val();
        $.ajax({
            type: "get",
            url: "{{ route('cron.management.startstop') }}",
            data: {
                id: id,
                current_status: currentStatus,
                _token: '{{ csrf_token() }}'
            },
            success: function(responseData) {
                if (responseData.success == 1) {
                    if (responseData.status == 1) {
                        $("#updateButton_" + id).attr('class', 'btn btn-danger');
                        $("#updateButton_" + id).text('Stop');
                        $("#updateButton_" + id).val('1');
                        $("#last_update_date_" + responseData.id).text(responseData.last_run_at);
                        $("#updateDate_" + responseData.id).text(responseData.next_run_time);
                    } else {
                        $("#updateButton_" + id).attr('class', 'btn btn-success');
                        $("#updateButton_" + id).text('Start');
                        $("#updateButton_" + id).val('0');
                    }
                } else if (responseData.success == 2) {
                    alert(responseData.message);
                    if (responseData.status == 1) {
                        $("#updateButton_" + id).attr('class', 'btn btn-danger');
                        $("#updateButton_" + id).text('Stop');
                        $("#updateButton_" + id).val('1');
                        $("#last_update_date_" + responseData.id).text(responseData.last_run_at);
                        $("#updateDate_" + responseData.id).text(responseData.next_run_time);
                    } else {
                        $("#updateButton_" + id).attr('class', 'btn btn-success');
                        $("#updateButton_" + id).text('Start');
                        $("#updateButton_" + id).val('0');
                        $("#last_update_date_" + responseData.id).text(responseData.last_run_at);
                        $("#updateDate_" + responseData.id).text(responseData.next_run_time);
                    }
                } else {
                    alert(responseData.message);
                }
            }
        });
    }

    function showCronEditModal(id) {
        loadEditCronForm(id);
    }

    function loadEditCronForm(id) {
        $.ajax({
            type: "POST",
            url: "{{ route('get.cron.edit.form') }}",
            data: {
                id: id,
                "_token": "{{ csrf_token() }}"
            },
            datatype: 'json',
            success: function(json) {
                $("#add_cron_modal").html(json.html);
                $("#add_cron_modal").modal('show');

            }
        });
    }

    function submitAddCronForm() {
        var form = $('#add_edit_cron');
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            dataType: 'json',
            success: function(json) {
                $("#add_cron_modal").html(json.html);
                $("#add_cron_modal").modal('hide');
                showAllCronForm();
                $(".modal-backdrop.in").hide();
            },
            error: function(json) {
                if (json.status === 422) {
                    var resJSON = json.responseJSON;
                    $('.help-block').html('');
                    $.each(resJSON.errors, function(key, value) {
                        $('.' + key + '-error').html('<strong>' + value + '</strong>');
                        $('#div_' + key).addClass('has-error');
                    });
                } else {}
            }
        });
    }

    $(document).ready(function() {

        var fieldHTML = ` <div class="row">
                            <div class="col-lg-10">
                                <div class="form-group" id="div_keywords">
                                    <input class="form-control" type="text" name="keywords[]" value=""/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <a href="javascript:void(0);" class="remove_button">
                                    <input type="button" class="btn btn-info" value="Remove">
                                </a>
                            </div>
                        </div>`
        var x = 1; //Initial field counter is 1
        //Once add button is clicked
        $(document).on('click', '.add_button', function() {
            $('.field_wrapper').append(fieldHTML); //Add field html
        });

        //Once remove button is clicked
        $(document).on('click', '.remove_button', function(e) {
            e.preventDefault();
            $(this).parent().parent('div').remove(); //Remove field html
        });
    });
</script>
@endsection
