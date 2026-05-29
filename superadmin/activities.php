<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Activities</title>
    <?php include("head.php"); ?>
</head>

<body>
    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>
    <!-- Loader -->

    <!-- START Wrapper -->
    <div class="wrapper">
        <!-- ========== Topbar Start ========== -->
        <?php include("topnav.php"); ?>
        <!-- ========== Topbar End ========== -->

        <!-- ========== App Menu Start ========== -->
        <?php include("sidenav.php"); ?>
        <!-- ========== App Menu End ========== -->

        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <!-- ============= Lead List ============= -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title"> All Activities </h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="activities-lists"></div>
                            </div>
                        </div>
                    </div>
                    <!-- ============= Lead List ============= -->
                </div>
            </div>
        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->

        <!-- ========== Footer Start ========== -->
        <?php include("../footer.php"); ?>
        <!-- ========== Footer End ========== -->

    </div>
    <!-- END Wrapper -->

    <!-- Vendor Javascript (Require in all Page) -->
    <script src="../assets/js/vendor.js"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="../assets/js/app.js"></script>

    <!-- Grid Js -->
    <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

    <script>
        const leadData = [
            <?php
            $counterValue = 1;  // Initialize the counter for So.No.
            foreach ($AllActionsList as $ActionAll) { ?>[
                    gridjs.html(`<center><?php echo $counterValue++; ?></center>`),
                    gridjs.html(`<?php echo $ActionAll['activity_content']; ?>`),
                    gridjs.html(`<center><?php echo date('d-m-Y  H:i:s', strtotime($ActionAll['activity_on'])); ?></center>`),
                    gridjs.html(`<center><?php echo $ActionAll['activity_by']; ?></center>`)
                ],
            <?php } ?>
        ];

        // Initialize Grid.js Table
        if (document.getElementById("activities-lists")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "80px"
                    },
                    "Activities",
                    {
                        name: "Date & Time",
                        width: "200px"
                    },
                    {
                        name: "By",
                        width: "120px"
                    }
                ],
                pagination: {
                    limit: 20
                },
                search: true,
                data: leadData,
                style: {
                    table: {
                        'min-width': '1200px',
                        'font-size': '15px',
                    },
                    th: {
                        'text-align': 'center',
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    }
                }
            }).render(document.getElementById("activities-lists"));
        }
    </script>
</body>

</html>