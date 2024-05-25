<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Please do not refresh the page</title>
    <style>
        body {
            background-color: #f8f8f8 !important;
            color: #000000 !important;
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
            border: 5px solid #dddddd;
            border-bottom-color: #80A1C2;
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
    <form method="{{ $method }}" action="{{ $actionUrl }}" id="redirectForm">


        @foreach ($fields as $field)
            <input type="hidden" name="{{ $field['name'] }}" value="{{ $field['value'] }}" />
        @endforeach
    </form>

    <script>
        const form = document.getElementById("redirectForm");
        form.submit();
    </script>

</body>

</html>
