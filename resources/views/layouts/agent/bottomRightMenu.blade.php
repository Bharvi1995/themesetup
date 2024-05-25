<div class="bottomRight">
    <ul>
        <li>
            <a href="{{ route('rp-summary-report') }}">
                <i class="fa fa-bar-chart" aria-hidden="true"></i>
                <div class="slider">
                    Summary Reports
                </div>
            </a>
        </li>
    
        <li>
            <a href="{{ route('rp.merchant.payout.report') }}">
                <i class="fa flaticon-381-calculator-1" aria-hidden="true"></i>
                <div class="slider">
                    Payout Reports
                </div>
            </a>
        </li>
        @if(Auth::guard('agentUser')->user()->main_agent_id == 0)
        <li>
            <a href="{{ route('agent.bank.details') }}">
                <i class="fa fa-bank" aria-hidden="true"></i>
                <div class="slider">
                    Bank Details
                </div>
            </a>
        </li>
        @endif
    </ul>
</div>