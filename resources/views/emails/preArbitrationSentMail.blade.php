<html>

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <style>
        .custom-btn {
            background-color: #6683A9;
            padding: 15px 30px;
            border-radius: 30px;
            line-height: 60px;
            color: #FFFFFF;
            font-weight: bold;
            border: 1px solid #fff;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            box-sizing: border-box;
            text-decoration: none;
        }

        p {
            margin: 0px;
            line-height: 26px;
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body style="height: 100%; background-color: #f8f8f8;font-family: 'Nunito', sans-serif;width: 100%;margin: auto;">
    <main>
        <table style="padding: 30px 60px 0px 60px; width: 100%;">
            <tr>
                <td>
                    <img src="{{ config('app.logo_url') }}" style="margin-bottom: 30px;  width: 250px;">
                </td>
            </tr>

            <tr>
                <td style="background: #FFFFFF; border-radius: 5px 5px 0px 0px; padding: 30px;">
                    <div style="padding-bottom: 60px;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <p style="text-transform: capitalize;">Dear Team,</p><br>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p>Please be advised that we have received pre arbitration notification from the
                                        issuer bank regarding the transaction/s listed below. This means the issuer bank
                                        has rejected the representment documents and has continued with the dispute.</p>
                                    <br>
                                    <table border="0" cellpadding="5" cellspacing="0"
                                        style="width: 100%; text-align: left; background-color: #f8f8f8; padding: 15px 15px 0px 15px; border-radius: 3px;">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p> <strong>Amount</strong></p>
                                                </td>
                                                <td>
                                                    <p> <span>{!! $amount !!} {!! $currency !!}</span></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><strong>Card Number</strong></p>
                                                </td>
                                                <td>
                                                    <p><span>{!! $card_no !!}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><strong>Transaction ID</strong></p>
                                                </td>
                                                <td>
                                                    <p><span>{!! $order_id !!}</span></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><strong>Merchant Name</strong></p>
                                                </td>
                                                <td>
                                                    <p><span>{!! $business_name !!}</span></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><strong>Client</strong></p>
                                                </td>
                                                <td>
                                                    <p><span>{!! $first_name !!} {!! $last_name !!}</span></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                    <p>Please let us know how you wish to proceed.</p>
                                    <ul>
                                        <li>To accept the pre-arbitration meaning that you are accepting the chargeback
                                            and the case will be closed in card holder's favour.</li>
                                        <li>You declined the pre-arbitration and to proceed with the dispute. Please
                                            note that in this case the issuer bank may escalate the dispute to a visa
                                            ruling and the decision for the outcome will be taken by Visa arbitration
                                            committee please note that if they rule in favour of the card holder you
                                            will be charged an additional fees of 5000 USD per transaction.</li>
                                    </ul>
                                    <p>Please inform us on your decision by, {{ date('d F Y', strtotime("+2 day")) }}.
                                        Please consider that if we don't receive a response by the due date, we will
                                        accept the pre-arbitration on your behalf.</p><br>
                                    <p> Please click here for more info : <a
                                            href="{{ storage_asset('preArbitration/visa-claims-resolution-efficient-dispute-processing-for-merchants-VBS-14.APR.16.pdf') }}">Pre-Arbitration
                                            Info</a></p><br>
                                    <p>Kindly also advise us whether we should address such correspondence to your or
                                        other team.</p><br>
                                    <p>Thank you in advance.</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        <table style="background-color: #FFF; border-top: 3px solid #6683A9; width: 100%; padding: 15px 60px 0px 60px;">
            <tr>
                <td style="width: 52%; float: left;">
                    <img src="{{ config('app.logo_url') }}" style="width: 120px;">
                    <p style="margin-bottom: 0px;">
                        Regards,<br>
                        <strong>{{ config('app.name') }}</strong> Team
                    </p>
                </td>
                <td style="width: 44%; float: right;">
                    <h3 style="margin: 0px;">In case of any query, reach out to us:-</h3>
                    <p style="margin-bottom: 0px;">
                        E-Mail: {{ config('app.email_support') }}
                    </p>
                </td>
            </tr>
        </table>
    </main>
</body>

</html>