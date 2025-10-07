<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title', 'ICTPMO')</title>

    <!-- Meta tags start -->
    <meta charset="UTF-8">
    <meta name="description" content="Traffic Violation Ticketing and Offense Management System">
    <meta name="keywords" content="Traffic Violation Ticketing, Management System">
    <meta name="author" content="ICTPMO - Iligan City Traffic and Parking Management Office">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Meta tags end -->

    <!-- Favicon start -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <!-- Favicon end -->

    <!-- Import lib -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/animatecss/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/bootstrap/bootstrap.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- End import lib -->

    <!-- Import fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Baloo+Chettan|Dosis:400,600,700|Poppins:400,600,700&display=swap" rel="stylesheet" />
    <!-- End fonts -->

    <!-- Import styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/home.css') }}">
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/modalticket.css') }}"> -->
    <link rel="stylesheet" href="@yield('css')">
    <link rel="stylesheet" href="@yield('js')">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- @yield('js') {{-- Or @stack('scripts') --}} -->
    <!-- End styles -->

    <!-- Import fontawesome from CDN -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- End fontawesome from CDN -->

</head>

<body onload="loadingIcon()" class="overlay-scrollbar">
    <!-- Loading Spinner -->
    <div id="loading">
        <table style="margin: 0 auto;">
            <tr>
                <td>
                    <div class="loadingio-spinner-dual-ball-ezjdz35ph7h">
                        <div class="ldio-1l6lp7zdq37">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div id="content" style="display: none;">
        @yield('content')

        <!-- Scripts -->
        <script>
            function loadingIcon() {
                setTimeout(function() {
                    document.getElementById("content").style.display = "block";
                    document.getElementById("loading").style.display = "none";
                });
            }

            // if ('serviceWorker' in navigator) {
            //     navigator.serviceWorker.register('/serviceworker.js')
            //         .then(() => console.log("Service Worker Registered"))
            //         .catch((error) => console.error("Service Worker Registration Failed", error));
            // }
        </script>
</body>

</html>