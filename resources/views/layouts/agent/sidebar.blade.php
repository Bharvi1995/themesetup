<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow expanded" data-scroll-to-active="true">
    <div class="navbar-header expanded">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="#">
                    <img src="{{ storage_asset('setup/images/logo_sm.png') }}" class="logo-sm">
                    <img src="{{ storage_asset('setup/images/Logo.png') }}" class="logo-big">
                </a>
            </li>
            <li class="nav-item nav-toggle mr-10">
                <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                    <svg width="16" height="5" viewBox="0 0 16 5" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M4 2.84354C4 3.94811 3.10457 4.84354 2 4.84354C0.895431 4.84354 0 3.94811 0 2.84354C0 1.73897 0.895431 0.843536 2 0.843536C3.10457 0.843536 4 1.73897 4 2.84354Z"
                            fill="#7D7D7D" />
                        <path
                            d="M10 2.84354C10 3.94811 9.10457 4.84354 8 4.84354C6.89543 4.84354 6 3.94811 6 2.84354C6 1.73897 6.89543 0.843536 8 0.843536C9.10457 0.843536 10 1.73897 10 2.84354Z"
                            fill="#7D7D7D" />
                        <path
                            d="M14 4.84354C15.1046 4.84354 16 3.94811 16 2.84354C16 1.73897 15.1046 0.843536 14 0.843536C12.8954 0.843536 12 1.73897 12 2.84354C12 3.94811 12.8954 4.84354 14 4.84354Z"
                            fill="#7D7D7D" />
                    </svg>
                </a>
            </li>
        </ul>
    </div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="{{ $pageActive == 'dashboard' ? 'active' : '' }} nav-item">
                    <a href="{{ route('rp.dashboard') }}" class="d-flex align-items-center">
                        <div class="svg-icon">
                            <svg width="20" height="20" viewBox="0 0 17 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.47615 0.539567C9.08563 0.149042 8.45246 0.149042 8.06194 0.539567L1.06194 7.53957C0.671412 7.93009 0.671412 8.56326 1.06194 8.95378C1.45246 9.34431 2.08563 9.34431 2.47615 8.95378L2.76904 8.66089V15.2467C2.76904 15.799 3.21676 16.2467 3.76904 16.2467H5.76904C6.32133 16.2467 6.76904 15.799 6.76904 15.2467V13.2467C6.76904 12.6944 7.21676 12.2467 7.76904 12.2467H9.76904C10.3213 12.2467 10.769 12.6944 10.769 13.2467V15.2467C10.769 15.799 11.2168 16.2467 11.769 16.2467H13.769C14.3213 16.2467 14.769 15.799 14.769 15.2467V8.66089L15.0619 8.95378C15.4525 9.34431 16.0856 9.34431 16.4761 8.95378C16.8667 8.56326 16.8667 7.93009 16.4761 7.53957L9.47615 0.539567Z"
                                    class="hover-ch" />
                            </svg>
                        </div>
                        <span class="menu-title text-truncate ps-1" data-i18n="Overview">Overview</span>

                    </a>
                </li>
               
                <li
                    class="{{ $pageActive == 'user-management' || $pageActive == 'user-management-application-show' || $pageActive == 'user-management-application-edit' || $pageActive == 'rp-merchant-payout-report' ? 'active' : '' }} nav-item">
                    <a href="{{ route('rp.user-management') }}" class="d-flex align-items-center">
                        <div class="svg-icon">
                            <svg width="18" height="15" viewBox="0 0 18 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8 3.24667C8 4.90353 6.65685 6.24667 5 6.24667C3.34315 6.24667 2 4.90353 2 3.24667C2 1.58982 3.34315 0.246674 5 0.246674C6.65685 0.246674 8 1.58982 8 3.24667Z"
                                    fill="#B3ADAD" class="hover-ch" />
                                <path
                                    d="M16 3.24667C16 4.90353 14.6569 6.24667 13 6.24667C11.3431 6.24667 10 4.90353 10 3.24667C10 1.58982 11.3431 0.246674 13 0.246674C14.6569 0.246674 16 1.58982 16 3.24667Z"
                                    fill="#B3ADAD" class="hover-ch" />
                                <path
                                    d="M11.9291 14.2467C11.9758 13.9201 12 13.5862 12 13.2467C12 11.6115 11.4393 10.1073 10.4998 8.91574C11.2352 8.49022 12.0892 8.24667 13 8.24667C15.7614 8.24667 18 10.4852 18 13.2467V14.2467H11.9291Z"
                                    fill="#B3ADAD" class="hover-ch" />
                                <path
                                    d="M5 8.24667C7.76142 8.24667 10 10.4852 10 13.2467V14.2467H0V13.2467C0 10.4852 2.23858 8.24667 5 8.24667Z"
                                    fill="#B3ADAD" class="hover-ch" />
                            </svg>

                        </div>
                        <span class="menu-title text-truncate ps-1" data-i18n="MerchantMangement"> Merchants
                            Management</span>
                    </a>
                </li>
                

                <li class="{{ $pageActive == 'merchant-transactions' ? 'active' : '' }} nav-item">
                    <a class="d-flex align-items-center" href="{{ route('rp-merchant-transactions') }}">
                        <div class="svg-icon">
                            <svg fill="#9B786F" width="20" height="20" viewBox="-1 0 19 19" xmlns="http://www.w3.org/2000/svg" class="cf-icon-svg"><path d="M16.417 9.583A7.917 7.917 0 1 1 8.5 1.666a7.917 7.917 0 0 1 7.917 7.917zm-2.307 2.53V7.069a.318.318 0 0 0-.317-.316H3.217a.318.318 0 0 0-.317.316v5.044a.318.318 0 0 0 .317.317h10.576a.318.318 0 0 0 .317-.317zm-3.172-2.522a2.357 2.357 0 1 1-.185-.92 2.351 2.351 0 0 1 .185.92zm-1.691 1.257a.744.744 0 0 0 .372-.638.754.754 0 0 0-.187-.496 1.03 1.03 0 0 0-.284-.226 1.203 1.203 0 0 0-.297-.107 1.29 1.29 0 0 0-.272-.029 1.061 1.061 0 0 1-.176-.013.689.689 0 0 1-.187-.06.45.45 0 0 1-.147-.118.279.279 0 0 1 .098-.443.69.69 0 0 1 .564-.087l.017.003a.92.92 0 0 1 .176.062.508.508 0 0 1 .148.101.237.237 0 1 0 .336-.336.982.982 0 0 0-.289-.198 1.373 1.373 0 0 0-.27-.093l-.017-.003-.02-.005V7.85a.237.237 0 0 0-.474 0v.306a1.228 1.228 0 0 0-.424.162.783.783 0 0 0-.39.66.77.77 0 0 0 .177.483.918.918 0 0 0 .302.243 1.158 1.158 0 0 0 .322.104 1.533 1.533 0 0 0 .254.02.825.825 0 0 1 .171.018.722.722 0 0 1 .177.063.555.555 0 0 1 .148.119.283.283 0 0 1 .069.183.271.271 0 0 1-.156.24.823.823 0 0 1-.424.117 1.257 1.257 0 0 1-.183-.022.888.888 0 0 1-.172-.054.38.38 0 0 1-.142-.11.237.237 0 1 0-.36.312.845.845 0 0 0 .326.239 1.309 1.309 0 0 0 .266.081l.038.007v.313a.237.237 0 0 0 .475 0v-.316a1.252 1.252 0 0 0 .434-.17z" class="hover-ch"/></svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="Overview">All Payments</span>
                    </a>
                </li>
                <!-- Reports Side menu -->
                <li
                    class="nav-item has-sub {{ $pageActive == 'payout-report' || $pageActive == 'rp-merchant-transaction-report' || $pageActive == 'rp-commision-report' || $pageActive == 'risk-report' ? 'active' : '' }}">
                    <a href="#" class="d-flex align-items-center">
                        <div class="svg-icon">
                            <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0 8.24667C0 3.8284 3.58172 0.246674 8 0.246674V8.24667H16C16 12.665 12.4183 16.2467 8 16.2467C3.58172 16.2467 0 12.665 0 8.24667Z"
                                    fill="#B3ADAD" class="hover-ch" />
                                <path d="M10 0.498627C12.8113 1.22219 15.0245 3.43544 15.748 6.24672H10V0.498627Z"
                                    fill="#B3ADAD" class="hover-ch" />
                            </svg>
                        </div>
                        <span class="menu-title text-truncate ps-1" data-i18n="reports">Reports</span>
                    </a>
                    <ul class="menu-content">
                        <li class="{{ $pageActive == 'rp-merchant-transaction-report' ? 'active' : '' }}"><a
                                class="d-flex align-items-center"
                                href="{{ route('rp.merchant-transaction-report') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate ps-1">Transaction Summary</span>
                            </a></li>
                        <li class="{{ $pageActive == 'rp-commision-report' ? 'active' : '' }}"><a
                                class="d-flex align-items-center" href="{{ route('rp.commision-report') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate ps-1">
                                    Commision Report</span>
                            </a></li>
                        <li class="{{ $pageActive == 'payout-report' ? 'active' : '' }}"><a
                                class="d-flex align-items-center" href="{{ route('rp.merchant.payout.report') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate ps-1">
                                    Payout Reports</span>
                            </a></li>
                    </ul>
                </li>
            
        </ul>
    </div>
</div>
