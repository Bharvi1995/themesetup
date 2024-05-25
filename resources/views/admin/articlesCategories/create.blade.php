@extends('layouts.appAdmin')

@section('content')
<div id="section_one">
    <div class="heading-title">
        <h3> Create Category </h3>
    </div>
</div>
{!! Form::open(array('route' => 'article-categories.store','method'=>'POST','class' => 'form form-horizontal','id'=>'articalcategory-form')) !!}
<div class="common-section mb-5">
   <div class="col-xl-12 col-sm-12 col-md-12 col-12">
      <div class="heading-title mb-4">
            <h3 class="mb-0" > Create Category </h3>
            <a href="{{ route('article-categories.index') }}" class="btn btn-yellow">Back</a>
      </div>
        <div class="col-xl-12 col-md-12 col-sm-12 col-12 mt-2 p-0">
            <div class="row mx-auto mt-3">
                <div class="col-xl-6 col-md-12 col-sm-12 col-12">
                    <div class="form-group">
                        <label for="text">Title</label>
                        {!! Form::text('title', Input::get('title'), array('placeholder' => 'Enter Title','class' => 'form-control')) !!}
                        @if ($errors->has('title'))
                            <span class="text-danger help-block form-error">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="text">Meta keyword</label>
                        {!! Form::textarea('meta_keyword', Input::get('meta_keyword'), array('placeholder' => 'Enter Meta Keyword','class' => 'form-control', 'rows' => 5)) !!}
                        @if ($errors->has('meta_keyword'))
                            <span class="text-danger help-block form-error">
                                <strong>{{ $errors->first('meta_keyword') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="text">Meta description</label>
                        {!! Form::textarea('meta_description', Input::get('meta_description'), array('placeholder' => 'Enter Meta Description','class' => 'form-control', 'rows' => 5)) !!}
                        @if ($errors->has('meta_description'))
                            <span class="text-danger help-block form-error">
                                <strong>{{ $errors->first('meta_description') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-md-12 col-sm-12 col-12 mt-2 p-0">
            <div class="row mx-auto mt-3">
                <div class="col-xl-6 col-md-12 col-sm-12 col-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-blue">
                            Save
                        </button>
                        <button type="button" class="btn btn-yellow">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
   </div>
</div>
</div>
{!! Form::close() !!}
@endsection
@section('customScript')
<script type="text/javascript">
    $('#articalcategory-form').submit(function(){
        $(this).find('input:text').each(function(){
            $(this).val($.trim($(this).val()));
        });
    });
</script>
@endsection
