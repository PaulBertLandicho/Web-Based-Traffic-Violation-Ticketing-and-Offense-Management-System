<!-- Left sidebar navigation start here =============================================-->
<div class="leftsidebar overlay-scrollbar scrollbar-hover">
    <ul class="leftsidebar-nav overlay-scrollbar scrollbar-hover">
        <!-- Dashboard -->
        <li class="leftsidebar-nav-item">
            <a href="{{ route('enforcer.enforcer-dashboard') }}"
                class="leftsidebar-nav-link {{ request()->routeIs('enforcer.enforcer-dashboard') ? 'active' : '' }}">
                <div>
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Issue Driver -->
        <li class="leftsidebar-nav-item">
            <a href="{{ route('drivers.create') }}"
                class="leftsidebar-nav-link {{ request()->routeIs('drivers.create') ? 'active' : '' }}">
                <div>
                    <i class="fas fa-user-plus"></i>
                </div>
                <span>Issue Driver</span>
            </a>
        </li>

        <!-- View Reported Fine -->
        <li class="leftsidebar-nav-item">
            <a href="{{ route('enforcer.view_fines') }}"
                class="leftsidebar-nav-link {{ request()->routeIs('enforcer.view_fines') ? 'active' : '' }}">
                <div>
                    <i class="fas fa-flag-checkered"></i>
                </div>
                <span>View Reported Fine</span>
            </a>
        </li>
    </ul>
</div>
<!-- Left sidebar navigation end here =============================================-->