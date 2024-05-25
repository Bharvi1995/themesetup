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
        <strong>Please do not refresh the page</strong>
    </div>
    {{-- @php
        // Render an IFRAME to show the ACS challenge (hidden for fingerprint method)
        $style = isset($response['threeDSRequest']['threeDSMethodData']) ? 'display: none;' : '';
        echo "<iframe name=\"threeds_acs\" style=\"height:420px; width:420px; {$style}\"></iframe>\n";
    @endphp --}}

    <div style="display: none;">
        <form action="{{ $response['threeDSURL'] }}" method="POST" id="silentPost">
            @if (isset($response['threeDSRef']))
                <input type="hidden" name="threeDSRef" value="{{ $response['threeDSRef'] }}" />
            @endif
            @if (isset($response['threeDSRequest']))
                @foreach ($response['threeDSRequest'] as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
                @endforeach
            @endif
        </form>
    </div>

    <script>
        window.setTimeout('document.forms.silentPost.submit()', 0);
    </script>
</body>

</html>
