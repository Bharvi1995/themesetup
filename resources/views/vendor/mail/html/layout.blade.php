<html>

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <style>
        .custom-btn {
            background-color: #9B786F;
            padding: 10px 30px;
            border-radius: 6px;
            line-height: 60px;
            color: #FFFFFF;
            font-weight: bold;
            border: 1px solid #9B786F;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
            text-decoration: none;
        }

        p {
            margin: 0px;
            line-height: 26px;
            margin-bottom: 10px !important;
            color: #5a5a5a !important;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body
    style="height: 100%; background-color: #f8f8f8;font-family: 'Poppins', sans-serif;width: 100%;margin: auto; border-radius: 15px;">
    <main>
        <table style="padding: 30px 60px 0px 60px; width: 100%; margin-bottom: 30px;">
            <tr>
                <td>
                    <img src="https://gateway.testpay.com/storage/setup/images/Logo.png" style="margin-bottom: 30px;  width: 250px;">
                </td>
            </tr>

            <tr>
                <td style="background: #FFFFFF; border-radius: 5px; padding: 30px;color:#5a5a5a; border-bottom: 1px solid #9B786F;">
                    <div style="padding-bottom: 30px;color: #5a5a5a;">
                        {{ Illuminate\Mail\Markdown::parse($slot) }}

                        {{ $subcopy ?? '' }}
                    </div>

                    <p style="margin-bottom: 0px;color: #5a5a5a;">
                        Best Regards,<br>
                        <span style="color: #9B786F;">The {{ config('app.name') }}</span> Team
                    </p>
                </td>
            </tr>
        </table>
        <table style="width: 100%; padding: 0px 60px 60px 60px; color: #9B786F; text-align: center;">
            <tr>
                <td style="width: 100%;">
                    If you have any queries, please feel free to reach out to us:  &nbsp; Email: <a href="#" style="color: #9B786F;">{{ config('app.email_support') }}</a>
                    </p>
                </td>
            </tr>
        </table>
    </main>
</body>

</html>