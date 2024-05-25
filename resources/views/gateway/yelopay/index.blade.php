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
    <form action="{{ $url }}" method="{{ $method }}" id="form">
        @foreach ($params as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach

    </form>
    <script>
        const form = document.getElementById("form");
        form.submit();
    </script>
</body>

</html>
