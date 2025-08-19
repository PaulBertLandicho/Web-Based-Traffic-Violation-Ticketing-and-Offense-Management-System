@extends('layouts.layout')
@section('title', 'Traffic Administrative | Dashboard')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.footer')

<!-- Dashboard main content start here =================================================-->
<div class="dashwrapper animated fadeIn">
    <div class="container-fluid">
        <h6 class="mt-4 badge badge-pill badge-light tag-hover" style="padding: 10px; font-size: 0.75rem;">Account Holder : <span><a href="profile.php">{{ session('admin_name') }}<span></a></h6>
        <!--Main four count boxes start here-->
        <div class="row p-2">
            @include('layouts.components.admin.chart.countBox')
        </div>
        <!--Main four count boxes end here-->

        <div id="printableReportCharts">
            <!--Charts start here-->
            <div class="row p-2">
                @include('layouts.components.admin.chart.chartReport')
            </div>
            <!--Charts end here-->

            <button class="btn btn-primary d-print-none mb-3" onclick="printOnlyCharts()">
                <i class="fas fa-print"></i> Print Generate Report
            </button>
            <!-- Dashboard main content end here ========================================-->

            <script src="{{ asset('assets/js/PrintChartReport.js') }}"></script>
            <script type="text/javascript" language="javascript" src="{{ asset('assets/vendors/bootstrap/bootstrap.min.js') }}"></script>


            <!-- Violation Count ================================== -->
            <script>
                const violationLabels = <?php echo json_encode($violationTypes); ?>;
                const violationData = <?php echo json_encode($violationCounts); ?>;


                const ctx = document.getElementById('violationTypeChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: violationLabels,
                        datasets: [{
                            label: 'Violation Count',
                            data: violationData,
                            backgroundColor: [
                                '#e74c3c', '#f39c12', '#27ae60', '#2980b9', '#8e44ad',
                                '#1abc9c', '#34495e', '#f1c40f', '#d35400', '#c0392b'
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Most Common Violation Types',
                                font: {
                                    size: 18
                                }
                            },
                            legend: {
                                position: 'right'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.parsed} violation(s)`;
                                    }
                                }
                            }
                        }
                    }
                });


                // <!-- Issued Fine Count ================================== -->
                let issuedFineChart;

                function loadIssuedFineChart(month = '', year = '') {
                    fetch(`/admin/fetch-issued-fines?month=${month}&year=${year}`)
                        .then(response => response.json())
                        .then(data => {
                            const labels = data.labels;
                            const values = data.values;

                            if (issuedFineChart) issuedFineChart.destroy();

                            issuedFineChart = new Chart(document.getElementById("issuedFineCount"), {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: "Issued Fine Count",
                                        backgroundColor: "#5cb85c",
                                        data: values
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: {
                                        duration: 2000,
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false,
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Fines Count'
                                            }
                                        }
                                    }
                                }
                            });
                        });
                }

                document.getElementById('issuedFineMonth').addEventListener('change', function() {
                    loadIssuedFineChart(this.value, document.getElementById('issuedFineYear').value);
                });

                document.getElementById('issuedFineYear').addEventListener('change', function() {
                    loadIssuedFineChart(document.getElementById('issuedFineMonth').value, this.value);
                });

                loadIssuedFineChart('', document.getElementById('issuedFineYear').value);


                // <!-- Pending and Paid fines ================================== -->
                const paid = '{{ $totalPaid ?? 0 }}';
                const pending = '{{ $totalPending ?? 0 }}';

                new Chart(document.getElementById("PendingPaidfines"), {
                    type: 'doughnut',
                    data: {
                        labels: ["Paid Fine Amount (₱)", "Pending Fine Amount (₱)"],
                        datasets: [{
                            backgroundColor: ["#1d9e8b", "#d46d31"],
                            data: [paid, pending]
                        }]
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: false,
                        },
                        animation: {
                            duration: 2000,
                        },
                        legend: {
                            onClick: (e) => e.stopPropagation(),
                            position: 'top',
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return '₱' + parseFloat(context.raw).toLocaleString(undefined, {
                                            minimumFractionDigits: 2
                                        });
                                    }
                                }
                            }
                        }
                    }
                });


                // <!-- Class of Vehicle Distribution ================================== -->
                const vehicleLabels = <?php echo json_encode($vehicleTypes); ?>;
                const vehicleCounts = <?php echo json_encode($vehicleCounts); ?>;

                const vehicleCtx = document.getElementById("vehicleClassChart").getContext("2d");

                new Chart(vehicleCtx, {
                    type: 'doughnut',
                    data: {
                        labels: vehicleLabels,
                        datasets: [{
                            backgroundColor: [
                                "#ff6384", "#36a2eb", "#ffcd56", "#4bc0c0", "#9966ff", "#ff9f40", "#c9cbcf"
                            ],
                            data: vehicleCounts
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: {
                            duration: 2000
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Class of Vehicle Distribution'
                            },
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });


                // <!-- Number of Issued Drivers ================================== -->
                drivers = '<?php echo $issuedDriversCount ?>'
                enforcers = '<?php echo $enforcersCount ?>'
                new Chart(document.getElementById("DriverAndEnforcersCount"), {
                    type: 'pie',
                    data: {
                        labels: ["Number of Issued Drivers", "Number of Traffic Enforcer"],
                        datasets: [{
                            backgroundColor: ["#0275d8", "#e84545"],
                            data: [drivers, enforcers]
                        }]
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: false,
                        },
                        animation: {
                            duration: 2000,
                        },
                        legend: {
                            onClick: (e) => e.stopPropagation(),
                            position: 'top',
                        }
                    }
                });


                // <!-- Number of Violations ================================== -->
                let violationChart;

                function fetchBarangayViolations() {
                    const month = document.getElementById('filterMonth').value;
                    const year = document.getElementById('filterYear').value;

                    fetch(`/admin/barangay-violations?month=${month}&year=${year}`)
                        .then(res => res.json())
                        .then(data => {
                            if (violationChart) {
                                violationChart.destroy(); // remove old chart
                            }

                            const ctx = document.getElementById("violationsPerBarangayChart").getContext("2d");
                            violationChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.labels,
                                    datasets: [{
                                        label: 'Number of Violations',
                                        data: data.data,
                                        fill: true,
                                        borderColor: '#e67e22',
                                        backgroundColor: 'rgba(230, 126, 34, 0.4)',
                                        tension: 0.5,
                                        pointRadius: 3,
                                        pointBackgroundColor: '#e67e22',
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        title: {
                                            display: true,
                                            text: `Number of Violations per Barangay (${month || 'All Months'} ${year})`
                                        },
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Violations Count'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Barangay'
                                            }
                                        }
                                    }
                                }
                            });
                        });
                }

                document.getElementById('filterMonth').addEventListener('change', fetchBarangayViolations);
                document.getElementById('filterYear').addEventListener('change', fetchBarangayViolations);

                fetchBarangayViolations();

                // <!-- Total Fine Amount ================================== -->
                let jan = '{{ $janTotal }}';
                let feb = '{{ $febTotal }}';
                let march = '{{ $marchTotal }}';
                let april = '{{ $aprilTotal }}';
                let may = '{{ $mayTotal }}';
                let june = '{{ $juneTotal }}';
                let july = '{{ $julyTotal }}';
                let aug = '{{ $augustTotal }}';
                let sep = '{{ $sepTotal }}';
                let oct = '{{ $octTotal }}';
                let nov = '{{ $novTotal }}';
                let dec = '{{ $decTotal }}';

                let totalAmountChart;

                function loadTotalAmountChart(month = '', year = '') {
                    fetch(`/admin/fetch-total-amount?month=${month}&year=${year}`)
                        .then(response => response.json())
                        .then(data => {
                            const labels = data.labels;
                            const values = data.values;

                            if (totalAmountChart) totalAmountChart.destroy();

                            totalAmountChart = new Chart(document.getElementById("totalFineAmount"), {
                                type: 'line',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: "Total Fine Amount (₱)",
                                        backgroundColor: "#d46d31",
                                        borderColor: "#d46d31",
                                        data: values,
                                        fill: true,
                                        tension: 0.5
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        title: {
                                            display: true,
                                            text: `Total Fine Amount (${month || 'All Months'} ${year})`
                                        },
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Total Fine Amount'
                                            }
                                        },
                                    }
                                }
                            });
                        });
                }


                document.getElementById('totalAmountMonth').addEventListener('change', function() {
                    loadTotalAmountChart(this.value, document.getElementById('totalAmountYear').value);
                });

                document.getElementById('totalAmountYear').addEventListener('change', function() {
                    loadTotalAmountChart(document.getElementById('totalAmountMonth').value, this.value);
                });

                loadTotalAmountChart('', document.getElementById('totalAmountYear').value);
            </script>
            <script>
                function loadingIcon() {
                    setTimeout(function() {
                        document.getElementById("content").style.display = "block";
                        document.getElementById("loading").style.display = "none";
                    }, 1000);
                }
            </script>
            @endsection