<!-- BEGIN: Header-->
<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-dark">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item">
                    <a class="nav-link menu-toggle" href="#">
                        <svg width="21" height="16" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0507812 0.821655H20.7266V3.72937H0.0507812V0.821655ZM0.0507812 6.79462H20.7266V9.70233H0.0507812V6.79462ZM20.7266 12.7676H0.0507812V15.6753H20.7266V12.7676Z" fill="#3E5C76"/>
                        </svg>
                    </a>
                </li>
            </ul>
            @yield('breadcrumbTitle')
        </div>
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown dropdown-notification">
                <a class="nav-link read-notification" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.166 4.84354C6.85234 4.84354 4.16604 7.52983 4.16604 10.8435V14.4293L3.45894 15.1364C3.17294 15.4224 3.08738 15.8525 3.24217 16.2262C3.39695 16.5999 3.76158 16.8435 4.16604 16.8435H16.166C16.5705 16.8435 16.9351 16.5999 17.0899 16.2262C17.2447 15.8525 17.1592 15.4224 16.8732 15.1364L16.166 14.4293V10.8435C16.166 7.52983 13.4798 4.84354 10.166 4.84354Z" fill="#B3ADAD"/>
                    <path d="M10.166 20.8435C8.50916 20.8435 7.16602 19.5004 7.16602 17.8435H13.166C13.166 19.5004 11.8229 20.8435 10.166 20.8435Z" fill="#B3ADAD"/>
                    <circle cx="17.166" cy="5.84354" r="5" fill="#F44336"/>
                    </svg>
                </a>
                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
                    <li class="dropdown-menu-header">
                        <div class="dropdown-header d-flex">
                            <h4 class="notification-title mb-0 me-auto">Notifications</h4>
                            <div class="badge badge-dark rounded-pill new-notification-count">{{count($notifications)}} New</div>
                        </div>
                    </li>
                    <li class="scrollable-container media-list notification-block p-0">
                        @if(count($notifications) > 0)
                            @foreach ($notifications as $notification)
                                <a class="d-flex" href="{{ route('read-admin-notifications', [$notification->id]) }}">
                                    <div class="list-item d-flex align-items-start">
                                        <div class="me-1">
                                            <div class="avatar bg-light-danger">
                                                <div class="avatar-content"><i data-feather="user"></i></div>
                                            </div>
                                        </div>
                                        <div class="list-item-body flex-grow-1">
                                            <p class="media-heading">
                                                <span class="fw-bolder">{{ $notification->title }}</span>
                                            </p>
                                            <small class="notification-text"> {{ $notification->body }}</small>  
                                            <p class="text-right mb-0">
                                                <small>{{ convertDateToLocal($notification->created_at, 'd-m-Y / H:i:s')}}</small>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="text-center mt-1 mb-1">
                                No new notification
                            </div>
                        @endif
                    </li>
                    <li class="dropdown-menu-footer"><a class="btn btn-primary w-100" href="{{ route('admin-notifications') }}">Read all notifications</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="avatar">
                        <img class="round" src="{{ storage_asset('setup/images/avatar7.png')}}" alt="avatar" height="40" width="40">
                        <span class="avatar-status-online"></span>
                    </span>
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name fw-bolder">{{ ucwords(\Session::get('user_name')) }}</span>
                        <span class="user-status">Admin</span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user" style="width: 14rem;">
                    <a class="dropdown-item" href="{!! route('admin-profile') !!}">
                        <svg width="10" height="10" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.48922 1.17094C9.1102 -0.390314 6.8898 -0.390314 6.51078 1.17094C6.26594 2.17949 5.11045 2.65811 4.22416 2.11809C2.85218 1.28212 1.28212 2.85218 2.11809 4.22416C2.65811 5.11045 2.17949 6.26593 1.17094 6.51078C-0.390314 6.8898 -0.390314 9.1102 1.17094 9.48922C2.17949 9.73407 2.65811 10.8896 2.11809 11.7758C1.28212 13.1478 2.85218 14.7179 4.22417 13.8819C5.11045 13.3419 6.26594 13.8205 6.51078 14.8291C6.8898 16.3903 9.1102 16.3903 9.48922 14.8291C9.73407 13.8205 10.8896 13.3419 11.7758 13.8819C13.1478 14.7179 14.7179 13.1478 13.8819 11.7758C13.3419 10.8896 13.8205 9.73407 14.8291 9.48922C16.3903 9.1102 16.3903 6.8898 14.8291 6.51078C13.8205 6.26593 13.3419 5.11045 13.8819 4.22416C14.7179 2.85218 13.1478 1.28212 11.7758 2.11809C10.8896 2.65811 9.73407 2.17949 9.48922 1.17094ZM8 11C9.65685 11 11 9.65685 11 8C11 6.34315 9.65685 5 8 5C6.34315 5 5 6.34315 5 8C5 9.65685 6.34315 11 8 11Z" fill="#B3ADAD"/>
                        </svg>
                        Edit Profile
                    </a>
                    <a class="dropdown-item" href="{!! URL::route('superintendent/logout') !!}">
                        <i class="fa fa-sign-out text-dark-1"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- END: Header-->