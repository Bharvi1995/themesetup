<html>

<head>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            background-color: #FFF;
            padding-top: 2cm;
            padding-left: 1.5cm;
            padding-right: 1.5cm;
            padding-bottom: 2cm;
            font-size: 18px;
        }

        body p {
            line-height: 26px;
        }

        body li {
            line-height: 26px;
            margin-bottom: 3px;
            font-size: 18px;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1.3cm;
            padding: 15px 15px;
            /** Extra personal styles **/
            background-color: #FFF;
            border-bottom: 1px solid #9B786F;
            color: white;
            text-align: left;
            line-height: 1.5cm;
        }

        .table-rate {
            border-spacing: 0;
            border-collapse: collapse;
            width: 100%;
        }

        .table-rate>tbody>tr>td,
        .table-rate>tbody>tr>th,
        .table-rate>thead>tr>td,
        .table-rate>thead>tr>th {
            border: 2px solid #ddd;
            padding: 15px;
            text-align: left;
        }

        .table-details {
            border-spacing: 0;
            border-collapse: collapse;
            width: 100%;
        }

        .table-details>tbody>tr>td,
        .table-details>tbody>tr>th,
        .table-details>thead>tr>td,
        .table-details>thead>tr>th {
            border: 2px solid #ddd;
            padding: 15px;
            text-align: left;
            width: 33.33%;
        }
    </style>
</head>

<body>
    <header>
        <img src="https://gateway.testpay.com/storage/setup/images/Logo.png" width="170px" />
    </header>

    <main>
        <div class="content">
            <h3 class="text-center">Payment Service Agreement</h3>
            
            <!-- <p>{{ $data->business_contact_first_name." ". $data->business_contact_last_name }},<br>
                {{ $data->business_name }},<br>
                {{ $data->country }},<br>
                {{ $data->business_address1 }},<br>
                {{ $data->email }},<br>
                {{ $data->phone_no }},<br>
                {{ $data->website_url }},<br>
            </p> -->
            <p><strong>IT SOLUTION TEAM LTD.</strong>, hereinafter referred to as the <strong>“Service Provider”</strong>, registered in <strong>United Kingdom</strong> under the company number <strong>Registered number</strong> and whose registered office address is <strong>Address,</strong> represented by, Iryna Korenna acting on the basis of the Articles of Association, on the one hand, and <strong>{{ $data->business_name }}</strong> trading as <strong>{{ $data->website_url }}</strong>, hereinafter referred to as the “Merchant”, registered in <strong>{{ $data->country }}</strong> under the company number <strong>{{ $data->business_category }}</strong> and whose registered office address is <strong>{{ $data->business_address1 }}</strong>, represented by <strong>{{ $data->business_contact_first_name." ". $data->business_contact_last_name }}</strong>, acting on the basis of the Articles of Association, on the other hand,</p> together referred to as the “Parties”, and individually as the “Party”, have entered into this Payment Service Agreement on <strong>{{ date('d-m-Y') }}</strong> (hereinafter referred to as the “Agreement”) as follows:
            <h4>A. General Terms</h4>
            <p><strong>Confidentiality:</strong> Both parties agree to maintain the confidentiality of all information disclosed during the term of this agreement. Any information disclosed by one party to the other party, whether orally or in writing, that is designated as confidential or that reasonably should be understood to be confidential given the nature of the information and the circumstances of disclosure. Confidential Information may include, without limitation, technical, financial, business, and customer information, as well as any proprietary algorithms, processes, or methodologies.</p>
            <p>The Parties agree to hold the Confidential Information in strict confidence and not to disclose such Confidential Information to any third party without the prior written consent of the Disclosing Party.</p>
            <p><strong>Compliance:</strong> The Merchant agrees to comply with all applicable laws and regulations related to the use of the Payment Gateway Solution.</p>
            <p><strong>Ownership:</strong> The Provider retains all rights, title, and interest in the Payment Gateway Solution and associated intellectual property rights.</p>
            <p><strong>Termination:</strong> This agreement may be terminated by either party with prior written notice as outlined in the termination clause.</p>
            <h4>B. Services</h4>
            <p><strong>Payment Gateway Access:</strong> The Provider shall grant the Merchant access to its Payment Gateway Solution for the purpose of processing payments securely.</p>
            <p><strong>Technical Support:</strong> The Provider shall provide technical support to the Merchant to ensure the smooth integration and operation of the Payment Gateway Solution.</p>
            <p><strong>Security:</strong> The Provider shall implement and maintain robust security measures to safeguard the integrity and confidentiality of payment transactions.</p>
            <p><strong>Transaction Reporting:</strong> The Provider shall furnish the Merchant with regular reports detailing transaction history, settlement status, and any relevant analytics.</p>
            <h4>C. Terms and Conditions</h4>
            <p><strong>Fees:</strong> The Merchant agrees to pay the Provider fees for the use of the Payment Gateway Solution as outlined in the annexure attached hereto.</p>
            <p><strong>Dispute Resolution:</strong> Any disputes arising under this agreement shall be resolved through good faith negotiations between the parties.</p>
            <p><strong>Indemnification:</strong> The Merchant agrees to indemnify and hold harmless the Provider from any claims, damages, or liabilities arising from the Merchant's use of the Payment Gateway Solution.</p>
            <p><strong>Limitation of Liability:</strong> In no event shall either party be liable to the other for any indirect, incidental, consequential, or punitive damages arising from or related to this agreement.</p>
            <h4>D. Termination</h4>
            <p><strong>Termination for Convenience:</strong> Either party may terminate this agreement with 30-Day written notice to the other party.</p>
            <p><strong>Termination for Cause:</strong> Either party may terminate this agreement immediately in the event of a material breach by the other party, provided that written notice of such breach is given.</p>
            <!-- <div style="page-break-before:always">&nbsp;</div> -->
            <h4>E. Governing Law</h4>
            <p>This agreement shall be governed by and construed in accordance with the laws of {{ config('custom.insert_jurisdiction') }}, without regard to its conflict of law provisions.</p>
            <h4>F. FORCE MAJEURE.</h4>
            <p>Neither Party will be liable for inadequate performance to the extent caused by a condition that was beyond the Party’s reasonable control. Force majeure circumstances may include, in particular: war (declared or not), armed conflict or its serious threat (including but not limited to hostile attack, blockades, military embargo), hostilities, invasion actions of a hostile state, extensive military mobilization, acts of terrorism; civil war, riot, insurrection and revolution, military or usurped power, insurrection, civil disturbance or disorder, street riots, lockdowns, acts of civil disobedience; curfew, expropriation, imposition of a work ban; natural disasters, epidemics, pandemics, lockdowns, natural disasters, power blackouts and Internet disturbance; labor disturbances, including but not limited to boycotts, strikes and lockouts.</p>
            <p>In this case, the term for the fulfillment of obligations under the Agreement is proportionately suspended for the duration of such circumstances and their consequences;</p>
            <p>The Party using the present force majeure clause shall immediately notify the other Party of such event and related circumstances with reasonable explanation in written for;</p>
            <p>If the impossibility of full or partial fulfillment of the obligations under the Agreement exists for more than 3 (three) months, the Service Provider has the right to terminate the Agreement in whole or in part without the obligation to reimburse possible losses (including expenses) of the Merchant.</p>
            <p>IN WITNESS WHEREOF, the parties hereto have executed this agreement as of the date first above written.</p>
            <table style="width: 100%; position: absolute; bottom: 10px;">
                <tr>
                    <td style="width: 50%;">
                        <p>Signature: testpay</p>
                    </td>
                    <td>
                        <p>Signature: .....................</p>
                    </td>
                </tr>
                 <tr>
                    <td style="width: 50%;">
                        <p>Date: {{ date('d-m-Y') }}</p>
                    </td>
                    <td>
                        <p>Date: .....................</p>
                    </td>
                </tr>
            </table>
        </div>
        <div style="page-break-before:always">&nbsp;</div>
        <h4>Annexure: Payment Gateway Rates</h4>
        <ul>
            <li>
                <p>Annexure detailing the rates and fees applicable for using the Payment Gateway Solution</p>
            </li>
        </ul>
        <table class="table-rate text-center">
            <tr>
                <th>Description</th>
                <th>Rates / Fees</th>
            </tr>
            <tr>
                <td>Rolling reserve (%)</td>
                <td>{{ $data->rolling_reserve_paercentage }}%</td>
            </tr>
            <tr>
                <td><b> Cards Fee </b>(%)</td>
                <td>{{ $data->merchant_discount_rate }}%</td>
            </tr>
            <tr>
                <td><b>Settlement Fee ($)</b></td>
                <td>{{ $data->settlement_fee }}%</td>
            </tr>
            <tr>
                <td>Settlement Period</td>
                <td>T +{{ $data->payment_frequency }}</td>
            </tr>
            <tr>
                <td>Settlement Threshold</td>
                <td>{{ $data->minimum_settlement_amount }} USDT</td>
            </tr>
            <tr>
                <td><b>Setup Fee ($)</b></td>
                <td>{{ $data->setup_fee }} USDT</td>
            </tr>
            <tr>
                <td>Transaction Fee ($)</td>
                <td>{{ $data->transaction_fee }} USD</td>
            </tr>
            <tr>
                <td>Refund fee ($)</td>
                <td>{{ $data->refund_fee }} USD</td>
            </tr>
            <tr>
                <td>Chargeback fee ($)</td>
                <td>{{ $data->chargeback_fee }} USD</td>
            </tr>
            <tr>
                <td>Dispute fee ($)</td>
                <td>{{ $data->flagged_fee }} USD</td>
            </tr>
            <tr>
                <td>Retrieval fee ($)</td>
                <td>{{ $data->retrieval_fee }} USD</td>
            </tr>
        </table>
    </main>
</body>

</html>
