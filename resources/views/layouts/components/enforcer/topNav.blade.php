<!--==================================================================================================================================SECTION_01====================================================================================================================================-->

<!-- Topbar navigation start here ===================================================-->
<div class="topnavbar">
    <!-- topnav left -->
    <ul class="topnavbar-nav">
        <li class="topnav-item" id="toggle">
            <a class="topnav-link">
                <i class="fas fa-bars" onclick="collapseSidebar()"></i>
            </a>
        </li>
        <li class="topnav-item" style="display: flex; align-items: center; gap: 13px;">
            <img src="../assets/img/ICTPMO-logo.png" alt="ICTPMO logo" style="width: 50px; height: 50px; background-color: white; border-radius: 10px;">
            <span style="color: white; font-size: 20px; font-weight: bold;">ICTPMO</span>
        </li>
    </ul>
    <!-- end topnav left -->
    <!-- topnav right -->
    <ul class="topnavbar-nav topnav-right">
        <li class="topnav-item">
            <div class="mydropdown">
                <p class="mydropdown-toggle pro-drop" data-toggle="user-menu">Settings <i class="fas fa-caret-down mydropdown-toggle" data-toggle="user-menu"></i></p>
                <ul id="user-menu" class="mydropdown-menu">
                    <li class="mydropdown-menu-item">
                        <a href="enforcer-profile" class="mydropdown-menu-link">
                            <div>
                                <i class="fas fa-user"></i>
                            </div>
                            <span>Edit Profile</span>
                        </a>
                    </li>
                    <li class="mydropdown-menu-item">
                        <a href="{{ route('enforcer.logout') }}" class="mydropdown-menu-link">
                            <div>
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
    <!-- end topnav right -->

    <script>
        const body = document.getElementsByTagName('body')[0]

        //Left sidebar toggle
        function collapseSidebar() {
            body.classList.toggle('leftsidebar-expand')
        }

        //Topnavbar dropdown function
        window.onclick = function(event) {
            openCloseDropdown(event)
        }

        function closeAllDropdown() {
            var dropdowns = document.getElementsByClassName('mydropdown-expand')
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].classList.remove('mydropdown-expand')
            }
        }

        function openCloseDropdown(event) {
            if (!event.target.matches('.mydropdown-toggle')) {
                closeAllDropdown()
            } else {
                var toggle = event.target.dataset.toggle
                var content = document.getElementById(toggle)
                if (content.classList.contains('mydropdown-expand')) {
                    closeAllDropdown()
                } else {
                    closeAllDropdown()
                    content.classList.add('mydropdown-expand')
                }
            }
        }
    </script>
</div>
<!-- Topbar navigation end here ===================================================-->