<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        
        @yield('breadcrumbTitle')
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <ul class="navbar-nav  justify-content-end">
              
              <li class="nav-item d-flex align-items-center">
                    <div class="dropdown-txt text-center">
                        <p class="text-body font-weight-bold mb-0">{{ ucwords(Auth::guard('agentUser')->user()->name) }}</p>
                        <span class="d-block">Affiliate</span>
                    </div>
                </li>

              <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                 <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                       <i class="sidenav-toggler-line"></i>
                       <i class="sidenav-toggler-line"></i>
                       <i class="sidenav-toggler-line"></i>
                    </div>
                 </a>
              </li>
             <!--  <li class="nav-item d-flex align-items-center">
                 <a class="btn btn-outline-primary btn-sm mb-0 me-3" href="{!! URL::route('logout') !!}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
              </li> -->
              <li class="nav-item px-3 d-flex align-items-center">
                 <a href="javascript:;" class="nav-link text-body p-0">
                 <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                 </a>
              </li>
           </ul>
        </div>
    </div>
</nav>