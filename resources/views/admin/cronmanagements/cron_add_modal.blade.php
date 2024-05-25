<div class="modal-dialog modal-lg modal-lg">
    <div class="modal-content">
        <form class="form" id="add_edit_cron" method="POST" action="{{ route('store.add.cron') }}">{{ csrf_field() }}
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add New Cron</h4>
            </div>
            <div class="modal-body">
                <div class="form-body">
                    <div class="row">
                            <div class="col-lg-10">
                                <div class="form-group" id="div_days_check">
                                    <label for="days_check" class="bold">Days Check</label>
                                    <input class="form-control" id="days_check" placeholder="days_check" name="days_check" type="number" min="0" value="{{(isset($crondata)? $crondata->days_check:'')}}">
                                    <span class="help-block days_check-error"></span>
                                </div>
                            </div>
                    </div>
                    @if(isset($crondata))
                    <input type="hidden" id="id" name="id" value="{{ $crondata->id }}">
                    @endif
                    
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="form-group">
                                <label for="days_check" class="bold">Keywords List</label>
                            </div>
                        </div>
                    </div>

                    <div class="field_wrapper">
                        @if(empty($crondata->keywords))

                        <div class="row">
                            <div class="col-lg-10">
                                <div class="form-group" id="div_keywords">
                                    <input class="form-control" type="text" name="keywords[]" value=""/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <a href="javascript:void(0);" class="add_button" title="Add field">
                                    <input type="button" class="btn btn-info" value="Add">
                                </a>
                            </div>
                        </div>
                        @else
                        @php 
                        $keywordsall = json_decode($crondata->keywords);
                        $i =1;
                        foreach ($keywordsall as $key => $value) {
                           if($i==1) {
                        @endphp
                            <div class="row">
                                <div class="col-lg-10">
                                    <div class="form-group" id="div_keywords">
                                        <input class="form-control" type="text" name="keywords[]" value="{{ $value}}"/>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <a href="javascript:void(0);" class="add_button" title="Add field">
                                        <input type="button" class="btn btn-info" value="Add">
                                    </a>
                                </div>
                            </div>
                        @php } else {  @endphp
                            <div class="row">
                                <div class="col-lg-10">
                                    <div class="form-group" id="div_keywords">
                                        <input class="form-control" type="text" value="{{ $value}}" name="keywords[]" />
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <a href="javascript:void(0);" class="remove_button">
                                        <input type="button" class="btn btn-info" value="Remove">
                                    </a>
                                </div>
                            </div>
                       @php  }
                        $i++;
                        }
                        @endphp
                        @endif
                    </div>

                     
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-large btn-primary" onClick="submitAddCronForm();">
                    Submit <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                </button>
            </div>
        </form>
    </div>
    <!-- /.modal-content --> 
</div>
<!-- /.modal-dialog modal-lg -->
