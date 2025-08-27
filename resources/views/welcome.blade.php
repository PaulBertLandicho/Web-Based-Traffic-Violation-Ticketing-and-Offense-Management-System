@extends('layouts.layout')

@section('content')

<!--==================================================================================================================================SECTION_01====================================================================================================================================-->
<!-- Topbar navigation start here ===================================================-->
<div class="topnavbar animated fadeIn">
    <!-- topnav left -->
    <ul class="topnavbar-nav">
        <li class="topnav-item" style="display: flex; align-items: center; gap: 13px;">
            <img src="{{ asset('assets/img/ICTPMO-logo.png') }}" alt="ICTPMO logo" style="width: 50px; height: 50px; background-color: white; border-radius: 10px; margin-left: 20px;">
            <span style="color: white; font-size: 20px; font-weight: bold;">ICTPMO - Welcome</span>
        </li>
    </ul>
    <!-- end topnav left -->
    <!-- topnav right -->
    <ul class="topnavbar-nav topnav-right">
        <li class="topnav-item">
            <div class="mydropdown">
                <p class="mt-3 mr-4">
                    <a href="#" id="topbarLogin">
                        <span class="btn btn-md btn-danger" data-toggle="modal" data-target="#userLogdin">
                            Log In <i class="fas fa-sign-in-alt" style="font-size: 1rem;"></i>
                        </span>
                    </a>
                </p>
            </div>
        </li>
    </ul>
    <!-- end topnav right -->
</div>
<!-- Topbar navigation end here ===================================================-->


<!--==================================================================================================================================SECTION_02====================================================================================================================================-->

<!-- Main slide show start here =================================================-->
<div class="hero_area">
    <!-- slider section -->
    <section class="slider_section position-relative">
        <div id="Customcarousel1" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="container">
                        <div class="row mt-4">
                            <div class="col-md-6 ">
                                <div class="detail-box animated bounceInLeft custom-slide-animi">
                                    <h1>
                                        Available 24x7
                                    </h1>
                                    <p>
                                        No more holidays or weekends. Our service is available throughout 24 hours every day. No matter, our online service is ready for you anytime.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 animated bounceInUp custom-slide-animi">
                                <div class="img-box">
                                    <img src="{{ asset('assets/img/slide_04.svg') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="container">
                        <div class="row mt-4">
                            <div class="col-md-6 animated bounceInLeft custom-slide-animi">
                                <div class="detail-box">
                                    <h1>
                                        Save Time
                                    </h1>
                                    <p>
                                        Unlike convertional fines system, you don't have to be inques. It takes seconds to pay even you can pay it while you are any kind of busy situation.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 animated bounceInDown custom-slide-animi">
                                <div class="img-box">
                                    <img src="{{ asset('assets/img/slide_01.svg') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item ">
                    <div class="container">
                        <div class="row mt-4">
                            <div class="col-md-6 ">
                                <div class="detail-box animated bounceInLeft custom-slide-animi">
                                    <h1>
                                        Analyze Fines
                                    </h1>
                                    <p>
                                        We keep tracks of your every single payment from the beginning. Furthermore, we are going to analyze your data for you. You will see all of your records with many analysed way at a glance.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 animated bounceInUp custom-slide-animi">
                                <div class="img-box">
                                    <img src="{{ asset('assets/img/slide_02.svg') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item ">
                    <div class="container">
                        <div class="row mt-4">
                            <div class="col-md-6 ">
                                <div class="detail-box animated bounceInLeft custom-slide-animi">
                                    <h1>
                                        Check Past Fines
                                    </h1>
                                    <p>
                                        Every driver has a driving record, which is a record of their driving story show traffic violation. Because your driving record can affect everything from your driving privileges. It's important to check your driving record for accuracy after handling a traffic ticket.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 animated bounceInDown custom-slide-animi">
                                <div class="img-box">
                                    <img src="{{ asset('assets/img/slide_03.svg') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#Customcarousel1" role="button" data-slide="prev">
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#Customcarousel1" role="button" data-slide="next">
            <span class="sr-only">Next</span>
        </a>
    </section>
    <!-- end slider section -->

</div>
<!-- Main slide show end here ========================================-->
</div>

<!-- JS Files -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="{{ asset('assets/vendors/bootstrap/popper.min.js') }}"></script>
<script src="{{ asset('assets/vendors/jquery/jquery-3.5.1.js') }}"></script>
<script src="{{ asset('assets/vendors/bootstrap/bootstrap.min.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const topbarLogin = document.getElementById("topbarLogin");

        topbarLogin.addEventListener("click", function(event) {
            event.preventDefault(); // Prevent default link behavior
            if (window.innerWidth <= 768) {
                // Mobile: go to Enforcer login
                window.location.href = 'enforcer-login';
            } else {
                // Desktop: go to Admin login
                window.location.href = 'admin-login';
            }
        });
    });
</script>
@endsection