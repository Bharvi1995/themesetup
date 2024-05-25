<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-dark">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item">
                    <a class="nav-link menu-toggle" href="#">
                        <svg width="21" height="16" viewBox="0 0 21 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M0.0507812 0.821655H20.7266V3.72937H0.0507812V0.821655ZM0.0507812 6.79462H20.7266V9.70233H0.0507812V6.79462ZM20.7266 12.7676H0.0507812V15.6753H20.7266V12.7676Z"
                                fill="#3E5C76" />
                        </svg>
                    </a>
                </li>
            </ul>
            @yield('breadcrumbTitle')
        </div>
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="avatar">
                        <img class="round" src="{{ storage_asset('NewTheme/images/avatar7.png') }}" alt="avatar"
                            height="40" width="40">
                        <span class="avatar-status-online"></span>
                    </span>
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name fw-bolder">{{ ucwords(Auth::guard('agentUserWL')->user()->name) }}</span>
                        <span class="user-status">Available</span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user" style="width: 14rem;">
                    <a class="dropdown-item" href="{{ url('wl/rp/profile') }}">
                        <svg width="10" height="10" viewBox="0 0 19 19" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M9.48922 1.17094C9.1102 -0.390314 6.8898 -0.390314 6.51078 1.17094C6.26594 2.17949 5.11045 2.65811 4.22416 2.11809C2.85218 1.28212 1.28212 2.85218 2.11809 4.22416C2.65811 5.11045 2.17949 6.26593 1.17094 6.51078C-0.390314 6.8898 -0.390314 9.1102 1.17094 9.48922C2.17949 9.73407 2.65811 10.8896 2.11809 11.7758C1.28212 13.1478 2.85218 14.7179 4.22417 13.8819C5.11045 13.3419 6.26594 13.8205 6.51078 14.8291C6.8898 16.3903 9.1102 16.3903 9.48922 14.8291C9.73407 13.8205 10.8896 13.3419 11.7758 13.8819C13.1478 14.7179 14.7179 13.1478 13.8819 11.7758C13.3419 10.8896 13.8205 9.73407 14.8291 9.48922C16.3903 9.1102 16.3903 6.8898 14.8291 6.51078C13.8205 6.26593 13.3419 5.11045 13.8819 4.22416C14.7179 2.85218 13.1478 1.28212 11.7758 2.11809C10.8896 2.65811 9.73407 2.17949 9.48922 1.17094ZM8 11C9.65685 11 11 9.65685 11 8C11 6.34315 9.65685 5 8 5C6.34315 5 5 6.34315 5 8C5 9.65685 6.34315 11 8 11Z"
                                fill="#B3ADAD" />
                        </svg>
                        Edit Profile
                    </a>
                    <a class="dropdown-item" href="{!! URL::route('wl/rp/logout') !!}" role="button">
                        <i class="fa fa-sign-out text-dark-1"></i>
                        Logout
                    </a>

                </div>
            </li>
        </ul>
    </div>
</nav>
