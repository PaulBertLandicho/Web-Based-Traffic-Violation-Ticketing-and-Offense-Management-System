@extends('layouts.layout')
@section('title', 'Traffic Enforcer | Dashboard')

@section('title', 'Enforcer Dashboard')

@section('content')
@include('layouts.components.enforcer.topNav')
@include('layouts.components.enforcer.leftsideNavbar')
@include('layouts.components.footer')

<!--==================================================================================================================================SECTION_03====================================================================================================================================-->

<!-- Dashboard main content start here =================================================-->
<div class="dashwrapper animated fadeIn">
    <div class="container-fluid" style="margin-top: 30px;">
        <h6 class="mt-1 badge badge-pill badge-light tag-hover" style="padding: 10px; font-size: 0.75rem;">Enforcer Officer ID : <a href="profile_details.php">{{ session('enforcer_id') }}</a></h6>
        <div class="row">
            <div class="col-12 p-3 d-lg-none d-md-block d-sm-block">
                <a class="btn btn-secondary btn-lg btn-block" href="add_driver"><span><i style="font-size: 2rem;" class="fas fa-plus-circle"></i> <br>Issue New Driver Fine</span></a>
            </div>
            <!-- Trigger Modal Button -->
            <div class="col-12 p-3 d-lg-none d-md-block d-sm-block">
                <button class="btn btn-secondary btn-lg btn-block" data-toggle="modal" data-target="#pastFinesModal">
                    <i style="font-size: 2rem;" class="fas fa-history"></i><br>
                    View driver's Past Fine
                </button>
            </div>

        </div>
    </div>

    <!--Main four count boxes start here-->
    <div class="row p-2">
        @include('layouts.components.enforcer.chart.countBox')

        <!--Fourth count box end-->
    </div>
    <!--Main four count boxes end here-->

    <!--Charts start here-->
    <div class="row p-2">
        @include('layouts.components.enforcer.chart.chartReport')

    </div>
</div>
<!-- Dashboard main content end here ========================================-->

<!-- Modal -->
<div class="modal fade @if(isset($results)) show d-block @endif" id="pastFinesModal" tabindex="-1" role="dialog" aria-labelledby="pastFinesModalLabel" @if(isset($results)) style="display:block; background-color: rgba(0,0,0,0.5);" @endif>
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-history"></i> Search Driver's Past Fines</h5>
                <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                <form id="pastFinesSearchForm">
                    <div class="form-row align-items-center">
                        <div class="col-md-9 mb-2">
                            <input type="text"
                                class="form-control"
                                name="licenseid"
                                placeholder="Enter License ID"
                                value="{{ request('licenseid') }}"
                                required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>

                <div id="pastFinesResults" class="mt-3"></div>
                <!-- Display Search Results -->
                @if(isset($results))
                @endif

            </div>

        </div>
    </div>
    <!--Charts end here-->
    <!-- Dashboard main content end here ========================================-->

    <script type="text/javascript" language="javascript" src="{{ asset('assets/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.getElementById('pastFinesSearchForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const licenseId = this.licenseid.value;
            const resultsDiv = document.getElementById('pastFinesResults');

            resultsDiv.innerHTML = '<div class="text-center text-muted mt-3"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';

            fetch("{{ route('fines.ajax-search') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        licenseid: licenseId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    resultsDiv.innerHTML = data.html;
                })
                .catch(() => {
                    resultsDiv.innerHTML = '<div class="alert alert-danger mt-3">Something went wrong. Please try again.</div>';
                });
        });
    </script>


    <script>
        // <!-- Reported Fine Count ================================== -->
        const jan = '{{ $janCount ?? 0 }}';
        const feb = '{{ $febCount ?? 0 }}';
        const mar = '{{ $marchCount ?? 0 }}';
        const apr = '{{ $aprilCount ?? 0 }}';
        const may = '{{ $mayCount ?? 0 }}';
        const jun = '{{ $juneCount ?? 0 }}';
        const jul = '{{ $julyCount ?? 0 }}';
        const aug = '{{ $augustCount ?? 0 }}';
        const sep = '{{ $sepCount ?? 0 }}';
        const oct = '{{ $octCount ?? 0 }}';
        const nov = '{{ $novCount ?? 0 }}';
        const dec = '{{ $decCount ?? 0 }}';

        new Chart(document.getElementById("reportedFineCount"), {
            type: 'line',
            data: {
                labels: [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ],
                datasets: [{
                    label: "Issued Fine Count",
                    backgroundColor: "#d9534f",
                    borderColor: "#d9534f",
                    fill: false,
                    data: [
                        jan, feb, mar, apr, may, jun,
                        jul, aug, sep, oct, nov, dec
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


        // <!-- Reported Fine Amount ================================== -->
        let January = '{{ $janTotal ?? 0 }}';
        let February = '{{ $febTotal ?? 0 }}';
        let march = '{{ $marchTotal ?? 0 }}';
        let april = '{{ $aprilTotal ?? 0 }}';
        let May = '{{ $mayTotal ?? 0 }}';
        let june = '{{ $juneTotal ?? 0 }}';
        let july = '{{ $julyTotal ?? 0 }}';
        let august = '{{ $augustTotal ?? 0 }}';
        let september = '{{ $sepTotal ?? 0 }}';
        let october = '{{ $octTotal ?? 0 }}';
        let november = '{{ $novTotal ?? 0 }}';
        let december = '{{ $decTotal ?? 0 }}';


        new Chart(document.getElementById("reportedFineAmount"), {
            type: 'bar',
            data: {
                labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                datasets: [{
                    label: "Issued Fine Amount (₱)",
                    backgroundColor: "#d46d31",
                    data: [jan, feb, march, april, may, june, july, aug, sep, oct, nov, dec]
                }]
            },
            options: {
                legend: {
                    display: false
                },
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000
                }
            }
        });
    </script>

    @endsection