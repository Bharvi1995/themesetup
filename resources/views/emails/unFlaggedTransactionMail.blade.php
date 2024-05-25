@component('mail::message')

{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => ''])
<h1>{{ config('app.name') }}</h1>
@endcomponent
@endslot

<style>
  table {
    border-collapse: separate;
    mso-table-lspace: 0pt;
    mso-table-rspace: 0pt;
    width: 100%;
  }

  p {
    margin: 0px;
  }

  table td {
    font-family: sans-serif;
    font-size: 14px;
    vertical-align: top;
  }

  /* -------------------------------------
      BUTTONS
  ------------------------------------- */
  .btn {
    box-sizing: border-box;
    width: 100%;
  }

  .btn>tbody>tr>td {
    padding-bottom: 15px;
  }

  .btn table {
    width: auto;
  }

  .btn table td {
    border-radius: 5px;
    text-align: center;
  }

  .btn a {
    border: solid 1px #3498db;
    border-radius: 5px;
    box-sizing: border-box;
    color: #3498db;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    font-weight: bold;
    margin: 0;
    padding: 12px 25px;
    text-decoration: none;
    text-transform: capitalize;
  }

  /* -------------------------------------
      RESPONSIVE AND MOBILE FRIENDLY STYLES
  ------------------------------------- */
  @media only screen and (max-width: 620px) {
    table[class=body] h1 {
      font-size: 28px !important;
      margin-bottom: 10px !important;
    }

    table[class=body] p,
    table[class=body] ul,
    table[class=body] ol,
    table[class=body] td,
    table[class=body] span,
    table[class=body] a {
      font-size: 16px !important;
    }

    table[class=body] .wrapper,
    table[class=body] .article {
      padding: 10px !important;
    }

    table[class=body] .content {
      padding: 0 !important;
    }

    table[class=body] .container {
      padding: 0 !important;
      width: 100% !important;
    }

    table[class=body] .main {
      border-left-width: 0 !important;
      border-radius: 0 !important;
      border-right-width: 0 !important;
    }

    table[class=body] .btn table {
      width: 100% !important;
    }

    table[class=body] .btn a {
      width: 100% !important;
    }

    table[class=body] .img-responsive {
      height: auto !important;
      max-width: 100% !important;
      width: auto !important;
    }
  }
</style>

<table role="presentation" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <p style="text-transform: capitalize;">Hi,</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>This mail is in regards to your suspicious transaction.</p><br>
      <p>We are glad to inform you that the acquiring bank has removed suspicion on your transaction after completing
        its due diligence.</p><br>
      <p>Please note, this transaction will be counted in your next eligible settlement.</p><br>
      <table border="0" cellpadding="5" cellspacing="0"
        style="width: 100%; text-align: left; background-color: #f8f8f8; padding: 15px 15px 0px 15px; border-radius: 3px;">
        <tbody>
          <tr>
            <td>
              <p><strong>Card Type</strong></p>
            </td>
            <td>
              <p><span>{!! $card_type !!}</span></p>
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
              <p><strong>Transaction Date</strong></p>
            </td>
            <td>
              <p><span>{!! $flagged_date !!}</span></p>
            </td>
          </tr>
          <tr>
            <td><strong>Amount</strong></td>
            <td><span>{!! $amount !!} {!! $currency !!}</span></td>
          </tr>
        </tbody>
      </table>
      </p>
    </td>
  </tr>
</table>
{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}
@endcomponent
@endslot

@endcomponent