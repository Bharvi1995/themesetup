<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="#">
                    <img src="{{ storage_asset('NewTheme/images/logo_sm.png') }}" class="logo-sm">
                    <img src="{{ storage_asset('NewTheme/images/Logo.png') }}" class="logo-big">
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

            @if(bankApplicationStatus(auth()->guard('bankUser')->user()->id) == '1')
                <li class="{{ $pageActive == 'dashboard' ? 'active' : '' }} nav-item">
                    <a class="d-flex align-items-center" href="{{ route('bank.dashboard') }}">
                        <div class="svg-icon">
                            <svg width="20" height="20" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.47615 0.539567C9.08563 0.149042 8.45246 0.149042 8.06194 0.539567L1.06194 7.53957C0.671412 7.93009 0.671412 8.56326 1.06194 8.95378C1.45246 9.34431 2.08563 9.34431 2.47615 8.95378L2.76904 8.66089V15.2467C2.76904 15.799 3.21676 16.2467 3.76904 16.2467H5.76904C6.32133 16.2467 6.76904 15.799 6.76904 15.2467V13.2467C6.76904 12.6944 7.21676 12.2467 7.76904 12.2467H9.76904C10.3213 12.2467 10.769 12.6944 10.769 13.2467V15.2467C10.769 15.799 11.2168 16.2467 11.769 16.2467H13.769C14.3213 16.2467 14.769 15.799 14.769 15.2467V8.66089L15.0619 8.95378C15.4525 9.34431 16.0856 9.34431 16.4761 8.95378C16.8667 8.56326 16.8667 7.93009 16.4761 7.53957L9.47615 0.539567Z" class="hover-ch"/>
                            </svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="Overview">Overview</span>
                    </a>
                </li>

                <li class="nav-item has-sub {{ ($pageActive1 == 'bank.applications' || $pageActive1 == 'bank.applications.approved' || $pageActive1 == 'bank.applications.pending' || $pageActive1 == 'bank.applications.declined'|| $pageActive1 == 'bank.applications.referred') ? 'sidebar-group-active open' : ''  }}">
                    <a href="#" class="d-flex align-items-center">
                        <div class="svg-icon">
                            <svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.6579 4.55245V7.88578C17.0188 8.85823 13.7415 9.41378 10.2135 9.41378C6.68549 9.41378 3.40815 8.85823 0.769043 7.88578V4.55245C0.769043 3.9409 1.26949 3.44134 1.88015 3.44134H6.46326V2.60756C6.46326 1.32934 7.51927 0.246674 8.82415 0.246674H11.6019C12.9077 0.246674 13.9628 1.33023 13.9628 2.60756V3.44134H18.5459C19.1575 3.44134 19.6579 3.94178 19.6579 4.55245ZM7.8526 2.60756V3.44134H12.5753V2.60756C12.5753 2.07956 12.1308 1.63512 11.6028 1.63512H8.82504C8.29704 1.63512 7.8526 2.07956 7.8526 2.60756ZM8.82504 7.69112V8.24667C8.82504 8.41378 8.93615 8.5249 9.10327 8.5249H11.3255C11.4926 8.5249 11.6037 8.41378 11.6037 8.24667V7.69112C11.6037 7.52401 11.4926 7.4129 11.3255 7.4129H9.10327C8.93615 7.4129 8.82504 7.52401 8.82504 7.69112ZM0.769043 15.1356V9.07956C3.43571 9.99601 6.68549 10.524 10.2135 10.524C13.7415 10.524 16.9913 9.99601 19.6579 9.07956V15.1356C19.6579 15.7471 19.1575 16.2467 18.5468 16.2467H1.88015C1.2686 16.2467 0.769043 15.7462 0.769043 15.1356ZM11.6028 12.2742V11.7187C11.6028 11.5516 11.4917 11.4405 11.3246 11.4405H9.10238C8.93527 11.4405 8.82415 11.5516 8.82415 11.7187V12.2742C8.82415 12.4413 8.93527 12.5525 9.10238 12.5525H11.3246C11.4917 12.5525 11.6028 12.4413 11.6028 12.2742Z" class="hover-ch"/>
                            </svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="Application">Application</span>
                    </a>
                    <ul class="menu-content">
                        <li class="{{ $pageActive1 == 'bank.applications' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{!! route('bank.applications') !!}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-item text-truncate" data-i18n="All Applications">All Applications</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive1 == 'bank.applications.approved' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{route('bank.applications.approved')}}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-item text-truncate" data-i18n="Approved Applications">Approved Applications</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive1 == 'bank.applications.pending' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{route('bank.applications.pending')}}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-item text-truncate" data-i18n="Pending Applications">Pending Applications</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive1 == 'bank.applications.declined' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{route('bank.applications.declined')}}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-item text-truncate" data-i18n="Pending Applications">Declined Applications</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive1 == 'bank.applications.referred' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{route('bank.applications.referred')}}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-item text-truncate" data-i18n="Referred Applications">Referred Applications</span>
                            </a>
                        </li>
                    </ul>
                </li>            
                
                <li class="nav-item has-sub {{ ($pageActive == 'merchant-transactions' || $pageActive == 'merchant-approved-transactions' || $pageActive == 'merchant-declined-transactions' || $pageActive == 'merchant-chargebacks-transactions' || $pageActive == 'merchant-refund-transactions') ? 'sidebar-group-active open' : ''  }}">
                    <a class="d-flex align-items-center" href="#">
                        <div class="svg-icon">
                            <svg width="20" height="24" viewBox="0 0 20 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.6202 3.18634L14.7628 1.32891L15.8878 0.203888L19.6658 3.98185L15.8878 7.75982L14.7628 6.63479L16.6202 4.77737H6.71202C5.10106 4.77737 3.79513 6.0833 3.79513 7.69425V9.30041H2.2041V7.69425C2.2041 5.2046 4.22236 3.18634 6.71202 3.18634H16.6202ZM13.858 18.5178C15.469 18.5178 16.7749 17.2119 16.7749 15.6009V13.9948H18.366V15.6009C18.366 18.0906 16.3477 20.1088 13.858 20.1088H3.94986L5.80728 21.9662L4.68225 23.0913L0.904297 19.3133L4.68226 15.5353L5.80729 16.6604L3.94986 18.5178H13.858ZM7.75 7.75C6.5163 7.75 5.625 8.82012 5.625 10V14C5.625 15.1799 6.5163 16.25 7.75 16.25H13.25C14.4837 16.25 15.375 15.1799 15.375 14V10C15.375 8.82012 14.4837 7.75 13.25 7.75H7.75ZM7.125 14V11.75H13.875V14C13.875 14.477 13.5351 14.75 13.25 14.75H7.75C7.46492 14.75 7.125 14.477 7.125 14ZM7.125 10.25H13.875V10C13.875 9.52302 13.5351 9.25 13.25 9.25H7.75C7.46492 9.25 7.125 9.52302 7.125 10V10.25ZM8.20833 12.75C7.79412 12.75 7.45833 13.0858 7.45833 13.5C7.45833 13.9142 7.79412 14.25 8.20833 14.25H8.66667C9.08088 14.25 9.41667 13.9142 9.41667 13.5C9.41667 13.0858 9.08088 12.75 8.66667 12.75H8.20833ZM10.5 12.75C10.0858 12.75 9.75 13.0858 9.75 13.5C9.75 13.9142 10.0858 14.25 10.5 14.25H10.9583C11.3725 14.25 11.7083 13.9142 11.7083 13.5C11.7083 13.0858 11.3725 12.75 10.9583 12.75H10.5Z" class="hover-ch"/>
                            </svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="Transactions">Transactions</span>
                    </a>
                    <ul class="menu-content">
                        <li class="{{ $pageActive == 'merchant-transactions' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('bank-merchant-transactions') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate" data-i18n="All Transactions">All Transactions</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive == 'merchant-approved-transactions' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('bank-merchant-approved-transactions') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate" data-i18n="Approved">Approved</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive == 'merchant-declined-transactions' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('bank-merchant-declined-transactions') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate" data-i18n="Declined">Declined</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive == 'merchant-chargebacks-transactions' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('bank-merchant-chargebacks-transactions') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate" data-i18n="Chargebacks">Chargebacks</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive == 'merchant-refund-transactions' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('bank-merchant-refund-transactions') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate" data-i18n="Refund">Refund</span>
                            </a>
                        </li>                    
                    </ul>
                </li>

                <li class="nav-item has-sub {{ ($pageActive == 'risk-report') || ($pageActive == 'merchant-volume') ? 'sidebar-group-active open' : ''  }}">
                    <a class="d-flex align-items-center" href="#">
                        <div class="svg-icon">
                            <svg width="20" height="21" viewBox="0 0 17 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.76904 10.2467H11.769M5.76904 14.2467H11.769M13.769 19.2467H3.76904C2.66447 19.2467 1.76904 18.3512 1.76904 17.2467V3.24667C1.76904 2.1421 2.66447 1.24667 3.76904 1.24667H9.35483C9.62005 1.24667 9.8744 1.35203 10.0619 1.53957L15.4762 6.95378C15.6637 7.14132 15.769 7.39567 15.769 7.66089V17.2467C15.769 18.3512 14.8736 19.2467 13.769 19.2467Z" stroke="#B3ADAD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hover-ch-s"/>
                            </svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="Reports">Reports</span>
                    </a>
                    <ul class="menu-content">
                        <li class="{{ $pageActive == 'merchant-volume' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('bank-merchant-volume-report') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate" data-i18n="Merchant Volume">Merchant Volume</span>
                            </a>
                        </li>                        
                        
                        <li class="{{ $pageActive == 'risk-report' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('bank.risk-report') }}">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.378831 10.5282C-0.0116932 10.1376 -0.0116932 9.50446 0.378831 9.11394L3.67172 5.82104L0.378831 2.52815C-0.0116936 2.13763 -0.0116936 1.50446 0.378831 1.11394C0.769355 0.723414 1.40252 0.723414 1.79304 1.11394L5.79304 5.11394C6.18357 5.50446 6.18357 6.13763 5.79304 6.52815L1.79304 10.5282C1.40252 10.9187 0.769355 10.9187 0.378831 10.5282Z"
                                        fill="#B3ADAD" />
                                </svg>
                                <span class="menu-title text-truncate" data-i18n="Approved">Risk Report</span>
                            </a>
                        </li>                                            
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>
<!-- END: Main Menu-->