<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ storage_asset('setup/images/favicon.ico') }}" />
    <link rel="stylesheet" type="text/css" href="{{ storage_asset('setup/css/bootstrap.css') }}">
    <title>{{ config('app.name') }} | Show Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            background-color: #e5dbd9;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        h1, h2 {
            margin-top: 0;
        }

        .section {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
/*            margin-bottom: 20px;*/
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
        }

        th {
/*            background-color: #46344E;*/
/*            color: #fff;*/
        }

        .total-row {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> <img src="{{ storage_asset('setup/images/Logo.png') }}" width="260px"></h1>
        </div>
        <div class="section">
            <h2>Merchant Details</h2>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Merchant Name:</strong> {{ $data->business_name }}</p>
                    <p><strong>Settlement Date:</strong> {{ $data->start_date }} to {{ $data->end_date }}</p>
                </div>
                <div class="col-md-6 text-right">
                    <p><strong>Date:</strong> {{ $data->date }}</p>
                </div>
            </div>
        </div>
        <hr>
        <?php
            $totalPayoutReport = 0;
            ?>
            @foreach ($childData as $key => $value)
                <?php
                $totalPayoutReport += $value->net_settlement_amount_in_usd;
                ?>
                <div class="section">
                    <!-- <h4>Transaction Summary For {{ $value->currency }}</h4> -->
                    <table>
                        <thead>
                            <tr>
                                <th>Transaction Summary For {{ $value->currency }}</th>
                                <th>Count</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Successful transaction</td>
                                <td>{{ number_format($value->approve_transaction_count, 0) }}</td>
                                <td class="text-right">{{ round($value->approve_transaction_sum, 2) }}</td>
                            </tr>

                            <tr>
                                <td>Total Declined transaction </td>
                                <td>{{ number_format($value->declined_transaction_count, 0) }}</td>
                                <td class="text-right">{{ round($value->declined_transaction_sum, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Total Chargebacks Transactions</td>
                                <td>{{ number_format($value->chargeback_transaction_count, 0) }}</td>
                                <td class="text-right" style="color: #F94687; font-weight: 900;">
                                    {{ round($value->chargeback_transaction_sum, 2) }}</td>
                            </tr>

                            <?php
                            $totalAmount = 0;
                            $totalCount = 0;
                            if ($value->remove_past_chargebacks > 0) {
                                $totalCount += number_format($value->remove_past_chargebacks, 0);
                                $totalAmount += $value->past_chargebacks_sum;
                            }
                            ?>
                            <tr>
                                <td>Total Refunds Transactions</td>
                                <td>{{ number_format($value->refund_transaction_count, 0) }}</td>
                                <td class="text-right" style="color: #F94687; font-weight: 900;">
                                    {{ round($value->refund_transaction_sum, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Total Dispute Transactions</td>
                                <td>{{ number_format($value->flagged_transaction_count, 0) }}</td>
                                <td class="text-right" style="color: #F94687; font-weight: 900;">
                                    {{ round($value->flagged_transaction_sum, 2) }}
                                </td>
                            </tr>
                            <?php
                            if ($value->remove_past_flagged > 0) {
                                $totalCount += number_format($value->remove_past_flagged, 0);
                                $totalAmount += $value->past_flagged_sum;
                            }
                            ?>
                            
                            <?php
                            if ($value->return_fee > 0) {
                                $totalCount += $value->return_fee_count;
                                $totalAmount += $value->return_fee;
                            }
                            ?>
                            @if ($totalCount > 0)
                                <tr>
                                    <td>Reversed Transaction Value</td>
                                    <td>{{ number_format($totalCount, 0) }}</td>
                                    <td class="text-right" style="color: green; font-weight: 900;">{{ round($totalAmount, 2) }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td><strong>Deductions</strong></td>
                                <td><strong>Rate</strong></td>
                                <td class="text-right"><strong>Amount</strong></td>
                            </tr>
                            <tr>
                                <td>Rolling Reserve (180 Days)</td>
                                <td>{{ $data->rolling_reserve_paercentage }} %</td>
                                <td class="text-right" style="color: #F94687; font-weight: 900;">
                                    {{ $value->rolling_reserve }}
                                </td>
                            </tr>
                            <tr>
                                <td>Total Transaction Fee</td>
                                <td>{{ $data->transaction_fee_paercentage }}</td>
                                <td class="text-right">{{ $value->transaction_fee }}</td>
                            </tr>
                            <tr>
                                <td>Refund Fee</td>
                                <td>{{ $data->refund_fee_paercentage }}</td>
                                <td class="text-right">{{ $value->refund_fee }}</td>
                            </tr>
                            <tr>
                                <td>Chargeback Fee</td>
                                <td>{{ $data->chargebacks_fee_paercentage }}</td>
                                <td class="text-right">{{ $value->chargeback_fee }}</td>
                            </tr>
                            
                            <tr>
                                <td>Dispute Fee</td>
                                <td>{{ $data->flagged_fee_paercentage }}</td>
                                <td class="text-right">{{ $value->flagged_fee }}</td>
                            </tr>
                            <tr>
                                <td>Retrieval Fee</td>
                                <td>{{ $data->retrieval_fee_paercentage }}</td>
                                <td class="text-right">{{ $value->retrieval_fee }}</td>
                            </tr>
                            <tr>
                                <td>Merchant Discount Rate </td>
                                <td>{{ $data->merchant_discount_rate }} %</td>
                                <td class="text-right" style="color: #F94687; font-weight: 900;">
                                    {{ $value->mdr }}
                                </td>
                            </tr>
                            @if($value->past_flagged_fee > 0)
                            <tr>
                                <td style="padding-bottom:15px;">Reversed fee</td>
                                <td style="padding-bottom:15px;">0</td>
                                <td class="text-right" style="color: green; font-weight: 900;padding-bottom:15px;">
                                    {{ $value->past_flagged_fee }}
                                </td>
                            </tr>
                            @endif
                            <!-- Other deduction rows go here -->
                        </tbody>
                    </table>
                <!-- </div>
                <div class="section"> -->
                    <!-- <h2>Total Payout</h2> -->
                    <table>
                        <tbody>
                            <tr class="greenbg">
                                <!-- <td colspan="2"><strong>Total Amount</strong></td> -->
                                <td colspan="3" class="text-right" style="font-weight: 900;"><strong>Total Amount: </strong>{{ $value->net_settlement_amount }}</td>
                            </tr>
                            @if($value->currency != "USD")
                            <tr class="total-row">
                                <!-- <td colspan="2"><strong>Total Amount in USD</strong></td> -->
                                <td colspan="3" class="text-right"><strong>Total Amount in USD: </strong><strong>{{ $value->net_settlement_amount_in_usd }}</strong></td>
                            </tr>
                            @endif
                            <!-- Other total payout rows go here -->
                        </tbody>
                    </table>
                </div>
            @endforeach
        <div class="section">
            <!-- <h2>Total Payout</h2> -->
            <?php
                $finalSettlementSub = $totalPayoutReport - $data->pre_arbitration_fee;
                $settlement_fee = $data->user->settlement_fee ?? 2.5;
                
                $final_settlement = $finalSettlementSub - ($finalSettlementSub * $settlement_fee) / 100;
            ?>
            <table>
                <tbody>
                    <tr class="total-row">
                        <td colspan="2"><strong>Total Amount in USD</strong></td>
                        <td class="text-right"><strong>{{ $totalPayoutReport }}</strong></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="2"><strong>Net Payable in USD (With {{ $settlement_fee }}% Fee Deducted)</strong></td>
                        <td class="text-right"><strong>{{ round($final_settlement, 2) }}</strong></td>
                    </tr>
                    <!-- Other total payout rows go here -->
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
