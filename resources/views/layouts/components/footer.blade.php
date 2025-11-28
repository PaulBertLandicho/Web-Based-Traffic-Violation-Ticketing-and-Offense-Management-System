<!-- DataTables plugin import start -->
<script type="text/javascript" language="javascript" src="../assets/vendors/jquery/jquery-3.5.1.js"></script> <!-- jQuery -->
<script type="text/javascript" language="javascript" src="../assets/vendors/bootstrap/popper.min.js"></script> <!-- Popper.js (should be before bootstrap.min.js) -->
<script type="text/javascript" language="javascript" src="../assets/vendors/bootstrap/bootstrap.min.js"></script> <!-- Bootstrap JS -->
<script type="text/javascript" language="javascript" src="../assets/vendors/chartjs/Chart.min.js"></script> <!-- Chart.js -->

<!-- DataTables plugin import -->
<script type="text/javascript" language="javascript" src="../assets/vendors/DataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="../assets/vendors/DataTables/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" language="javascript" src="../assets/vendors/DataTables/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="../assets/vendors/DataTables/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="../assets/vendors/DataTables/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="../assets/vendors/DataTables/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="../assets/vendors/DataTables/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="../assets/vendors/DataTables/buttons.print.min.js"></script>
<script type="text/javascript" language="javascript" src="../assets/vendors/bootstrap/bootstrap.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>

<!-- Fix main.js script order -->
<script type="text/javascript" language="javascript" src="../assets/js/main.js"></script> <!-- Main JS Script -->

<!--Boostrap tooltip call script-->

<!-- 
<script>
    $(document).ready(function() {
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csv',
                        className: 'btn btn-primary mb-3'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-success mb-3'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger mb-3'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-dark mb-3'
                    }
                ]
            });
        }
    });
</script> -->
<script>
    setInterval(function() {
        fetch("{{ route('enforcer.check-lock') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.text())
            .then(status => {
                if (status.trim() === "locked") {
                    alert("Your account has been locked by the admin. You will be logged out.");
                    window.location.href = "{{ route('enforcer.logout') }}"; // Adjust if your logout route is different
                }
            })
            .catch(error => {
                console.error("Lock check failed:", error);
            });
    }, 5000); // every 5 seconds
</script>


<script>
    $(document).ready(function() {
        $('.counter').counterUp({
            delay: 10,
            time: 1500
        });
    });
</script>