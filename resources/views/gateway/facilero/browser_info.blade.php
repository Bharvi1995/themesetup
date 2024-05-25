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
    <form method="POST" action="{{ route('facilero.store.browser.info') }}" id="browserInfoForm">
        @csrf
        <input type="hidden" name="session_id" value="{{ $id }}" />
        <input type="hidden" name="browser_info" id="browserInfo" />
    </form>
    <script>
        var userAgent = navigator.userAgent;
        var browserInfo = {
            'browserColorDepth': window.screen.colorDepth,
            'browser_ip': null, // Client-side JavaScript cannot retrieve the IP address directly
            'browserJavaEnabled': false, // Java applets are no longer widely supported
            'browserLanguage': window.navigator.language,
            'browserScreenHeight': window.screen.height,
            'browserScreenWidth': window.screen.width,
            'browserTimezoneOffset': -new Date().getTimezoneOffset(),
            'user_Agent': userAgent,

        };

        document.getElementById("browserInfo").value = JSON.stringify(browserInfo);
        const form = document.getElementById("browserInfoForm");
        form.submit();
    </script>
</body>

</html>
