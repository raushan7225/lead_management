<?php
include("../config/db.php");
include("../config/session.php");


########################  Edit /  Delete  #########################
if (isset($_POST['edit_lead'])) {
    $mylead_id = $_POST['mylead_id'];
    echo "<script>window.location.href='installation-update.php?id=$mylead_id';</script>";
    exit;
}
########################  Edit /  Delete  #########################
?>


<!DOCTYPE html>
<html lang="en-US">

<head>
    <title> Dashboard </title>
    <?php include("head.php"); ?>
</head>

<body>

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

            <!-- Start Container Fluid -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"> New Leads </h3>
                            </div>
                            <div class="card-body">
                                <div id="all-assigned-tasks"></div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div> <!-- end row -->

            </div>
            <!-- End Container Fluid -->


            <!-- ========== Footer Start ========== -->
            <?php include("../footer.php"); ?>
            <!-- ========== Footer End ========== -->

        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->

    </div>
    <!-- END Wrapper -->

    <!-- Vendor Javascript (Require in all Page) -->
    <script src="../assets/js/vendor.js"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="../assets/js/app.js"></script>

    <!-- Grid Js -->
    <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

    <script>
        // New Lead data table
        const TodayleadData = [
            <?php
            $date = date("Y-m-d");
            ############ Get Filter On Lead List ##############
            $Todaylead = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician` = '$username'  AND `lead_instalation_date` = '0000-00-00' AND `lead_type`='Completed' AND `lead_instalation_aggreement`='' ORDER BY `id` DESC";
            $lead_list_result = $conn->query($Todaylead);
            ############# Filter Applied on Lead #################
            $counterValue = 1;
            if ($counterValue <= $lead_list_result->num_rows) {
                while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                        
                        gridjs.html(`
                            <div class="row gap-2">
                                <div class="col-5">
                                    <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="primary-tooltip" data-bs-title="Edit followups">
                                        <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                        <button type="submit" name="edit_lead" value="<?php echo $lead['id']; ?>" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                    </form>
                                </div>

                                <div class="col-5">
                                    <?php if (!empty($lead_customer_contact)) { ?>
                                        <a href="tel:<?php echo $lead['lead_customer_contact']; ?>"  target="_blank" class="btn btn-soft-success btn-sm" data-bs-toggle="tooltip" data-bs-custom-class="success-tooltip" data-bs-title="Call Now">
                                            <iconify-icon icon="solar:outgoing-call-line-duotone" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                    <?php } else { ?>
                                        <a href="#" target="_blank"  class="btn btn-soft-success btn-sm" disabled data-bs-toggle="tooltip" data-bs-custom-class="success-tooltip" data-bs-title="Call Now">
                                            <iconify-icon icon="solar:call-cancel-line-duotone" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>`),

                            gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                            gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                            gridjs.html(`<?php echo $lead['lead_whatsapp']; ?>`),
                            gridjs.html(`<?php echo date('d-m-Y', strtotime($lead['lead_delivery_date'])); ?>`),
                            gridjs.html(`<?php if ($lead['lead_instalation_date'] === '0000-00-00') {echo '00-00-0000';} else { echo date('d-m-Y', strtotime($lead['lead_instalation_date'])); } ?>`),
                            gridjs.html(`<?php echo $lead['lead_type']; ?>`),
                            gridjs.html(`<?php echo $lead['lead_updatedby']; ?>`)
                    ],
            <?php }
            } ?>
        ];

        // Initialize Grid.js Table
        if (document.getElementById("all-assigned-tasks")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    {
                        name: "Action",
                        width: "130px"
                    },
                    {
                        name: "Org/Client Name",
                        width: "200px"
                    },
                    {
                        name: "Contact",
                        width: "100px"
                    },
                    {
                        name: "Whatsapp",
                        width: "100px"
                    },
                    {
                        name: "Delivery Date",
                        width: "100px",
                    },
                    {
                        name: "Installed Date",
                        width: "100px",
                    },
                    {
                        name: "Lead Type",
                        width: "100px",
                    },
                    {
                        name: "Assigned By",
                        width: "100px",
                    }
                ],
                pagination: {
                    limit: 10
                },
                search: true,
                data: TodayleadData,
                style: {
                    table: {
                        'min-width': '1000px',
                        'font-size': '15px',
                        'text-align': 'center',
                    },
                    th: {
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    },
                }
            }).render(document.getElementById("all-assigned-tasks"));
        }
    </script>
</body>

</html>

<?php
$conn->close();
?>