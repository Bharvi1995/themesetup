<html>

<head>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            background-color: #FFF;
            padding: 0.5cm;
        }

        ul {
            padding: 0px;
            margin: 0px;
        }

        li {
            list-style: none;
            margin-bottom: 15px;
        }

        li.close:before {
            content: "XX";
            margin-right: 5px;
        }

        .table-imp {
            background-color: #d1d1d1;
            width: 100%;
            font-weight: bold;
        }

        .table-imp tr td {
            display: table-cell;
            vertical-align: top;
        }

        .table-name {
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <main>
        <h3> Cardholder's Certification of Disputed Transaction <span style="float: right;"> Case ID DIS
                {{ substr($data->order_id, 0, 10) }}{{ $data->user_id }}</span> </h3>
        <p>Cardholder's Certification of Disputed Transaction USER: &tel-rich& / &contatto& <br>In case of disputed
            transactions following one of those reasons mentioned hereafter, could you please return this form back
            within 10 days, duly completed</p>

        <div>
            <table class="table-imp">
                <tr>
                    <td>Important:</td>
                    <td>
                        — In case of disputed transactions following one of the reasons mentioned hereafter, please
                        return this form within 10 days, duly completed <br>
                        — This document is only valid signed by the cardholder<br>
                        — This document is only valid if duly completed in the indicated fields (*)
                    </td>
                </tr>
            </table>
        </div>

        <table class="table-name">
            <tr>
                <td style="width: 60%; padding-right: 15px;">
                    <h4 style="border-bottom: 1px solid #000; margin-bottom: 0px;">{{ $data->first_name }}
                        {{ $data->last_name }}</h4>
                    Name and First Name
                </td>
                <td style="width: 40%;">
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <h4 style="border-bottom: 1px solid #000; margin-bottom: 0px;">&nbsp;</h4>
                                Home ph. number
                            </td>
                            <td>
                                <h4 style="border-bottom: 1px solid #000; margin-bottom: 0px;">&nbsp;</h4>
                                Business ph. number
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td style="width: 60%; padding-right: 15px;">
                    <h4 style="border-bottom: 1px solid #000; margin-bottom: 0px;">{{ $data->card_no }}</h4>
                    Card No.
                </td>
                <td style="width: 40%;">
                    <h4 style="border-bottom: 1px solid #000; margin-bottom: 0px;">{!! date('d M Y', strtotime($data->transaction_date)) !!}</h4>
                    Transaction Date
                </td>
            </tr>
        </table>

        <h3 style="margin-bottom: 0px;">{{ $data->currency }} {{ $data->amount }} </h3>
        Original Amount

        <p style="border-top: 3px solid #000;"><b>Merchant Name — Location</b> &nbsp; &nbsp; &nbsp; &nbsp;
            {{ getBusinessName($data->user_id) }}</p>

        <p>
            <strong>I have examined the transactions shown on my statement of account and I dispute the above mentioned
                sales draft for the following reason:</strong>
        </p>

        <ul>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 1. The
                amount of the sales draft was altered from ______ to ______ (please enclose the documentation)</li>
            <li><i class="fas fa-check-square"></i> 2. I certify that I neither made nor authorized the above mentioned
                transaction.</li>
            <li class="btn-close">3. I certify that I neither made a phone/mail order nor an internet order with my
                credit card.</li>
            <li class="btn-close">4. I certify that I do not recognize the above mentioned transaction. The following
                description is unknown: ______</li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 5. I
                certify that the charge in question is a duplicate/multiple debit to my statement. I made only one
                transaction at this merchant outlet.</li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 6. The
                attached credit slip does not appear on my monthly statement or was posted as a debit on my monthly
                statement.</li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 7. I
                made only one transaction at this merchant (see enclosed copy). I never made nor authorized an
                additional transaction. I certify that my credit card has never been lost or stolen and was always in my
                possession, at the time of the disputed transaction. </li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 8. I
                certify that I made a hotel reservation, but I cancelled it on (date) __________ (cancellation number:)
                __________</li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 9. I
                certify that I have not been in this hotel and that I did not reserve a room there.</li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 10. I
                have never received the merchandise that I have been debited for. I have contacted the merchant (please
                enclose outcome). </li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 11.
                The ordered merchandise was received as defective/not as described (please enclose the relevant
                documentation). I have ❑ contacted the merchant (please enclose outcome). </li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 12. I
                certify that I already cancelled my subscription. Cancellation date : __________</li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 13. a)
                I certify that the merchant did not advise me about Dynamic Currency Conversion (DCC).</li>
            <li><i class="fas fa-square"
                    style="color: #fff; border: 1px solid #000; height: 10px; width: 10px; overflow:hidden;"></i> 14. b)
                I certify that the merchant did not accept my decision to settle the transaction in local currency.</li>
        </ul>

        <table class="table-name">
            <tr>
                <td style="width: 50%; padding-right: 15px;">
                    <h4 style="border-bottom: 1px solid #000; margin-bottom: 0px;">{{ date('d.m.Y') }}</h4>
                    Date
                </td>
                <td style="width: 50%;">
                    <h4 style="border-bottom: 1px solid #000; margin-bottom: 0px;">&nbsp;</h4>
                    &nbsp;
                </td>
            </tr>
        </table>
    </main>
</body>

</html>
