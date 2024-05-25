<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Please do not refresh the page</title>
	<style>
        body {
            background-color: #262626 !important;
            color: #B3ADAD !important;
        }

        #loadingDiv {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid #B3ADAD;
            border-bottom-color: #FF3D00;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
	<div id="loadingDiv">
        <span class="loader"></span>
        <p>Loading ...</p>
    </div>
	<form action="{{ route('itexpay.submit', ['id' => $session_id, 'encrypt' => $encrypt]) }}" method="post" id="pmtform">
        @csrf
        <input type='hidden' name='device' id="device" value="" />
    </form>
	<script type="text/javascript" src="{{ storage_asset('js/device-uuid.min.js') }}"></script>
	<script type="text/javascript">
	    var uniqueId = new DeviceUUID().get();
	    document.getElementById('device').value = uniqueId;
	    document.getElementById('pmtform').submit();
	</script>
</body>
</html>
