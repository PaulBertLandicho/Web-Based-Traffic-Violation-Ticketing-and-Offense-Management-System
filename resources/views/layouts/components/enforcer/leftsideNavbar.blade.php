<!-- Left sidebar navigation start here =============================================-->
<div class="leftsidebar overlay-scrollbar scrollbar-hover">
    <div class="text-center p-3 border-b border-gray-300">
        @php
        // Fetch enforcer's profile image from database
        $imagePath = DB::table('traffic_enforcers')
        ->where('enforcer_id', session('enforcer_id'))
        ->value('profile_image');

        // If no uploaded image, use the default
        $profileImage = $imagePath && file_exists(public_path($imagePath))
        ? asset($imagePath)
        : asset('assets/img/default-enforcer.png');
        @endphp

        <!-- Enforcer Profile Image -->
        <img src="{{ $profileImage }}"
            alt="Profile"
            class="rounded-circle mx-auto mb-3"
            style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #00587a;">

        <!-- Enforcer Name -->
        <h6 class="text-white mb-0">{{ session('enforcer_name', 'Traffic Enforcer') }}</h6>
    </div>

    <ul class="leftsidebar-nav overlay-scrollbar scrollbar-hover mt-3">
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
                <span>Reported Driver Violation Records</span>
            </a>
        </li>
    </ul>
</div>
<!-- Left sidebar navigation end here =============================================-->