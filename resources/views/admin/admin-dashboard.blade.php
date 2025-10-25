@extends('layouts.layout')
@section('title', 'Traffic Administrative | Dashboard')

@section('content')
@include('layouts.components.admin.topNav')
@include('layouts.components.admin.leftsideNavbar')
@include('layouts.components.footer')

<!-- Dashboard main content start here =================================================-->
<div class="dashwrapper animated fadeIn">
    <div class="container-fluid">
        <h6 class="mt-4 badge badge-pill badge-light tag-hover" style="padding: 10px; font-size: 0.75rem;"><i class="fas fa-user" style="background-color: #333; color: white; padding: 5px; border-radius: 50%; margin-right: 5px;"></i> Account Holder : <span><a href="profile.php">{{ session('admin_name') }}<span></a></h6>
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
            <script src="{{ asset('assets/js/DashboardAnimation.js') }}"></script>
            <script type="text/javascript" language="javascript" src="{{ asset('assets/vendors/bootstrap/bootstrap.min.js') }}"></script>


            <!-- Violation Count ================================== -->
            <script>
                const violationLabels = <?php echo json_encode($violationTypes); ?>;
                const violationData = <?php echo json_encode($violationCounts); ?>;

                const backgroundColors = [
                    '#e74c3c', '#f39c12', '#27ae60', '#2980b9', '#8e44ad',
                    '#1abc9c', '#34495e', '#f1c40f', '#d35400', '#c0392b'
                ];

                // Chart rendering
                const ctx = document.getElementById('violationTypeChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: violationLabels,
                        datasets: [{
                            label: 'Violation Count',
                            data: violationData,
                            backgroundColor: backgroundColors,
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
                                display: false
                            }, // hide right legend, since we’re making our own summary list
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

                // Generate inline color labels beside summary items
                const listContainer = document.getElementById('violationList');
                violationLabels.forEach((label, index) => {
                    const count = violationData[index] ?? 0;
                    const color = backgroundColors[index % backgroundColors.length];

                    const li = document.createElement('li');
                    li.innerHTML = `
            <span style="
                display:inline-block;
                width:14px;
                height:14px;
                background-color:${color};
                border-radius:50%;
                margin-right:8px;
            "></span>
            <strong>${label}:</strong> ${count}
        `;
                    listContainer.appendChild(li);
                });


                // <!-- Number of Issued Fine Count ================================== -->
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
                const paid = parseFloat('{{ $totalPaid ?? 0 }}');
                const pending = parseFloat('{{ $totalPending ?? 0 }}');

                const fineLabels = ["Paid Fine Amount", "Pending Fine Amount"];
                const fineColors = ["#1d9e8b", "#d46d31"];
                const fineCounts = [paid, pending];

                new Chart(document.getElementById("PendingPaidfines"), {
                    type: 'doughnut',
                    data: {
                        labels: fineLabels,
                        datasets: [{
                            backgroundColor: fineColors,
                            data: fineCounts
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: {
                            duration: 2000
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Pending Fine and Paid Fine Amount'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ₱${parseFloat(context.raw).toLocaleString(undefined, {
                            minimumFractionDigits: 2
                        })}`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Build color-coded summary list
                const fineList = document.getElementById('fineList');
                fineLabels.forEach((label, index) => {
                    const count = fineCounts[index] ?? 0;
                    const color = fineColors[index];
                    const li = document.createElement('li');
                    li.innerHTML = `
        <span style="
            display:inline-block;
            width:14px;
            height:14px;
            background-color:${color};
            border-radius:50%;
            margin-right:8px;
        "></span>
        <strong>${label}:</strong> ₱${count.toLocaleString(undefined, { minimumFractionDigits: 2 })}
    `;
                    fineList.appendChild(li);
                });


                // <!-- Class of Vehicle Distribution ================================== -->
                const vehicleLabels = <?php echo json_encode($vehicleTypes); ?>;
                const vehicleCounts = <?php echo json_encode($vehicleCounts); ?>;

                const vehicleColors = [
                    "#ff6384", "#36a2eb", "#ffcd56", "#4bc0c0",
                    "#9966ff", "#ff9f40", "#c9cbcf"
                ];

                // Create the doughnut chart
                const vehicleCtx = document.getElementById("vehicleClassChart").getContext("2d");
                new Chart(vehicleCtx, {
                    type: 'doughnut',
                    data: {
                        labels: vehicleLabels,
                        datasets: [{
                            backgroundColor: vehicleColors,
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
                                display: false
                            } // we'll create our own summary legend
                        }
                    }
                });

                // Create dynamic summary with color dots beside each label
                const vehicleList = document.getElementById('vehicleList');
                vehicleLabels.forEach((label, index) => {
                    const count = vehicleCounts[index] ?? 0;
                    const color = vehicleColors[index % vehicleColors.length];

                    const li = document.createElement('li');
                    li.innerHTML = `
            <span style="
                display:inline-block;
                width:14px;
                height:14px;
                background-color:${color};
                border-radius:50%;
                margin-right:8px;
            "></span>
            <strong>${label}:</strong> ${count} vehicles
        `;
                    vehicleList.appendChild(li);
                });


                // <!-- Number of Issued Drivers and Number of Traffic Enforcer ================================== -->
                const drivers = parseInt('<?php echo $issuedDriversCount ?>');
                const enforcers = parseInt('<?php echo $enforcersCount ?>');

                const driverLabels = ["Total Issued Drivers", "Total Registered Enforcers"];
                const driverColors = ["#0275d8", "#e84545"];
                const driverCounts = [drivers, enforcers];

                new Chart(document.getElementById("DriverAndEnforcersCount"), {
                    type: 'pie',
                    data: {
                        labels: driverLabels,
                        datasets: [{
                            backgroundColor: driverColors,
                            data: driverCounts
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: {
                            duration: 2000
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Total Issued Driver Count and Traffic Enforcer Count'
                            }
                        }
                    }
                });

                // Build color-coded summary list
                const driverList = document.getElementById('driverList');
                driverLabels.forEach((label, index) => {
                    const count = driverCounts[index] ?? 0;
                    const color = driverColors[index];
                    const li = document.createElement('li');
                    li.innerHTML = `
        <span style="
            display:inline-block;
            width:14px;
            height:14px;
            background-color:${color};
            border-radius:50%;
            margin-right:8px;
        "></span>
        <strong>${label}:</strong> ${count.toLocaleString()}
    `;
                    driverList.appendChild(li);
                });


                // <!-- Number of Violations Per Barangay ================================== -->
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

                function loadSummary() {
                    fetch("{{ route('admin.fetch-summary') }}")
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById("totalFine").innerText = "₱" + parseFloat(data.totalFineAmount).toLocaleString(undefined, {
                                minimumFractionDigits: 2
                            });
                            document.getElementById("pendingAmount").innerText = "₱" + parseFloat(data.pendingFineAmount).toLocaleString(undefined, {
                                minimumFractionDigits: 2
                            });
                            document.getElementById("paidAmount").innerText = "₱" + parseFloat(data.paidFineAmount).toLocaleString(undefined, {
                                minimumFractionDigits: 2
                            });
                            document.getElementById("provisionsCount").innerText = data.provisionsCount;
                            document.getElementById("driversCount").innerText = data.issuedDriversCount;
                            document.getElementById("enforcersCount").innerText = data.enforcersCount;
                        });
                }

                // Load every 10 seconds
                setInterval(loadSummary, 10000);

                // Load immediately on page load
                loadSummary();
            </script>
            @endsection