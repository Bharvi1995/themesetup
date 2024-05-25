<!doctype html>
<html lang="en" class="light">
    <head>
        <meta charset="utf-8">
        <link href="{{ storage_asset('theme/assets/dist/images/logo-sm.png') }}" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Checkout Response</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

		<style type="text/css">
			body{
			    background-color: #354E96;
			    padding: 30px 0px;
			    font-size: .875rem;
			    font-family: 'Roboto', sans-serif;

			}
			.container{
				background-color: #F0F4F8;
				box-shadow: 0px 0px 3px 0px #FFF;
				border-radius: 15px;
			}
			.header{
				background-color: #FFF;
				border-radius: 15px 15px 0px 0px;
				padding: 15px 0px;
			}
			.cbody{
				padding: 120px 0px;
			}
			.cbody h2{
				font-size: 64px;
				border: 1px solid #28A745;
				width: 150px;
				margin: auto;
				border-radius: 50%;
				height: 150px;
				line-height: 150px;
			}
			.mt-20 {
			    margin-top: 50px !important
			}
		</style>
	</head>
    <body>
    	<div class="container">
    		<div class="row">
    			<div class="col-md-12 text-center header">
    				<h2>{{ $store->name }}</h2>
    			</div>
    			<div class="col-md-12">
    				<div class="row">
			            <div class="col-md-12 text-center cbody">
			            	@if ($input['status'] == 'success')
			                <h2><i class="fa fa-thumbs-o-up text-success"></i></h2>
			                <h3 class="mt-3">Payment Successful</h3>
			                <a href="{{ route('stores.index', $store->slug) }}">Back to My Store</a>
			            	@else
			            	<h2><i class="fa fa-thumbs-o-down text-danger"></i></h2>
			                <h3 class="mt-3">{{ $input['response'] }}</h3>
			                <a href="{{ route('store.cart', $store->slug) }}">Back to Checkout</a>
			            	@endif
			            </div>
			        </div>
    			</div>
    		</div>	
    	</div>

    	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
</html>