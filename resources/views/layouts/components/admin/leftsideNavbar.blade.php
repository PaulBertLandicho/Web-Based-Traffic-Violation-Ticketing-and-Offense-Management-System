<div class="leftsidebar overlay-scrollbar scrollbar-hover">
    <ul class="leftsidebar-nav overlay-scrollbar scrollbar-hover">

        <li class="leftsidebar-nav-item">
            <a href="{{ route('admin.admin-dashboard') }}"
                class="leftsidebar-nav-link {{ request()->is('admin-dashboard') ? 'active' : '' }}">
                <div><i class="fas fa-tachometer-alt"></i></div>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="leftsidebar-nav-item">
            <a href="{{ route('enforcers.create') }}"
                class="leftsidebar-nav-link {{ request()->is('add_enforcer') ? 'active' : '' }}">
                <div><i class="fas fa-address-card"></i></div>
                <span>Add Traffic Officer</span>
            </a>
        </li>

        <li class="leftsidebar-nav-item">
            <a href="{{ route('enforcers.view') }}"
                class="leftsidebar-nav-link {{ request()->is('view_all_enforcers') ? 'active' : '' }}">
                <div><i class="fas fa-users-cog"></i></div>
                <span>Manage Traffic Enforcers</span>
            </a>
        </li>

        <li class="leftsidebar-nav-item">
            <a href="{{ route('violation.create') }}"
                class="leftsidebar-nav-link {{ request()->is('manage_traffic_violations') || request()->is('manage_traffic_violations/view') ? 'active' : '' }}">
                <div><i class="fas fa-receipt"></i></div>
                <span>Manage Traffic Violations</span>
            </a>
        </li>

        <li class="leftsidebar-nav-item">
            <a href="{{ route('admin.view.drivers') }}"
                class="leftsidebar-nav-link {{ request()->is('view_all_drivers') ? 'active' : '' }}">
                <div><i class="fas fa-users"></i></div>
                <span>Manage Driver Violation Records</span>
            </a>
        </li>

        <li class="leftsidebar-nav-item">
            <a href="{{ route('admin.paidTickets') }}"
                class="leftsidebar-nav-link {{ request()->is('paid_fine_tickets') ? 'active' : '' }}">
                <div><i class="fas fa-check-double"></i></div>
                <span>List of Paid Violation Tickets</span>
            </a>
        </li>

        <li class="leftsidebar-nav-item">
            <a href="{{ route('admin.pendingTickets.index') }}"
                class="leftsidebar-nav-link {{ request()->is('pending_fine_tickets') ? 'active' : '' }}">
                <div><i class="fas fa-pause"></i></div>
                <span>List of Pending Violation Tickets</span>
            </a>
        </li>

        <li class="leftsidebar-nav-item">
            <a href="{{ url('sms_activity_log') }}"
                class="leftsidebar-nav-link {{ request()->is('sms_activity_log') ? 'active' : '' }}">
                <div><i class="fas fa-sms"></i></div>
                <span>SMS Activity Logs</span>
            </a>
        </li>

        <li class="leftsidebar-nav-item">
            <a href="{{ route('notifications.index') }}"
                class="leftsidebar-nav-link {{ request()->is('admin/notifications') ? 'active' : '' }}">
                <div><i class="fas fa-bell"></i></div>
                <span>Manage Notifications</span>
                @php
                $unreadCount = \App\Models\Notification::where('is_read', false)->count();
                @endphp
                @if($unreadCount > 0)
                <span class="badge badge-danger ml-2">{{ $unreadCount }}</span>
                @endif
            </a>
        </li>
    </ul>
</div>