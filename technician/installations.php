<?php
include("../config/db.php");
include("../config/session.php");


################## Edit Redirect ##################
if (isset($_POST['edit_lead'], $_POST['mylead_id'])) {
    $mylead_id = $_POST['mylead_id'];
    echo "<script>window.location.href='installation-update.php?id=$mylead_id';</script>";
    exit;
}
################## Edit Redirect ##################

// ################## Current Date ##################
// $today = date("Y-m-d");
// $next365 = (new DateTime())->modify("+365 days")->format("Y-m-d");
// $prev365 = (new DateTime())->modify("-365 days")->format("Y-m-d");
// ################## Current Date ##################

// ################## Fetch All Leads ##################
// $customername = [];
// $customer_query = " SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician` = '$username' AND `lead_customer_status` = 1";
// $customer_list_result = $conn->query($customer_query);
// if ($customer_list_result && $customer_list_result->num_rows > 0) {
//     while ($row = $customer_list_result->fetch_assoc()) {
//         $customername[] = $row;
//     }
// }
// ################## Fetch All Leads ##################

// ################## Today's Task Count ##################
// $newleadcount = 0;
// $newlead_query = " SELECT COUNT(*) as count FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician` = '$username' AND `assign_technician_date` = '$today' AND `lead_type` = 'Completed'";
// $newlead_result = $conn->query($newlead_query);
// if ($newlead_result) {
//     $row = $newlead_result->fetch_assoc();
//     $newleadcount = (int)$row['count'];
// }
// ################## Today's Task Count ##################

// ################## Total Task Count ##################
// $TotalTaskscount = 0;
// $total_query = " SELECT COUNT(*) as count FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician` = '$username' AND `lead_type` = 'Completed'";
// $total_result = $conn->query($total_query);
// if ($total_result) {
//     $row = $total_result->fetch_assoc();
//     $TotalTaskscount = (int)$row['count'];
// }
// ################## Total Task Count ##################

// ################## Upcoming Task Count ##################
// $UpcomingTaskcount = 0;
// $upcoming_query = " SELECT COUNT(*) as count FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician` = '$username' AND `assign_technician_date` > '$today' AND `assign_technician_date` <= '$next365' AND `lead_type` = 'Completed'";
// $upcoming_result = $conn->query($upcoming_query);
// if ($upcoming_result) {
//     $row = $upcoming_result->fetch_assoc();
//     $UpcomingTaskcount = (int)$row['count'];
// }
// ################## Upcoming Task Count ##################

// ################## Missed Task Count ##################
// $MissedTaskcount = 0;
// $missed_query = " SELECT COUNT(*) as count FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician` = '$username' AND `assign_technician_date` < '$today' AND `assign_technician_date` >= '$prev365' AND `lead_type` = 'Completed'";
// $missed_result = $conn->query($missed_query);
// if ($missed_result) {
//     $row = $missed_result->fetch_assoc();
//     $MissedTaskcount = (int)$row['count'];
// }
// ################## Missed Task Count ##################

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
                                <h3 class="card-title"> All Installations </h3>
                            </div>

                            <div class="card-body">
                                <div id="all-assigned-tasks"></div>
                            </div>

                            <!--================ Modal ================-->
                            <?php
                            ############ Get Filter On Lead List ##############
                            $newlead = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician`='$username' AND `lead_delivery_date`!='0000-00-00' AND `lead_type` = 'Completed' AND `lead_instalation_aggreement`!='' AND `lead_customer_status`='1' ORDER BY `id` DESC";
                            $lead_list_result = $conn->query($newlead);
                            ############# Filter Applied on Lead #################
                            $counterValue = 1;
                            if ($counterValue <= $lead_list_result->num_rows) {
                                while ($lead = $lead_list_result->fetch_assoc()) { ?>
                                    <div class="modal fade" id="userdocument<?php echo $lead['id']; ?>" tabindex="-1" aria-labelledby="userdocument<?php echo $lead['id']; ?>Title" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="userdocument<?php echo $lead['id']; ?>Title">Aggreement Document</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="../uploads/agreement/<?php echo $lead['lead_instalation_aggreement']; ?>" class="img-fluid" style="width: 100%;" alt="agreement">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php }
                            } ?>
                            <!--================ Modal ================-->

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
        const NewleadData = [
            <?php
            ############ Get Filter On Lead List ##############
            $newlead = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician`='$username' AND `lead_delivery_date`!='0000-00-00' AND `lead_type` = 'Completed' AND `lead_instalation_aggreement`!='' ORDER BY `id` DESC";
            $lead_list_result = $conn->query($newlead);
            ############# Filter Applied on Lead #################
            $counterValue = 1;
            if ($counterValue <= $lead_list_result->num_rows) {
                while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter

                        gridjs.html(`
                        <div class="row gap-2">
                            <div class="col-3">
                                <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="primary-tooltip" data-bs-title="Edit followups">
                                    <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                    <button type="submit" name="edit_lead" value="<?php echo $lead['id']; ?>" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                </form>
                            </div>

                            <div class="col-3">
                                    <div class="btn btn-soft-info btn-sm" data-bs-toggle="modal" data-bs-target="#userdocument<?php echo $lead['id']; ?>">
                                       <iconify-icon icon="solar:bill-list-line-duotone" class="align-middle fs-18"></iconify-icon>
                                    </div>
                                </div>

                            <div class="col-3">
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
                        gridjs.html(`<?php echo ($lead['lead_delivery_date'] === '0000-00-00') ? '00-00-0000' : date('d-m-Y', strtotime($lead['lead_delivery_date'])); ?>`),
                        gridjs.html(`<?php echo ($lead['lead_instalation_date'] === '0000-00-00') ? '00-00-0000' : date('d-m-Y', strtotime($lead['lead_instalation_date'])); ?>`),
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
                        width: "170px"
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
                        name: "Installation Date",
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
                data: NewleadData,
                style: {
                    table: {
                        'min-width': '1300px',
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