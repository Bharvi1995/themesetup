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
            <!-- <li class="nav-item nav-toggle mr-10">
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
            </li> -->
        </ul>
    </div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="{{ $pageActive == 'dashboard' ? 'active' : '' }} nav-item">
                <a href="{!! url('paylaksa/dashboard') !!}" class="d-flex align-items-center">
                    <div class="svg-icon">
                        <svg width="20" height="20" viewBox="0 0 17 17" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.47615 0.539567C9.08563 0.149042 8.45246 0.149042 8.06194 0.539567L1.06194 7.53957C0.671412 7.93009 0.671412 8.56326 1.06194 8.95378C1.45246 9.34431 2.08563 9.34431 2.47615 8.95378L2.76904 8.66089V15.2467C2.76904 15.799 3.21676 16.2467 3.76904 16.2467H5.76904C6.32133 16.2467 6.76904 15.799 6.76904 15.2467V13.2467C6.76904 12.6944 7.21676 12.2467 7.76904 12.2467H9.76904C10.3213 12.2467 10.769 12.6944 10.769 13.2467V15.2467C10.769 15.799 11.2168 16.2467 11.769 16.2467H13.769C14.3213 16.2467 14.769 15.799 14.769 15.2467V8.66089L15.0619 8.95378C15.4525 9.34431 16.0856 9.34431 16.4761 8.95378C16.8667 8.56326 16.8667 7.93009 16.4761 7.53957L9.47615 0.539567Z"
                                class="hover-ch" />
                        </svg>
                    </div>
                    <span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span>
                </a>
            </li>

            @if (auth()->guard('admin')->user()->can(['role-list']) ||
                    auth()->guard('admin')->user()->can(['users-admin-list']) ||
                    auth()->guard('admin')->user()->can(['users-agents-list']) ||
                    auth()->guard('admin')->user()->can(['users-bank-list']) ||
                    auth()->guard('admin')->user()->can(['view-merchant-stores']) ||
                    auth()->guard('admin')->user()->can(['view-merchant']))

                <li
                    class="{{ $pageActive == 'roles' ||
                    $pageActive == 'admin-user' ||
                    $pageActive == 'banks' ||
                    $pageActive == 'agents' ||
                    $pageActive == 'wl-agents' ||
                    $pageActive == 'users-management' ||
                    $pageActive == 'wl-agent-merchant' ||
                    $pageActive == 'merchant-stores' ||
                    $pageActive == 'merchant-user-edit'
                        ? 'sidebar-group-active open'
                        : '' }} nav-item has-sub">
                    <a class="d-flex align-items-center" href="#">
                        <div class="svg-icon">
                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.96289 6.24667C9.96289 7.90353 8.61974 9.24667 6.96289 9.24667C5.30604 9.24667 3.96289 7.90353 3.96289 6.24667C3.96289 4.58982 5.30604 3.24667 6.96289 3.24667C8.61974 3.24667 9.96289 4.58982 9.96289 6.24667Z"
                                    class="hover-ch" />
                                <path
                                    d="M17.9629 6.24667C17.9629 7.90353 16.6197 9.24667 14.9629 9.24667C13.306 9.24667 11.9629 7.90353 11.9629 6.24667C11.9629 4.58982 13.306 3.24667 14.9629 3.24667C16.6197 3.24667 17.9629 4.58982 17.9629 6.24667Z"
                                    class="hover-ch" />
                                <path
                                    d="M13.892 17.2467C13.9387 16.9201 13.9629 16.5862 13.9629 16.2467C13.9629 14.6115 13.4022 13.1073 12.4626 11.9157C13.1981 11.4902 14.0521 11.2467 14.9629 11.2467C17.7243 11.2467 19.9629 13.4852 19.9629 16.2467V17.2467H13.892Z"
                                    class="hover-ch" />
                                <path
                                    d="M6.96289 11.2467C9.72431 11.2467 11.9629 13.4852 11.9629 16.2467V17.2467H1.96289V16.2467C1.96289 13.4852 4.20147 11.2467 6.96289 11.2467Z"
                                    class="hover-ch" />
                            </svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="Users Management">Users Management</span>
                    </a>
                    <ul class="menu-content">
                        @if (auth()->guard('admin')->user()->can(['role-list']))
                            <li class="{{ $pageActive == 'roles' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ route('roles.index') }}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Admin Roles">Admin Roles</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['users-admin-list']))
                            <li class="{{ $pageActive == 'admin-user' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{!! url('paylaksa/admin-user') !!}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Admin User List">Admin User
                                        List</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['users-agents-list']))
                            <li class="{{ $pageActive == 'agents' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{!! url('paylaksa/agents') !!}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Referral Partners List">Referral
                                        Partners List</span>
                                </a>
                            </li>
                        @endif
                        
                        @if (auth()->guard('admin')->user()->can(['view-merchant']))
                            <li class="{{ $pageActive == 'users-management' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{!! url('paylaksa/users-management') !!}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Merchant List">Merchant
                                        List</span>
                                </a>
                            </li>
                        @endif
                       
                    </ul>
                </li>
            @endif

            @if (auth()->guard('admin')->user()->can(['list-gateway']) ||
                    auth()->guard('admin')->user()->can(['list-mid']) ||
                    auth()->guard('admin')->user()->can(['list-rule']) ||
                    auth()->guard('admin')->user()->can(['merchant-rule-list']))
                <li
                    class="{{ $pageActive == 'gateway' ||
                    $pageActive == 'mid-feature-management' ||
                    $pageActive == 'create_rules' ||
                    $pageActive == 'merchant_rules'
                        ? 'sidebar-group-active open'
                        : '' }} nav-item has-sub">
                    <a class="d-flex align-items-center" href="#">
                        <div class="svg-icon">
                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.4668 1.37843C10.7742 1.20275 11.1516 1.20275 11.459 1.37843L13.209 2.37843C13.6885 2.65244 13.8551 3.2633 13.5811 3.74281C13.3071 4.22233 12.6963 4.38893 12.2168 4.11492L10.9629 3.39842L9.70903 4.11492C9.22951 4.38893 8.61866 4.22233 8.34465 3.74281C8.07064 3.2633 8.23723 2.65244 8.71675 2.37843L10.4668 1.37843Z"
                                    class="hover-ch" />
                                <path
                                    d="M6.58113 4.75053C6.85514 5.23005 6.68855 5.84091 6.20903 6.11492L5.97846 6.24667L6.20903 6.37843C6.68855 6.65244 6.85514 7.2633 6.58113 7.74281C6.30712 8.22233 5.69627 8.38893 5.21675 8.11492L4.96289 7.96985V8.24667C4.96289 8.79896 4.51518 9.24667 3.96289 9.24667C3.41061 9.24667 2.96289 8.79896 2.96289 8.24667V6.24667C2.96289 5.99669 3.05462 5.76813 3.20626 5.59281C3.24091 5.5527 3.27887 5.51521 3.31988 5.48079C3.37043 5.4383 3.42525 5.40073 3.4836 5.3688L5.21675 4.37843C5.69627 4.10442 6.30712 4.27102 6.58113 4.75053Z"
                                    class="hover-ch" />
                                <path
                                    d="M15.3446 4.75053C15.6187 4.27102 16.2295 4.10442 16.709 4.37843L18.4422 5.36879C18.5005 5.40072 18.5553 5.4383 18.6059 5.48079C18.8242 5.66423 18.9629 5.93925 18.9629 6.24667V8.24667C18.9629 8.79896 18.5152 9.24667 17.9629 9.24667C17.4106 9.24667 16.9629 8.79896 16.9629 8.24667V7.96985L16.709 8.11492C16.2295 8.38893 15.6187 8.22233 15.3446 7.74281C15.0706 7.2633 15.2372 6.65244 15.7168 6.37843L15.9473 6.24667L15.7168 6.11492C15.2372 5.84091 15.0706 5.23005 15.3446 4.75053Z"
                                    class="hover-ch" />
                                <path
                                    d="M8.34465 8.75053C8.61866 8.27102 9.22951 8.10442 9.70903 8.37843L10.9629 9.09492L12.2168 8.37843C12.6963 8.10442 13.3071 8.27102 13.5811 8.75053C13.8551 9.23005 13.6885 9.84091 13.209 10.1149L11.9629 10.827V12.2467C11.9629 12.799 11.5152 13.2467 10.9629 13.2467C10.4106 13.2467 9.96289 12.799 9.96289 12.2467V10.827L8.71675 10.1149C8.23723 9.84091 8.07064 9.23005 8.34465 8.75053Z"
                                    class="hover-ch" />
                                <path
                                    d="M3.96289 11.2467C4.51518 11.2467 4.96289 11.6944 4.96289 12.2467V13.6664L6.20903 14.3784C6.68855 14.6524 6.85514 15.2633 6.58113 15.7428C6.30712 16.2223 5.69627 16.3889 5.21675 16.1149L3.46675 15.1149C3.15518 14.9369 2.96289 14.6055 2.96289 14.2467V12.2467C2.96289 11.6944 3.41061 11.2467 3.96289 11.2467Z"
                                    class="hover-ch" />
                                <path
                                    d="M17.9629 11.2467C18.5152 11.2467 18.9629 11.6944 18.9629 12.2467V14.2467C18.9629 14.6055 18.7706 14.9369 18.459 15.1149L16.709 16.1149C16.2295 16.3889 15.6187 16.2223 15.3446 15.7428C15.0706 15.2633 15.2372 14.6524 15.7168 14.3784L16.9629 13.6664V12.2467C16.9629 11.6944 17.4106 11.2467 17.9629 11.2467Z"
                                    class="hover-ch" />
                                <path
                                    d="M8.34465 16.7505C8.61866 16.271 9.22951 16.1044 9.70903 16.3784L9.96289 16.5235V16.2467C9.96289 15.6944 10.4106 15.2467 10.9629 15.2467C11.5152 15.2467 11.9629 15.6944 11.9629 16.2467V16.5235L12.2168 16.3784C12.6963 16.1044 13.3071 16.271 13.5811 16.7505C13.8551 17.2301 13.6885 17.8409 13.209 18.1149L11.4742 19.1062C11.3246 19.1954 11.1497 19.2467 10.9629 19.2467C10.776 19.2467 10.6012 19.1954 10.4516 19.1062L8.71675 18.1149C8.23723 17.8409 8.07064 17.2301 8.34465 16.7505Z"
                                    class="hover-ch" />
                            </svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="MID">MID</span>
                    </a>
                    <ul class="menu-content">
                        @if (auth()->guard('admin')->user()->can(['list-gateway']))
                            <li class="{{ $pageActive == 'gateway' ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ route('admin.gateway.index') }}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Gateway Management">Gateway
                                        Management</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['list-mid']))
                            <li class="{{ $pageActive == 'mid-feature-management' ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{!! url('paylaksa/mid-feature-management') !!}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="MIDs List">MIDs List</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['list-rule']))
                            <li class="{{ $pageActive == 'create_rules' ? 'active' : '' }}"><a
                                    class="d-flex align-items-center" href="{{ route('admin.create_rules.index') }}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Create Rules">Create
                                        Rules</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['merchant-rule-list']))
                            <li class="{{ $pageActive == 'merchant_rules' ? 'active' : '' }}"><a
                                    class="d-flex align-items-center"
                                    href="{{ route('admin.merchant_rules.index') }}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Merchant Rules">Merchant
                                        Rules</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <li
                class="nav-item has-sub {{ $pageActive1 == 'admin.applications.list' ||
                $pageActive1 == 'admin.applications.is_completed' ||
                $pageActive1 == 'admin.applications.is_approved' ||
                $pageActive1 == 'admin.applications.is_rejected' ||
                $pageActive1 == 'admin.applications.not_interested' ||
                $pageActive1 == 'admin.applications.is_terminated' ||
                $pageActive1 == 'admin.applications.sent_to_bank' ||
                $pageActive1 == 'admin.applications.is_deleted' ||
                $pageActive1 == 'admin.applications.rate_accepted' ||
                $pageActive1 == 'admin.applications.agreement_send' ||
                $pageActive1 == 'admin.applications.rate_decline' ||
                $pageActive1 == 'admin.applications.agreement_signed' ||
                $pageActive1 == 'admin.applications.agreement_received' ||
                $pageActive1 == 'application-bank.all' ||
                $pageActive1 == 'application-bank.pending' ||
                $pageActive1 == 'application-bank.reassign' ||
                $pageActive1 == 'application-bank.approved' ||
                $pageActive1 == 'application-rp.all' ||
                $pageActive1 == 'application-rp.pending' ||
                $pageActive1 == 'application-rp.reassign' ||
                $pageActive1 == 'application-rp.approved' ||
                $pageActive1 == 'application-rp.detail' ||
                $pageActive1 == 'application-rp.edit'
                    ? 'sidebar-group-active open'
                    : '' }}">
                <a class="d-flex align-items-center" href="#">
                    <div class="svg-icon">
                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19.9629 21.2467V5.24667C19.9629 4.1421 19.0675 3.24667 17.9629 3.24667H7.96289C6.85832 3.24667 5.96289 4.1421 5.96289 5.24667V21.2467M19.9629 21.2467L21.9629 21.2466M19.9629 21.2467H14.9629M5.96289 21.2467L3.96289 21.2466M5.96289 21.2467H10.9629M9.96289 7.24665H10.9629M9.96289 11.2467H10.9629M14.9629 7.24665H15.9629M14.9629 11.2467H15.9629M10.9629 21.2467V16.2467C10.9629 15.6944 11.4106 15.2467 11.9629 15.2467H13.9629C14.5152 15.2467 14.9629 15.6944 14.9629 16.2467V21.2467M10.9629 21.2467H14.9629"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="hover-ch-s" />
                        </svg>
                    </div>
                    <span class="menu-title text-truncate" data-i18n="Applications">Applications</span>
                </a>
                <ul class="menu-content">
                    @if (auth()->guard('admin')->user()->can(['list-application']))
                        <li>
                            <a class="d-flex align-items-center" href="#">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="Merchant Application">Merchant
                                    Application</span>
                            </a>
                            <ul class="menu-content">
                                <li class="{{ $pageActive1 == 'admin.applications.list' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.list') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="All Applications">All
                                            Applications</span>
                                    </a>
                                </li>
                                <li class="{{ $pageActive1 == 'admin.applications.is_completed' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.is_completed') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="In Progress">In
                                            Progress</span>
                                    </a>
                                </li>
                                <li class="{{ $pageActive1 == 'admin.applications.is_approved' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.is_approved') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="Pre Approval">Pre
                                            Approval</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.rate_accepted') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="Rate Accepted">Approval</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.agreement_send') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate"
                                            data-i18n="Application-Sent Agreements">Application-Sent
                                            Agreements</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.agreement_signed') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate"
                                            data-i18n="Application-Signed Agreements">Application-Signed
                                            Agreements</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.agreement_received') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate"
                                            data-i18n="Application-Received Agreements">Application-Received
                                            Agreements</span>
                                    </a>
                                </li>
                                <!-- <li>
                                    <a class="d-flex align-items-center" href="#">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="Rate & Agreements">Rate &  Agreements</span>
                                    </a>
                                    <ul class="menu-content">
                                        <li>
                                            <a class="d-flex align-items-center"
                                                href="{{ route('admin.applications.rate_accepted') }}">
                                                <i class="fa fa-circle"></i>
                                                <span class="menu-title text-truncate" data-i18n="Rate Accepted">Rate
                                                    Accepted</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="d-flex align-items-center"
                                                href="{{ route('admin.applications.rate_decline') }}">
                                                <i class="fa fa-circle"></i>
                                                <span class="menu-title text-truncate" data-i18n="Rate Decline">Rate
                                                    Decline</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="d-flex align-items-center"
                                                href="{{ route('admin.applications.agreement_send') }}">
                                                <i class="fa fa-circle"></i>
                                                <span class="menu-title text-truncate"
                                                    data-i18n="Application-Sent Agreements">Application-Sent
                                                    Agreements</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="d-flex align-items-center"
                                                href="{{ route('admin.applications.agreement_signed') }}">
                                                <i class="fa fa-circle"></i>
                                                <span class="menu-title text-truncate"
                                                    data-i18n="Application-Signed Agreements">Application-Signed
                                                    Agreements</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="d-flex align-items-center"
                                                href="{{ route('admin.applications.agreement_received') }}">
                                                <i class="fa fa-circle"></i>
                                                <span class="menu-title text-truncate"
                                                    data-i18n="Application-Received Agreements">Application-Received
                                                    Agreements</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li> -->
                                <li class="{{ $pageActive1 == 'admin.applications.is_rejected' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.is_rejected') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="Rejected">Rejected</span>
                                    </a>
                                </li>
                                <li class="{{ $pageActive1 == 'admin.applications.not_interested' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.not_interested') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="Not Interested">Not
                                            Interested</span>
                                    </a>
                                </li>
                                <li class="{{ $pageActive1 == 'admin.applications.is_terminated' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.is_terminated') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate"
                                            data-i18n="Terminated">Terminated</span>
                                    </a>
                                </li>
                                <li class="{{ $pageActive1 == 'admin.applications.is_deleted' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('admin.applications.is_deleted') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="Deleted">Deleted</span>
                                    </a>
                                </li>                                
                            </ul>
                        </li>
                    @endif
                    @if (auth()->guard('admin')->user()->can(['view-rp-application']))
                        <li>
                            <a class="d-flex align-items-center" href="#">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="RP Application">RP
                                    Application</span>
                            </a>
                            <ul class="menu-content">
                                <li class="{{ $pageActive1 == 'application-rp.all' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center" href="{{ route('application-rp.all') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="All Applications">All
                                            Applications</span>
                                    </a>
                                </li>
                                <li class="{{ $pageActive1 == 'application-rp.pending' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('application-rp.pending') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="Pending">Pending</span>
                                    </a>
                                </li>
                                <li class="{{ $pageActive1 == 'application-rp.reassign' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('application-rp.reassign') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate"
                                            data-i18n="ReAssigned">ReAssigned</span>
                                    </a>
                                </li>
                                <li class="{{ $pageActive1 == 'application-rp.approved' ? 'active' : '' }}">
                                    <a class="d-flex align-items-center"
                                        href="{{ route('application-rp.approved') }}">
                                        <i class="fa fa-circle"></i>
                                        <span class="menu-title text-truncate" data-i18n="Approved">Approved</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </li>

            @if (auth()->guard('admin')->user()->can(['list-all-transaction']))
                <li
                    class="nav-item has-sub {{ $pageActive == 'transactions' ||
                    $pageActive == 'crypto' ||
                    $pageActive == 'refund' ||
                    $pageActive == 'chargebacks' ||
                    $pageActive == 'suspicious' ||
                    $pageActive == 'declined-transactions' ||
                    $pageActive == 'test-transactions'
                        ? 'sidebar-group-active open'
                        : '' }}">
                    <a class="d-flex align-items-center" href="#">
                        <div class="svg-icon">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M3.96289 10.2467H21.9629M7.96289 15.2467H8.96289M12.9629 15.2467H13.9629M6.96289 19.2467H18.9629C20.6197 19.2467 21.9629 17.9035 21.9629 16.2467V8.24667C21.9629 6.58982 20.6197 5.24667 18.9629 5.24667H6.96289C5.30604 5.24667 3.96289 6.58982 3.96289 8.24667V16.2467C3.96289 17.9035 5.30604 19.2467 6.96289 19.2467Z"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="hover-ch-s" />
                            </svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="Transactions">Transactions</span>
                    </a>
                    <ul class="menu-content">
                        <li class="{{ $pageActive == 'transactions' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{!! url('paylaksa/transactions') !!}">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="All transactions">All
                                    transactions</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive == 'refund' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{!! url('paylaksa/refund') !!}">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="Refunds">Refunds</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive == 'chargebacks' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{!! url('paylaksa/chargebacks') !!}">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="Chargebacks">Chargebacks</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive == 'suspicious' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.suspicious') }}">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="Suspicious">Dispute</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive == 'declined-transactions' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.declined.transactions') }}">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="Declined">Declined</span>
                            </a>
                        </li>
                        <li class="{{ $pageActive == 'test-transactions' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{!! url('paylaksa/test-transactions') !!}">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="Test transactions">Test
                                    transactions</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->guard('admin')->user()->can(['list-transaction-summary-report']) ||
                    auth()->guard('admin')->user()->can(['list-merchant-transaction-report']) ||
                    auth()->guard('admin')->user()->can(['list-card-type-report']) ||
                    auth()->guard('admin')->user()->can(['list-payment-status-summary-report']) ||
                    auth()->guard('admin')->user()->can(['list-mid-summary-report']) ||
                    auth()->guard('admin')->user()->can(['list-country-summary-report']))
                <li
                    class="nav-item has-sub {{ $pageActive == 'transaction-summary-report' ||
                    $pageActive == 'merchant-transaction-report' ||
                    $pageActive == 'card-summary-report' ||
                    $pageActive == 'mid-summary-report' ||
                    $pageActive == 'summary-report-on-country' ||
                    $pageActive == 'auto-suspicious-report' ||
                    $pageActive == 'merchant-reports'
                        ? 'sidebar-group-active open'
                        : '' }}">
                    <a class="d-flex align-items-center" href="#">
                        <div class="svg-icon">
                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M2.96289 10.2467C2.96289 5.8284 6.54461 2.24667 10.9629 2.24667V10.2467H18.9629C18.9629 14.665 15.3812 18.2467 10.9629 18.2467C6.54461 18.2467 2.96289 14.665 2.96289 10.2467Z"
                                    class="hover-ch" />
                                <path
                                    d="M12.9629 2.49863C15.7741 3.22219 17.9874 5.43544 18.7109 8.24672H12.9629V2.49863Z"
                                    class="hover-ch" />
                            </svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="Reports">Reports</span>
                    </a>
                    <ul class="menu-content">
                        @if (auth()->guard('admin')->user()->can(['list-transaction-summary-report']))
                            <li class="{{ $pageActive == 'transaction-summary-report' ? 'active' : '' }}">
                                <a class="d-flex align-items-center"
                                    href="{{ route('transaction-summary-report') }}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Transaction summary">Transaction
                                        summary</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['list-merchant-transaction-report']))
                            <li class="{{ $pageActive == 'merchant-transaction-report' ? 'active' : '' }}">
                                <a class="d-flex align-items-center"
                                    href="{{ route('merchant-transaction-report') }}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Merchant Transaction">Merchant
                                        Transaction</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['list-transaction-summary-report']))
                            <li class="{{ $pageActive == 'card-summary-report' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{!! url('paylaksa/card-summary-report') !!}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="Card type Summary">Card type
                                        Summary</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['list-mid-summary-report']))
                            <li class="{{ $pageActive == 'mid-summary-report' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{!! url('paylaksa/mid-summary-report') !!}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate" data-i18n="MID type Summary Report">MID
                                        type Summary Report</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can(['list-country-summary-report']))
                            <li class="{{ $pageActive == 'summary-report-on-country' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{!! route('summary-report-on-country') !!}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate"
                                        data-i18n="Country wise Summary Report">Country wise Summary Report</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (auth()->guard('admin')->user()->can(['list-generated-payout-reports']) ||
                    auth()->guard('admin')->user()->can(['list-generated-rp-payout-reports']) ||
                    auth()->guard('admin')->user()->can(['list-generated-wl-rp-payout-reports']) ||
                    auth()->guard('admin')->user()->can(['list-rp-payout-reports']))
                <li
                    class="nav-item has-sub {{ $pageActive == 'generate-payout-report' ||
                    $pageActive == 'generate-payout-report-rp' ||
                    $pageActive == 'generate-agent-report' ||
                    $pageActive == 'agent-report' ||
                    $pageActive == 'generate-payout-report-new'
                        ? 'sidebar-group-active open'
                        : '' }}">
                    <a class="d-flex align-items-center" href="#">
                        <div class="svg-icon">
                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M2.96289 5.24664C2.96289 4.14207 3.85832 3.24664 4.96289 3.24664H12.9629C14.0675 3.24664 14.9629 4.14207 14.9629 5.24664V15.2466C14.9629 16.3512 15.8583 17.2466 16.9629 17.2466H4.96289C3.85832 17.2466 2.96289 16.3512 2.96289 15.2466V5.24664ZM5.96289 6.24664H11.9629V10.2466H5.96289V6.24664ZM11.9629 12.2466H5.96289V14.2466H11.9629V12.2466Z"
                                    class="hover-ch" />
                                <path
                                    d="M15.9629 7.24664H16.9629C18.0675 7.24664 18.9629 8.14207 18.9629 9.24664V14.7466C18.9629 15.5751 18.2913 16.2466 17.4629 16.2466C16.6345 16.2466 15.9629 15.5751 15.9629 14.7466V7.24664Z"
                                    class="hover-ch" />
                            </svg>
                        </div>
                        <span class="menu-title text-truncate" data-i18n="Payout">Payout</span>
                    </a>
                    <ul class="menu-content">
                        <li class="{{ $pageActive == 'generate-payout-report-new' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('generate-payout-report-new') }}">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="Auto Generate Payout Report">Auto
                                    Generate Payout Report</span>
                            </a>
                        </li>
                        @if (auth()->guard('admin')->user()->can(['list-generated-rp-payout-reports']))
                            <li class="{{ $pageActive == 'generate-agent-report' ? 'active' : '' }}">
                                <a class="d-flex align-items-center" href="{{ route('generate-agent-report') }}">
                                    <i class="fa fa-circle"></i>
                                    <span class="menu-title text-truncate"
                                        data-i18n="Generate Referral Partner's Report">Generate Referral Partner's
                                        Report</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <li
                class="nav-item has-sub {{ $pageActive == 'agreement-upload' ||
                $pageActive == 'ticket' ||
                $pageActive == 'technical' ||
                $pageActive == 'ip-whitelist' ||
                $pageActive == 'asp-iframe' ||
                $pageActive == 'transaction-session' ||
                $pageActive == 'required_fields' ||
                $pageActive == 'agreement_content' ||
                $pageActive == 'block-system' ||
                $pageActive == 'payout-schedule' ||
                $pageActive == 'categories' ||
                $pageActive == 'integration-preference' ||
                $pageActive == 'admin-logs' ||
                $pageActive == 'mail-templates' ||
                $pageActive == 'mass-mid'
                    ? 'sidebar-group-active open'
                    : '' }}">
                <a class="d-flex align-items-center" href="#">
                    <div class="svg-icon">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4.96289 4.24664C4.96289 3.14207 5.85832 2.24664 6.96289 2.24664H11.5487C12.0791 2.24664 12.5878 2.45736 12.9629 2.83243L16.3771 6.24664C16.7522 6.62172 16.9629 7.13042 16.9629 7.66086V16.2466C16.9629 17.3512 16.0675 18.2466 14.9629 18.2466H6.96289C5.85832 18.2466 4.96289 17.3512 4.96289 16.2466V4.24664ZM6.96289 10.2466C6.96289 9.69436 7.41061 9.24664 7.96289 9.24664H13.9629C14.5152 9.24664 14.9629 9.69436 14.9629 10.2466C14.9629 10.7989 14.5152 11.2466 13.9629 11.2466H7.96289C7.41061 11.2466 6.96289 10.7989 6.96289 10.2466ZM7.96289 13.2466C7.41061 13.2466 6.96289 13.6944 6.96289 14.2466C6.96289 14.7989 7.41061 15.2466 7.96289 15.2466H13.9629C14.5152 15.2466 14.9629 14.7989 14.9629 14.2466C14.9629 13.6944 14.5152 13.2466 13.9629 13.2466H7.96289Z"
                                class="hover-ch" />
                        </svg>
                    </div>
                    <span class="menu-title text-truncate" data-i18n="Extra Tools">Extra Tools</span>
                </a>
                <ul class="menu-content">
                    <li
                        class="{{ $pageActive == 'technical' ||
                        $pageActive == 'ip-whitelist' ||
                        $pageActive == 'asp-iframe' ||
                        $pageActive == 'transaction-session' ||
                        $pageActive == 'required_fields' ||
                        $pageActive == 'agreement_content' ||
                        $pageActive == 'block-system' ||
                        $pageActive == 'payout-schedule' ||
                        $pageActive == 'categories' ||
                        $pageActive == 'integration-preference' ||
                        $pageActive == 'admin-logs' ||
                        $pageActive == 'mail-templates' ||
                        $pageActive == 'mass-mid'
                            ? 'active'
                            : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('admin.technical') }}">
                            <i class="fa fa-circle"></i>
                            <span class="menu-title text-truncate" data-i18n="Technical & Additional">Technical &
                                Additional</span>
                        </a>
                    </li>
                    @if (auth()->guard('admin')->user()->can(['list-ticket']))
                        <li class="{{ $pageActive == 'ticket' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{!! route('admin.ticket') !!}">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="Tickets">Tickets</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->guard('admin')->user()->can(['view-agreement-upload']))
                        <li class="{{ $pageActive == 'agreement-upload' ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{!! route('agreement-upload') !!}">
                                <i class="fa fa-circle"></i>
                                <span class="menu-title text-truncate" data-i18n="Upload Agreement">Upload
                                    Agreement</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
