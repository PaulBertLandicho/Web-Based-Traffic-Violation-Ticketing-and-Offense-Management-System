@extends('layouts.layout')

@section('content')

<!--==================================================================================================================================SECTION_01====================================================================================================================================-->
<!-- Topbar navigation start here ===================================================-->
<div class="topnavbar animated fadeIn">
    <!-- topnav left -->
    <ul class="topnavbar-nav">
        <li class="topnav-item" style="display: flex; align-items: center; gap: 13px;">
            <img src="./assets/img/ICTPMO-logo.png" alt="ICTPMO logo" style="width: 50px; height: 50px; background-color: white; border-radius: 10px; margin-left: 20px;">
            <span style="color: white; font-size: 20px; font-weight: bold;">ICTPMO - Welcome</span>
        </li>
    </ul>

    <!-- end topnav left -->
    <!-- topnav right -->
    <ul class="topnavbar-nav topnav-right">
        <li class="topnav-item">
            <div class="mydropdown">
                <p class="mt-3 mr-4">
                    <a href="./"><span class="btn btn-md btn-danger" data-toggle="modal" data-target="#userLogdin">Exit <i class="fas fa-sign-in-alt" style="font-size: 1rem;"></i></span></a>
                </p>
            </div>
        </li>
    </ul>
    <!-- end topnav right -->
</div>
<!-- Topbar navigation end here ===================================================-->

<div class="hero_area">
    <div class="container home-tiles">
        <div class="row custom-row">
            <a href="enforcer-login">
                <div class="col-md-4 box-btn">
                    <i class="fas fa-book-reader"></i>
                    <h5>Traffic Enforcer Officer</h5>
                </div>
            </a>


            <a href="admin-login" id="adminLogin">
                <div class="col-md-4 box-btn">
                    <i class="fas fa-user-shield"></i>
                    <h5>Traffic Administrative</h5>
                </div>
            </a>
        </div>
    </div>
</div>



</div>

<!--==================================================================================================================================JS_FILES======================================================================================================================================-->
<script src="//code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script type="text/javascript" language="javascript" src="assets/vendors/bootstrap/popper.min.js"></script>
<script type="text/javascript" language="javascript" src="assets/vendors/jquery/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="assets/vendors/bootstrap/bootstrap.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (window.innerWidth <= 768) { // Mobile and tablet screens
            let adminLogin = document.getElementById("adminLogin");
            if (adminLogin) {
                adminLogin.style.display = "none";
            }
        }
    });
</script>

@endsection