<div class="col-md-12 transaction-summary-tbl">
    <div class="card">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-9" style="padding: 30px 30px 30px 45px;">
                    <div class="header-title">
                        <h5 class="card-title">Transactions Volume Report</h5>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane active" id="SUCCESSFUL">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th width="50px">Currency</th>
                                            <th>Count</th>
                                            <th>Amount</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($TransactionSummary) > 0)
                                            @foreach ($TransactionSummary as $ts)
                                                <tr>
                                                    <td>{{ $ts['currency'] }}</td>
                                                    <td>{{ $ts['success_count'] }}</td>
                                                    <td>{{ $ts['success_amount'] }}</td>
                                                    <td>{{ round($ts['success_percentage'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center text-white" colspan="4">No record found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="DECLINED">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th width="50px">Currency</th>
                                            <th>Count</th>
                                            <th>Amount</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($TransactionSummary) > 0)
                                            @foreach ($TransactionSummary as $ts)
                                                <tr>
                                                    <td>{{ $ts['currency'] }}</td>
                                                    <td>{{ $ts['declined_count'] }}</td>
                                                    <td>{{ number_format($ts['declined_amount'], 2, '.', ',') }}</td>
                                                    <td>{{ round($ts['declined_percentage'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center text-white" colspan="4">No record found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="CHARGEBACKS">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th width="50px">Currency</th>
                                            <th>Count</th>
                                            <th>Amount</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($TransactionSummary) > 0)
                                            @foreach ($TransactionSummary as $ts)
                                                <tr>
                                                    <td>{{ $ts['currency'] }}</td>
                                                    <td>{{ $ts['chargebacks_count'] }}</td>
                                                    <td>{{ number_format($ts['chargebacks_amount'], 2, '.', ',') }}
                                                    </td>
                                                    <td>{{ round($ts['chargebacks_percentage'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center text-white" colspan="4">No record found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="REFUND">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th width="50px">Currency</th>
                                            <th>Count</th>
                                            <th>Amount</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($TransactionSummary) > 0)
                                            @foreach ($TransactionSummary as $ts)
                                                <tr>
                                                    <td>{{ $ts['currency'] }}</td>
                                                    <td>{{ $ts['refund_count'] }}</td>
                                                    <td>{{ number_format($ts['refund_amount'], 2, '.', ',') }}</td>
                                                    <td>{{ round($ts['refund_percentage'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center text-white" colspan="4">No record found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="SUSPICIOUS">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th width="50px">Currency</th>
                                            <th>Count</th>
                                            <th>Amount</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($TransactionSummary) > 0)
                                            @foreach ($TransactionSummary as $ts)
                                                <tr>
                                                    <td>{{ $ts['currency'] }}</td>
                                                    <td>{{ $ts['flagged_count'] }}</td>
                                                    <td>{{ number_format($ts['flagged_amount'], 2, '.', ',') }}</td>
                                                    <td>{{ round($ts['flagged_percentage'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center text-white" colspan="4">No record found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="BLOCK">
                            <div class="table-responsive custom-table">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th width="50px">Currency</th>
                                            <th>Count</th>
                                            <th>Amount</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($TransactionSummary) > 0)
                                            @foreach ($TransactionSummary as $ts)
                                                <tr>
                                                    <td>{{ $ts['currency'] }}</td>
                                                    <td>{{ $ts['block_count'] }}</td>
                                                    <td>{{ number_format($ts['block_amount'], 2, '.', ',') }}</td>
                                                    <td>{{ round($ts['block_percentage'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center text-white" colspan="4">No record found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-12 mt-2 mb-2">
                            <a href="{{ route('transaction-summary-report') }}" class="btn btn-primary btn-sm">View
                                All</a>

                            <div class="pull-right">

                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                        data-bs-toggle="dropdown">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M13.7088 10.5417C13.7062 10.8377 13.7445 11.1325 13.8228 11.418L3.19384 11.418C2.96145 11.418 2.73857 11.3256 2.57424 11.1613C2.40991 10.997 2.31759 10.7741 2.31759 10.5417C2.31759 10.3093 2.40991 10.0864 2.57424 9.92211C2.73857 9.75778 2.96145 9.66547 3.19384 9.66547L13.8228 9.66547C13.7445 9.95089 13.7062 10.2458 13.7088 10.5417Z"
                                                fill="#B3ADAD" />
                                            <path
                                                d="M7.57505 16.6755C7.57241 16.9714 7.61075 17.2663 7.68896 17.5518L3.1938 17.5518C2.96141 17.5518 2.73853 17.4594 2.5742 17.2951C2.40987 17.1308 2.31755 16.9079 2.31755 16.6755C2.31755 16.4431 2.40987 16.2202 2.5742 16.0559C2.73853 15.8916 2.96141 15.7993 3.1938 15.7993L7.68896 15.7993C7.61075 16.0847 7.57241 16.3796 7.57505 16.6755Z"
                                                fill="#B3ADAD" />
                                            <path
                                                d="M18.9663 17.5518L14.4711 17.5518C14.623 16.9775 14.623 16.3735 14.4711 15.7993L18.9663 15.7993C19.1987 15.7993 19.4216 15.8916 19.5859 16.0559C19.7502 16.2202 19.8425 16.4431 19.8425 16.6755C19.8425 16.9079 19.7502 17.1308 19.5859 17.2951C19.4216 17.4594 19.1987 17.5518 18.9663 17.5518Z"
                                                fill="#B3ADAD" />
                                            <path
                                                d="M18.9663 5.28424L8.33737 5.28424C8.41559 4.99881 8.45392 4.70393 8.45128 4.40799C8.45392 4.11205 8.41559 3.81717 8.33737 3.53174L18.9663 3.53174C19.1987 3.53174 19.4216 3.62406 19.5859 3.78839C19.7502 3.95271 19.8425 4.17559 19.8425 4.40799C19.8425 4.64038 19.7502 4.86326 19.5859 5.02759C19.4216 5.19192 19.1987 5.28424 18.9663 5.28424Z"
                                                fill="#B3ADAD" />
                                            <path
                                                d="M4.94633 7.03673C4.46409 7.03808 3.99076 6.90675 3.57816 6.65711C3.16555 6.40747 2.82957 6.04914 2.60698 5.62134C2.38439 5.19354 2.28376 4.71275 2.31613 4.23159C2.34849 3.75043 2.51258 3.28744 2.79045 2.8933C3.06832 2.49915 3.44926 2.18904 3.89158 1.9969C4.3339 1.80476 4.82055 1.73799 5.29827 1.80391C5.77599 1.86983 6.22637 2.06589 6.60013 2.37063C6.97388 2.67537 7.25661 3.07706 7.41736 3.53173C7.62748 4.097 7.62748 4.71896 7.41736 5.28423C7.23657 5.79556 6.90206 6.23846 6.45967 6.55221C6.01728 6.86596 5.48868 7.0352 4.94633 7.03673Z"
                                                fill="#B3ADAD" />
                                            <path
                                                d="M17.2137 13.1705C16.6714 13.1689 16.1428 12.9997 15.7004 12.6859C15.258 12.3722 14.9235 11.9293 14.7427 11.418C14.5326 10.8527 14.5326 10.2307 14.7427 9.66545C14.9034 9.21079 15.1862 8.8091 15.5599 8.50436C15.9337 8.19961 16.3841 8.00355 16.8618 7.93764C17.3395 7.87172 17.8262 7.93849 18.2685 8.13063C18.7108 8.32277 19.0917 8.63288 19.3696 9.02702C19.6475 9.42117 19.8116 9.88416 19.8439 10.3653C19.8763 10.8465 19.7757 11.3273 19.5531 11.7551C19.3305 12.1829 18.9945 12.5412 18.5819 12.7908C18.1693 13.0405 17.696 13.1718 17.2137 13.1705Z"
                                                fill="#B3ADAD" />
                                            <path
                                                d="M11.0799 19.3042C10.5375 19.3027 10.0089 19.1334 9.56654 18.8197C9.12415 18.5059 8.78963 18.063 8.60885 17.5517C8.39873 16.9864 8.39873 16.3645 8.60885 15.7992C8.79304 15.2917 9.12902 14.8533 9.57112 14.5434C10.0132 14.2335 10.54 14.0673 11.0799 14.0673C11.6198 14.0673 12.1465 14.2335 12.5886 14.5434C13.0307 14.8533 13.3667 15.2917 13.5509 15.7992C13.761 16.3645 13.761 16.9864 13.5509 17.5517C13.3701 18.063 13.0356 18.5059 12.5932 18.8197C12.1508 19.1334 11.6222 19.3027 11.0799 19.3042Z"
                                                fill="#B3ADAD" />
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item transactionSummaryFilter  {{ $valuetSummary == '0' ? 'active' : '' }}"
                                            data-value="0" href="javascript:void(0);">Daily</a>
                                        <a class="dropdown-item transactionSummaryFilter {{ $valuetSummary == '6' ? 'active' : '' }}"
                                            data-value="7" href="javascript:void(0);">Weekly</a>
                                        <a class="dropdown-item transactionSummaryFilter {{ $valuetSummary == '30' ? 'active' : '' }}"
                                            data-value="30" href="javascript:void(0);">Monthly</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#SUCCESSFUL" data-bs-toggle="tab">
                                Successful
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#DECLINED" data-bs-toggle="tab">
                                Declined
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#CHARGEBACKS" data-bs-toggle="tab">
                                Chargebacks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#REFUND" data-bs-toggle="tab">
                                Refund
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#SUSPICIOUS" data-bs-toggle="tab">
                                Marked Transactions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#BLOCK" data-bs-toggle="tab">
                                Block
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
