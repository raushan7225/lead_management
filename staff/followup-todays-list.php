<?php
include("../config/db.php");
include("../config/session.php");
include("../config/fornotification.php");

######### Fetch all employees from the database #########
$Technician_list = "SELECT * FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_of_branch`='$Branch' AND `employee_user_role` = 'Technician' AND `employee_status`='1'";
$Technician_list_result = $conn->query($Technician_list);
$Technicianname = [];
if (mysqli_num_rows($Technician_list_result) > 0) {
    while ($Technician = $Technician_list_result->fetch_assoc()) {
        $Technicianname[] = $Technician;
    }
}
######### Fetch all employees from the database #########


########################  Edit /  Delete  #########################
if (isset($_POST['delete_lead'])) {
    $mylead_id = $_POST['mylead_id'];
    $deleteLeadQuery = "DELETE FROM `leads` WHERE `id` = '$mylead_id'";
    $checkDelete = mysqli_query($conn, $deleteLeadQuery);

    if ($checkDelete) {
        echo "<script>window.location.href='followup-todays-list.php'</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} elseif (isset($_POST['edit_lead'])) {
    $mylead_id = $_POST['mylead_id'];
    echo "<script>window.location.href='followup-edit2.php?id=$mylead_id';</script>";
    exit;
}
########################  Edit /  Delete  #########################


############# Todays Lead #################
$date = date("Y-m-d");
$lead_list = "SELECT * FROM `leads` WHERE `lead_for_company`='$Company' AND `lead_for_branch`='$Branch' AND `lead_next_followup_date` = '$date' AND `lead_assign_to`='$username' AND `lead_updatedby` = '$username' AND `lead_type` NOT IN ('Completed', 'Cancel') AND `lead_customer_status`='1' ORDER BY `id` DESC";
$lead_list_result = $conn->query($lead_list);
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Lead List</title>
    <?php include("head.php"); ?>
</head>

<body>
    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

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
                    <!-- ============= Followups List ============= -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <h4 class="card-title">Today's Followup List </h4>
                                <div class="text-center">
                                    <span class="mx-6"><?php echo date('d-m-Y'); ?> | <span id="time"></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="table-lead-search"></div>
                            </div>
                        </div>
                    </div>
                    <!-- ============= Followups List ============= -->
                </div>
            </div>
        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->

        <!-- Footer -->
        <?php include("../footer.php"); ?>

    </div>
    <!-- END Wrapper -->

    <!-- Vendor Javascript (Require in all Page) -->
    <script src="../assets/js/vendor.js"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="../assets/js/app.js"></script>

    <!-- Grid Js -->
    <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

    <script>
        // Branch Table Data
        const leadData = [
            <?php
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
                                     <?php if (!empty($lead['lead_customer_contact'])) { ?>
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

                        gridjs.html(`<?php echo $lead_id = $lead['lead_id']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_alternate_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_whatsapp']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_email']; ?>`),
                        gridjs.html(`<?php echo ($lead['lead_next_followup_date'] === '0000-00-00') ? '00-00-0000' : date('d-m-Y', strtotime($lead['lead_next_followup_date'])); ?>`),
                        gridjs.html(`<?php echo date('H:i', strtotime($lead['lead_next_followup_time'])); ?>`),
                        gridjs.html(`<?php echo $lead['lead_type']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_status']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_assign_to']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_updatedby']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_remark']; ?>`)
                    ],
            <?php }
            } ?>
        ];

        // Initialize Grid.js Table
        if (document.getElementById("table-lead-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    {
                        name: "Actions",
                        width: "130px"
                    },
                    {
                        name: "Unique ID",
                        width: "100px"
                    },
                    {
                        name: "Org/Client Name",
                        width: "180px"
                    },
                    {
                        name: "Contact",
                        width: "100px"
                    },
                    {
                        name: "2nd Contact",
                        width: "100px"
                    },
                    {
                        name: "Whatsapp",
                        width: "100px"
                    },
                    {
                        name: "Email",
                        width: "200px"
                    },
                    {
                        name: "Next Folloup Date",
                        width: "100px",
                    },
                    {
                        name: "Next Followup Time",
                        width: "100px",
                    },
                    {
                        name: "Lead Type",
                        width: "100px",
                    },
                    {
                        name: "Lead Status",
                        width: "100px",
                    },
                    {
                        name: "Lead Assign To",
                        width: "100px",
                    },
                    {
                        name: "Handled By",
                        width: "100px"
                    },
                    {
                        name: "Remarks",
                        width: "300px"
                    }
                ],
                pagination: {
                    limit: 10
                },
                search: true,
                data: leadData,
                style: {
                    table: {
                        'min-width': '2300px',
                        'font-size': '15px',
                        'text-align': 'center',
                    },
                    th: {
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    },
                }
            }).render(document.getElementById("table-lead-search"));
        }
    </script>
</body>

</html>