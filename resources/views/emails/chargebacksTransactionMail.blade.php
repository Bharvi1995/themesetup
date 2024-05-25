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

  table td {
    font-family: sans-serif;
    font-size: 14px;
    vertical-align: top;
  }

  p {
    margin: 0px;
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
    border: solid 1px #F44336;
    border-radius: 5px;
    box-sizing: border-box;
    color: #F44336;
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
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <p style="text-transform: capitalize;">Dear {{ $userName }},</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>We are writing to inform you of a dispute that has been raised regarding a recent transaction made by your client. Below are the details:
      </p><br>
      <table border="0" cellpadding="5" cellspacing="0"
        style="width: 100%; text-align: left; background-color: #f8f8f8; padding: 15px 15px 0px 15px; border-radius: 3px;">
        <tbody>
          <tr>
            <td>
              <p><strong>Order ID</strong></p>
            </td>
            <td>
              <p><span>{!! $order_id !!}</span></p>
            </td>
          </tr>
          <tr>
            <td>
              <p><strong>Chargeback Date</strong></p>
            </td>
            <td>
              <p><span>{!! $chargebacks_date !!}</span></p>
            </td>
          </tr>
          <tr>
            <td>
              <p><strong>Transaction Date</strong></p>
            </td>
            <td>
              <p><span>{!! $created_at !!}</span></p>
            </td>
          </tr>
          <tr>
            <td>
              <p> <strong>Amount</strong></p>
            </td>
            <td>
              <p> <span>{!! $amount !!} {!! $currency !!}</span></p>
            </td>
          </tr>
        </tbody>
      </table>
      <br>
      <p>To address and resolve this matter promptly, we kindly request you to connect with your designated Account Manager at your earliest convenience. It is crucial that you take action within the next 72 hours to prevent our company from initiating a dispute from your side with the acquirer
      </p>
     
      <p>Thank you for your cooperation, and we look forward to a prompt resolution.</p>
  </tr>
</table>
<br>

@endcomponent