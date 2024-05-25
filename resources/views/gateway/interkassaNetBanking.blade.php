<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }} | InterKassa Payment</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ storage_asset('NewTheme/images/favicon.ico') }}">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ storage_asset('/theme/vendor/select2/css/select2.min.css') }}">
    <style type="text/css" media="screen">
        body {
            background-color: #F9F9F9;
        }

        .form-group label {
            color: #000;
        }

        .page-background {
            padding: 2em;
            box-shadow: 0 0 35px 0 rgb(154 161 171 / 15%);
            border-radius: 5px;
            background-color: #ffffff;
        }

        .black-btn {
            background-color: #000;
            color: #fff;
        }

        .form-control {
            border-radius: 0px;
        }

        .select2-container .select2-selection--single {
            height: 34px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 0;
            border: 1px solid #f0f1f5;
        }
    </style>

</head>

<body>
    <div class="container" style="padding-top: 12%;">
        @if(isset($error) && !empty($error))
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4 col-md-offset-4 text-center page-background">
                    <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}" />
                    <br /><br />
                    <div class="alert alert-danger">
                        <strong>Sorry!</strong> {{$error}}
                    </div>
                </div>
            </div>
        </div>
        @else

        <div class="row">
            <div class="col-md-12">

                <div class="col-md-4 col-md-offset-4 text-center page-background">
                    @if (\Session::has('danger'))
                    <div class="alert alert-danger">
                        {!! \Session::get('danger') !!}
                    </div>
                    @endif
                    <img style="max-width: 60%;margin-top: 10px;" src="{{ storage_asset('NewTheme/images/Logo.png') }}" />
                    <br /><br />
                    <form action="{{route('interkassa-net-banking-confirmation-submit')}}" method="post"
                        id="paymentForm" name="paymentForm">
                        @csrf
                        <div class="form-group">
                            <select class="select2 form-control @error('bank') is-invalid @enderror" name="bank"
                                data-size="7" data-live-search="true" data-title="Select Bank" id="bank"
                                data-width="100%">
                                <option selected disabled>Select Bank</option>
                                @foreach($bankList as $key=> $bank)
                                <option value="{{$key}}">
                                    {{$bank}}</option>
                                @endforeach
                            </select>
                            @error('bank')
                            <div class="text-danger text-left">{{ $message }}</div>
                            @enderror<br />
                        </div>
                        <input type="hidden" name="session_id" value="{{$sessionId}}">
                        <input type="hidden" name="order_id" value="{{$orderId}}">
                        <input type="submit" name="pay" class="form-control btn-danger" value="Submit" id="pay">
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
    <script src="{{ storage_asset('theme/vendor/global/global.min.js') }}"></script>
    <script src="{{ storage_asset('theme/vendor/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(".select2").select2({});
    </script>

</body>

</html>