<!--==================================================================================================================================SECTION_01====================================================================================================================================-->
<style>
    @keyframes bell-shake {
        0% {
            transform: rotate(0deg);
        }

        10% {
            transform: rotate(-15deg);
        }

        20% {
            transform: rotate(15deg);
        }

        30% {
            transform: rotate(-15deg);
        }

        40% {
            transform: rotate(15deg);
        }

        50% {
            transform: rotate(-10deg);
        }

        60% {
            transform: rotate(10deg);
        }

        70% {
            transform: rotate(-5deg);
        }

        80% {
            transform: rotate(5deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }

    .shake {
        animation: bell-shake 1s ease infinite;
        /* repeat forever */
    }
</style>
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
            <span style="color: white; font-size: 20px; font-weight: bold;">ICTPMO - Iligan City Traffic and Parking Management Office</span>
        </li>
    </ul>
    <!-- end topnav left -->
    <!-- topnav right -->
    <ul class="topnavbar-nav topnav-right" style="display: flex; align-items: center; gap: -10px;">
        <!-- Notification Bell -->
        <li class="topnav-item dropdown" style="position: relative;">
            <a class="topnav-link" href="#" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="position: relative;">
                <i id="notification-bell" class="fas fa-bell"></i>
                <span id="notification-count" class="badge badge-danger" style="display: none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown"
                style="width: 300px; max-height: 400px; overflow-y: auto;">
                <h6 class="dropdown-header">Notifications</h6>
                <div id="notification-list">
                    <p class="text-center text-muted mb-0">No new notifications</p>
                </div>
            </div>
        </li>

        <!-- Settings Dropdown -->
        <li class="topnav-item">
            <div class="mydropdown">
                <p class="mydropdown-toggle pro-drop" data-toggle="user-menu">
                    Settings <i class="fas fa-caret-down mydropdown-toggle" data-toggle="user-menu"></i>
                </p>
                <ul id="user-menu" class="mydropdown-menu">
                    <li class="mydropdown-menu-item">
                        <a href="admin-profile" class="mydropdown-menu-link">
                            <div>
                                <i class="fas fa-user"></i>
                            </div>
                            <span>Edit Profile</span>
                        </a>
                    </li>
                    <li class="mydropdown-menu-item">
                        <a href="{{ route('admin.logout') }}" class="mydropdown-menu-link">
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

    <!-- 🔔 Notification Sound -->
    <audio id="notification-sound" src="{{ asset('assets/sounds/notification.mp3') }}" preload="auto"></audio>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-database.js"></script>
    <script>
        const bell = document.getElementById("notification-bell");
        const countBadge = document.getElementById("notification-count");
        const notificationList = document.getElementById("notification-list");
        const audio = document.getElementById("notification-sound");

        let notificationCount = 0;
        let audioEnabled = false; // 🔑 track if user interacted

        // ✅ Enable audio after first user interaction
        document.body.addEventListener("click", () => {
            audioEnabled = true;
        }, {
            once: true
        });

        // ✅ Firebase config
        var firebaseConfig = {
            apiKey: "AIzaSyB9JcOfWIAjAhA8rWJOdEp7_AvVfbsiJOY",
            databaseURL: "https://traffic-violation-system-79ff7-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "traffic-violation-system-79ff7",
        };

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }

        const db = firebase.database();

        // 🔔 Listen for new violations
        db.ref("admin_notifications").on("value", function(snapshot) {
            notificationList.innerHTML = "";
            notificationCount = 0;

            let today = new Date().toISOString().slice(0, 10);

            snapshot.forEach(child => {
                let data = child.val();
                let key = child.key;
                if (!data) return;

                let violationDate = data.created_at ? data.created_at.slice(0, 10) : null;

                if (violationDate === today) {
                    // 🕒 Format the date
                    let formattedDateTime = "";
                    if (data.created_at) {
                        let dateObj = new Date(data.created_at);
                        formattedDateTime = dateObj.toLocaleString("en-US", {
                            month: "short", // Sept
                            day: "numeric", // 14
                            year: "numeric", // 2025
                            hour: "numeric",
                            minute: "2-digit",
                            hour12: true // AM/PM
                        });
                    }

                    let item = `
            <a class="dropdown-item" href="{{ route('admin.pendingTickets.index') }}">
                <strong>${data.title}</strong><br>
                <small>${data.message}</small><br>
                <small>Driver: ${data.driver_name || "Unknown"}</small><br>
                <small class="text-muted">${formattedDateTime}</small>
            </a>
            <div class="dropdown-divider"></div>
        `;
                    notificationList.insertAdjacentHTML("afterbegin", item);

                    if (data.status === "unread") {
                        notificationCount++;
                    }
                }
            });


            if (notificationCount > 0) {
                countBadge.textContent = notificationCount;
                countBadge.style.display = "inline-block";
                bell.classList.add("shake");
                bell.style.color = "red";

                // 🎵 Play sound only if user already interacted
                if (audioEnabled) {
                    audio.currentTime = 0;
                    audio.play().catch(err => console.warn("Sound blocked:", err));
                }
            } else {
                countBadge.style.display = "none";
                bell.classList.remove("shake");
                bell.style.color = "";
            }

            if (!notificationList.innerHTML.trim()) {
                notificationList.innerHTML = `<p class="text-center text-muted mb-0">No new notifications</p>`;
            }
        });

        // ✅ When dropdown opened → reset count + stop shake + update Firebase
        document.getElementById("notificationDropdown").addEventListener("click", function() {
            countBadge.style.display = "none";
            bell.classList.remove("shake");
            bell.style.color = "";

            // Mark today's as read
            let today = new Date().toISOString().slice(0, 10);
            db.ref("admin_notifications").once("value", function(snapshot) {
                snapshot.forEach(child => {
                    let data = child.val();
                    let key = child.key;
                    if (data.created_at && data.created_at.slice(0, 10) === today && data.status === "unread") {
                        db.ref("admin_notifications/" + key).update({
                            status: "read"
                        });
                    }
                });
            });
        });
    </script>

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