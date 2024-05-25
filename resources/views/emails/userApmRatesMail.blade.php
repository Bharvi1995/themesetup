@component('mail::message')
    <p style="text-transform: capitalize;">Hello,</p>
    <p>We have assigned you new APM on your account.please check below rates for assigned APM</p>
    <table cellpadding="5" cellspacing="0"
        style="width: 100%; text-align: left; background-color: #f8f8f8; padding: 15px 15px 0px 15px; border-radius: 3px;">
        <thead style="background-color: #f8f8f8; color: #B3ADAD; font-size: 18px; font-weight: bold;padding:5px;">
            <tr>
                <td>APM</td>
                <td>Rates %</td>
            </tr>
        </thead>
        <tbody style="font-size: 16px;padding: 5px;">
            @foreach ($userApms as $index => $userApm)
                <tr style="background-color: {{ $index % 2 == 0 ? '#1C1C1C' : '#f8f8f8' }}; color: #B3ADAD;">
                    <td>
                        @if ($userApm['apm_type'] == '1')
                            Card
                        @elseif ($userApm['apm_type'] == '2')
                            Bank
                        @elseif ($userApm['apm_type'] == '3')
                            Crypto
                        @elseif ($userApm['apm_type'] == '4')
                            UPI
                        @endif
                    </td>
                    <td>{{ $userApm['apm_mdr'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endcomponent
