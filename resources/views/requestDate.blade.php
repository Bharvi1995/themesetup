@php
    if (isset($_GET['start_date']) && $_GET['start_date'] != '') {
        $start = explode(' ', $_GET['start_date']);
        $_GET['start_date'] = date('Y-m-d', strtotime($start[0]));
    }
    if (isset($_GET['end_date']) && $_GET['end_date'] != '') {
        $end = explode(' ', $_GET['end_date']);
        $_GET['end_date'] = date('Y-m-d', strtotime($end[0]));
    }
    if (isset($_GET['refund_start_date']) && $_GET['refund_start_date'] != '') {
        $start = explode(' ', $_GET['refund_start_date']);
        $_GET['refund_start_date'] = date('d-m-Y', strtotime($start[0]));
    }
    if (isset($_GET['refund_end_date']) && $_GET['refund_end_date'] != '') {
        $end = explode(' ', $_GET['refund_end_date']);
        $_GET['refund_end_date'] = date('d-m-Y', strtotime($end[0]));
    }
    if (isset($_GET['chargebacks_start_date']) && $_GET['chargebacks_start_date'] != '') {
        $start = explode(' ', $_GET['chargebacks_start_date']);
        $_GET['chargebacks_start_date'] = date('d-m-Y', strtotime($start[0]));
    }
    if (isset($_GET['chargebacks_end_date']) && $_GET['chargebacks_end_date'] != '') {
        $end = explode(' ', $_GET['chargebacks_end_date']);
        $_GET['chargebacks_end_date'] = date('d-m-Y', strtotime($end[0]));
    }
    if (isset($_GET['flagged_start_date']) && $_GET['flagged_start_date'] != '') {
        $start = explode(' ', $_GET['flagged_start_date']);
        $_GET['flagged_start_date'] = date('d-m-Y', strtotime($start[0]));
    }
    if (isset($_GET['flagged_end_date']) && $_GET['flagged_end_date'] != '') {
        $end = explode(' ', $_GET['flagged_end_date']);
        $_GET['flagged_end_date'] = date('d-m-Y', strtotime($end[0]));
    }
    if (isset($_GET['transaction_start_date']) && $_GET['transaction_start_date'] != '') {
        $end = explode(' ', $_GET['transaction_start_date']);
        $_GET['transaction_start_date'] = date('d-m-Y', strtotime($end[0]));
    }
    if (isset($_GET['transaction_end_date']) && $_GET['transaction_end_date'] != '') {
        $end = explode(' ', $_GET['transaction_end_date']);
        $_GET['transaction_end_date'] = date('d-m-Y', strtotime($end[0]));
    }
@endphp
