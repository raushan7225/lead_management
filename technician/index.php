<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");


################# Check Technician #####################
if ($Role !== "Technician") {
    echo "<script>window.location.href='../index.php';</script>";
    exit;
}
################# Check Technician #####################

################# check maintain mode #####################
if ($ModeStatus == 1) {
    header("Location: ../maintenance.php");
    exit;
}
################# check maintain mode #####################

######################## Edit #########################
if (isset($_POST['edit_lead'], $_POST['mylead_id'])) {
    $mylead_id = $_POST['mylead_id'];
    echo "<script>window.location.href='installation-update.php?id=$mylead_id';</script>";
    exit;
}
######################## Edit #########################

$date = date("Y-m-d");

################## Today's Task Count ####################
$sql = "SELECT COUNT(*) as count FROM `leads`  WHERE `lead_for_company` = '$Company'  AND `lead_for_branch` = '$Branch'  AND `assign_technician` = '$username'  AND `lead_delivery_date` = '$date'  AND `lead_instalation_date` = '0000-00-00'  AND `lead_type`='Completed'  AND `lead_customer_status`='1'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$newleadcount = $row['count'];
################## Today's Task Count ####################


################## Upcoming Task Count ####################
$futureDate = (new DateTime())->modify("+365 days")->format("Y-m-d");
$sql = "SELECT COUNT(*) as count FROM `leads` WHERE `lead_for_company` = '$Company'  AND `lead_for_branch` = '$Branch'  AND `assign_technician` = '$username'  AND `lead_delivery_date` > '$date'  AND `lead_delivery_date` <= '$futureDate' AND `lead_instalation_aggreement` = ''  AND `lead_type` = 'Completed'  AND `lead_customer_status` = '1'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$UpcomingTaskCounter = $row['count'];
################## Upcoming Task Count ####################


################## Missed Tasks ####################
$pastDate = (new DateTime())->modify("-365 days")->format("Y-m-d");
$sql = "SELECT COUNT(*) as count FROM `leads` WHERE `lead_for_company` = '$Company'  AND `lead_for_branch` = '$Branch'  AND `assign_technician` = '$username'  AND `lead_delivery_date` < '$date'  AND `lead_delivery_date` >= '$pastDate' AND `lead_instalation_aggreement` = ''  AND `lead_type` = 'Completed'  AND `lead_customer_status` = '1'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$MissedTaskcount = $row['count'];
################## Missed Tasks ####################


################## Total Task Count ####################
$sql = "SELECT COUNT(*) as count FROM `leads` WHERE `lead_for_company` = '$Company'  AND `lead_for_branch` = '$Branch'  AND `assign_technician` = '$username'  AND `lead_type` = 'Completed'  AND `lead_customer_status`='1'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$TotalTaskscount = $row['count'];
################## Total Task Count ####################

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


                    <div class="col-xl-4">
                        <a href="todays-installations.php">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="card-title fs-17">Today's Installation</h3>
                                        <span class="badge bg-primary rounded-pill fs-15 px-2 py-1">
                                            <?php echo $newleadcount; ?>
                                        </span>
                                    </div>
                                </div> <!-- end card body -->
                            </div> <!-- end card -->
                        </a>
                    </div> <!-- end col -->



                    <div class="col-xl-4">
                        <a href="upcomming-installations.php">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="card-title fs-17">Upcomming Installations</h3>
                                        <span class="badge bg-primary rounded-pill fs-15 px-2 py-1">
                                            <?php echo $UpcomingTaskCounter; ?>
                                        </span>
                                    </div>
                                </div> <!-- end card body -->
                            </div> <!-- end card -->
                        </a>
                    </div> <!-- end col -->



                    <div class="col-xl-4">
                        <a href="missed-installations.php">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="card-title fs-17">Missed Installations</h3>
                                        <span class="badge bg-primary rounded-pill fs-15 px-2 py-1">
                                            <?php echo $MissedTaskcount; ?>
                                        </span>
                                    </div>
                                </div> <!-- end card body -->
                            </div> <!-- end card -->
                        </a>
                    </div> <!-- end col -->
                </div> <!-- end row -->

                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"> Today's Installations </h3>
                            </div>
                            <div class="card-body">
                                <div id="all-assigned-tasks"></div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                    
                     <?php
                          if ($AlertNoteDate == date('Y-m-d')) { ?>
                              <!-- =============== Show Alert Notes ================ -->
                              <div class="alert alert-secondary alertNote p-2 mb-0" role="alert">
                                   <div class="d-flex justify-content-between align-items-center position-relative">
                                        <h4 class="alert-heading text-soft-preimary"><?php echo $AlertNoteTitle; ?></h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                   </div>
                                   <div class="alert-body pt-2 border-top">
                                        <?php echo $AlertNoteMessage; ?>
                                   </div>
                              </div>
                              <!-- ==============x====== Show Alert Notes ========x====== -->
                         <?php } ?>
                         
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
        const leadData = [
            <?php
            $date = date("Y-m-d");
            ############ Get Filter On Lead List ##############
            $newlead = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician` = '$username' AND `lead_delivery_date`!='0000-00-00' AND `lead_type`='Completed' AND `lead_instalation_aggreement`='' AND `lead_customer_status`='1' ORDER BY `id` DESC";
            $lead_list_result = $conn->query($newlead);
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
                        gridjs.html(`<?php if ($lead['lead_instalation_date'] === '0000-00-00') {echo '00-00-0000';} else {echo date('d-m-Y', strtotime($lead['lead_instalation_date']));} ?>`),
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
                data: leadData,
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