<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
    <!-- Form -->
    <form action="{{ route('symoco.authPage') }}" method="POST" id="symocoAuthForm">
        @csrf
        <input type="hidden" name="session_id" value="{{ $id }}" />
        <input type="hidden" name="card" value="{{ $card }}" />
        <input type="hidden" name="fingerprint" value="" id="fingerprint" />
    </form>

    <!--- Error Form -->
    <form action="{{ route('symoco.fingerprint.error') }}" method="POST" id="symocoErrorForm">
        @csrf
        <input type="hidden" name="session_id" value="{{ $id }}" />
        <input type="hidden" name="card" value="{{ $card }}" />
        <input type="hidden" name="fingerprint" value="" id="fingerprint" />
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
        integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://pay.symoco.com/js/fp.min.js"></script>
    <script>
        $(document).ready(function() {
            try {
                SYMOCO.Fingerprint.init((fingerprint) => {
                    if (fingerprint) {
                        $("#fingerprint").val(fingerprint)
                        $("#symocoAuthForm").submit();
                    } else {
                        $("#symocoErrorForm").submit();
                    }
                });
            } catch (error) {
                $("#symocoErrorForm").submit();
            }

        });
    </script>
</body>

</html>
