@extends('layouts.layout')

@section('content')

<!--==================================================================================================================================SECTION_01====================================================================================================================================-->
<!-- Topbar navigation start here ===================================================-->
<div class="topnavbar animated fadeIn">
    <!-- topnav left -->
    <ul class="topnavbar-nav">
        <li class="topnav-item" style="display: flex; align-items: center; gap: 13px;">
            <img src="{{ asset('assets/img/ICTPMO-logo.png') }}" alt="ICTPMO logo" style="width: 50px; height: 50px; background-color: white; border-radius: 10px; margin-left: 20px;">
            <span class="ictpmo-title">ICTPMO - Iligan City Traffic and Parking Management Office</span>
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
                                    <h1>24/7 Traffic Assistance</h1>
                                    <p>
                                        The ICTPMO Traffic Management Office ensures that road safety and monitoring are
                                        available 24 hours a day, 7 days a week. Our system enables real-time coordination,
                                        emergency response, and data-driven traffic enforcement anytime, anywhere.
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
                                    <h1>Efficient Violation Processing</h1>
                                    <p>
                                        No more manual paperwork or long queues. The ICTPMO online ticketing system allows
                                        motorists to view, verify, and settle their violations efficiently—promoting a faster,
                                        transparent, and accountable traffic management process.
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
                                    <h1>Data-Driven Insights</h1>
                                    <p>
                                        With integrated data analytics, the system helps ICTPMO analyze trends in traffic
                                        violations, identify accident-prone zones, and improve enforcement strategies through
                                        data visualization and digital reports.
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
                                    <h1>Transparency and Accountability</h1>
                                    <p>
                                        Every transaction is recorded securely, ensuring that both motorists and officers are
                                        protected under a fair, transparent, and reliable system—upholding ICTPMO’s mission to
                                        maintain discipline and trust in traffic management.
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
<!-- <style>
    /* ===== Topbar Transparent Modern Design ===== */
    .topnavbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        backdrop-filter: blur(12px);
        background: rgba(0, 0, 0, 0.4);
        /* Transparent dark overlay */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        transition: background 0.3s ease-in-out;
    }
</style> -->
@endsection