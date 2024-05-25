@component('mail::message')
    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <p style="text-transform: capitalize;">Dear Admin</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>I hope this email finds you in good health and spirits. As per our usual practice, I am sending you the
                    daily MID's volume report .</p>
                <p><strong>Please find {!! $date !!} total volume in USD below.</strong></p>
                <table border="0" cellpadding="5" cellspacing="0"
                    style="width: 100%; text-align: left;  padding: 15px 15px 0px 15px; border-radius: 3px;">
                    <tbody>
                        <tr>
                            <td>
                                <p><strong>Mid Name</strong></p>
                            </td>
                            <td>
                                <p><strong>Volume in USD</strong></p>
                            </td>
                        </tr>
                        @if (count($data) > 0)
                            @php
                                $totalVolume = 0;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    $totalVolume = $totalVolume + $item->total_vol;
                                @endphp
                                <tr>
                                    <td>
                                        <p>{{ $item->bank_name }}</p>
                                    </td>
                                    <td>
                                        <p>{!! $item->total_vol !!}</p>
                                    </td>
                                </tr>
                            @endforeach
                            <hr />
                            <tr>
                                <td>
                                    <p> <strong>Total Volume</strong> </p>
                                </td>
                                <td>
                                    <p> <strong>{{ number_format($totalVolume, 2, '.', '') }} USD</strong></p>
                                </td>
                            </tr>
                        @else
                            <p>No Processing found.</p>
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
@endcomponent
