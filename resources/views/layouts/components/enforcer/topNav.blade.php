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
    <ul class="topnavbar-nav topnav-right" style="display: flex; align-items: center; gap: -10px;">
        <!-- Notification Bell -->
        <li class="topnav-item dropdown" style="position: relative;">
            <a class="topnav-link" href="#" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="position: relative;">
                <i id="notification-bell" class="fas fa-bell"></i>
                <span id="notification-count" class="badge badge-danger"
                    style="display:none; position: absolute; top: 5px; right: -8px; font-size: 0.6rem;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="notificationDropdown" style="margin-right: 50px;">
                <h6 class="dropdown-header">Notifications</h6>
                <div id="notification-list" class="px-2">
                    <p id="no-notifications" class="text-muted text-left mb-0"></p>
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
                        <a href="enforcer-profile" class="mydropdown-menu-link">
                            <div><i class="fas fa-user"></i></div>
                            <span>Edit Profile</span>
                        </a>
                    </li>
                    <li class="mydropdown-menu-item">
                        <a href="{{ route('enforcer.logout') }}" class="mydropdown-menu-link">
                            <div><i class="fas fa-sign-out-alt"></i></div>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
    <!-- end topnav right -->

    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-database.js"></script>

    <!-- Notification Sound -->
    <audio id="notification-sound" src="{{ asset('assets/sounds/notification.mp3') }}" preload="auto"></audio>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var firebaseConfig = {
                apiKey: "AIzaSyB9JcOfWIAjAhA8rWJOdEp7_AvVfbsiJOY",
                databaseURL: "https://traffic-violation-system-79ff7-default-rtdb.asia-southeast1.firebasedatabase.app",
                projectId: "traffic-violation-system-79ff7",
            };

            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }

            var enforcerId = "{{ session('enforcer_id') }}";
            var notificationList = document.getElementById("notification-list");
            var notificationCount = document.getElementById("notification-count");
            const notificationBell = document.getElementById("notification-bell");
            const notificationDropdown = document.getElementById("notificationDropdown");
            const dropdownMenu = notificationDropdown.nextElementSibling;

            let count = 0;
            let soundUnlocked = false;
            let soundPlaying = false;
            let soundInterval = null;

            const audio = document.getElementById("notification-sound");

            // Unlock audio on first interaction
            document.addEventListener('click', () => {
                if (!soundUnlocked) {
                    audio.play().then(() => {
                        audio.pause();
                        audio.currentTime = 0;
                        soundUnlocked = true;
                    }).catch(err => console.log("Sound blocked:", err));
                }
            }, {
                once: true
            });

            // 🔹 Add default "No new notifications" placeholder
            notificationList.innerHTML = `
        <p id="no-notifications" class="text-muted text-left mb-0 py-2">No new notifications</p>
    `;

            // Firebase notifications listener
            firebase.database().ref("notifications/" + enforcerId).on("value", function(snapshot) {
                notificationList.innerHTML = ""; // Clear list before reloading
                count = 0;

                if (!snapshot.exists()) {
                    // 🔹 Show empty message if no notifications at all
                    notificationList.innerHTML = `
                <p id="no-notifications" class="text-muted text-left mb-0 py-2">No new notifications</p>
            `;
                    notificationCount.style.display = "none";
                    return;
                }

                snapshot.forEach(function(childSnapshot) {
                    var data = childSnapshot.val();

                    // Format timestamp
                    let dateStr = "";
                    if (data.created_at || data.timestamp) {
                        let dt = new Date(data.created_at || data.timestamp);
                        dateStr = dt.toLocaleString("en-US", {
                            dateStyle: "medium",
                            timeStyle: "short"
                        });
                    }

                    if (!data.is_read) {
                        count++;
                        notificationCount.style.display = "inline-block";
                        notificationCount.innerText = count;
                        notificationBell.style.color = "red";
                        notificationBell.classList.add("shake");

                        // Dynamic sound
                        if (soundUnlocked && !soundPlaying) {
                            soundInterval = setInterval(() => {
                                audio.currentTime = 0;
                                audio.play().catch(err => console.log("Sound blocked:", err));
                            }, Math.max(1000, 5000 - count * 500));
                            soundPlaying = true;
                        }
                    }

                    let item = `
                <a class="dropdown-item ${data.is_read ? 'text-muted' : ''}" href="#"
                   data-toggle="modal" data-target="#notificationModal"
                   data-title="${data.title}" data-message="${data.message}" data-date="${dateStr}">
                    <strong>${data.title}</strong><br>
                    <small>${data.message}</small><br>
                    <small class="text-muted">${dateStr}</small>
                </a>
                <div class="dropdown-divider"></div>
            `;

                    notificationList.insertAdjacentHTML("afterbegin", item);
                });

                // 🔹 If still no notifications (after loop)
                if (count === 0 && snapshot.size === 0) {
                    notificationList.innerHTML = `
                <p id="no-notifications" class="text-muted text-center mb-0 py-2">No new notifications</p>
            `;
                    notificationCount.style.display = "none";
                    notificationBell.style.color = "";
                    notificationBell.classList.remove("shake");
                }
            });

            // Notification bell click
            notificationBell.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (dropdownMenu.style.display === "block") {
                    dropdownMenu.style.display = "none";
                } else {
                    dropdownMenu.style.display = "block";

                    // Stop sound
                    if (soundInterval) {
                        clearInterval(soundInterval);
                        soundInterval = null;
                        soundPlaying = false;
                    }

                    // Reset bell UI
                    count = 0;
                    notificationCount.style.display = "none";
                    notificationBell.style.color = "";
                    notificationBell.classList.remove("shake");

                    // Mark notifications as read
                    firebase.database().ref("notifications/" + enforcerId).once("value", function(snapshot) {
                        snapshot.forEach(function(childSnapshot) {
                            var key = childSnapshot.key;
                            var data = childSnapshot.val();

                            if (!data.is_read) {
                                firebase.database().ref("notifications/" + enforcerId + "/" + key).update({
                                    is_read: true
                                });

                                fetch("/enforcer/notifications/mark-read", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        notification_id: data.id
                                    })
                                });
                            }
                        });
                    });
                }
            });

            // Close dropdown if clicked outside
            document.addEventListener("click", function(e) {
                if (!notificationDropdown.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.style.display = "none";
                }
            });
        });

        const body = document.getElementsByTagName('body')[0];

        // Left sidebar toggle
        function collapseSidebar() {
            body.classList.toggle('leftsidebar-expand');
        }

        // Topnavbar dropdown function
        window.onclick = function(event) {
            openCloseDropdown(event);
        }

        function closeAllDropdown() {
            var dropdowns = document.getElementsByClassName('mydropdown-expand');
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].classList.remove('mydropdown-expand');
            }
        }

        function openCloseDropdown(event) {
            if (!event.target.matches('.mydropdown-toggle')) {
                closeAllDropdown();
            } else {
                var toggle = event.target.dataset.toggle;
                var content = document.getElementById(toggle);
                if (content.classList.contains('mydropdown-expand')) {
                    closeAllDropdown();
                } else {
                    closeAllDropdown();
                    content.classList.add('mydropdown-expand');
                }
            }
        }
    </script>
</div>
<!-- Topbar navigation end here ===================================================-->