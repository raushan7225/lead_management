<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


########################  Edit /  Delete  #########################
if (isset($_POST['delete_lead'])) {
    $mylead_id = $_POST['mylead_id'];
    $deleteLeadQuery = "DELETE FROM `leads` WHERE `id` = '$mylead_id'";
    $checkDelete = mysqli_query($conn, $deleteLeadQuery);

    if ($checkDelete) {
        echo "<script>window.location.href='followup-upcoming-list.php'</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} elseif (isset($_POST['edit_lead'])) {
    $mylead_id = $_POST['mylead_id'];
    echo "<script>window.location.href='followup-edit.php?id=$mylead_id';</script>";
    exit;
}
########################  Edit /  Delete  #########################


 
############# Filter Applied on Lead #################
$currentDate = new DateTime();
$endDate = clone $currentDate;
$endDate->modify('+365 days');
$start = $currentDate->format('Y-m-d');
$end = $endDate->format('Y-m-d');
$leadData = [];
$lead_list_query = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` NOT IN ('Completed', 'Cancel') AND `lead_next_followup_date` BETWEEN '$start' AND '$end'ORDER BY `lead_next_followup_date` ASC, `id` DESC";
$lead_list_result = $conn->query($lead_list_query);
if ($lead_list_result && $lead_list_result->num_rows > 0) {
    while ($lead = $lead_list_result->fetch_assoc()) {
        $leadData[] = $lead;
    }
}
############# Filter Applied on Lead #################
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
                                <h4 class="card-title">Upcoming Followup List </h4>
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
        document.addEventListener('DOMContentLoaded', function() {
            const technicianModal = document.getElementById('AssTechnician');
            technicianModal.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget;

                // Extract data attributes
                const leadID = button.getAttribute('data-lead-id');
                const assignTechnician = button.getAttribute('data-assign-technician');

                // Update the modal's hidden and visible fields
                document.querySelector('#AssTechnician input[name="mylead_id"]').value = leadID;

                // Auto-select the technician in the dropdown
                const technicianDropdown = document.getElementById('assignTechnician');
                for (const option of technicianDropdown.options) {
                    if (option.value === assignTechnician) {
                        option.selected = true;
                        break;
                    }
                }
            });
        });
    </script>

    <script>
        // upcomming followups Table Data
        const leadData = [
            <?php
            $counterValue = 1;
            if (!empty($leadData)) {
                foreach ($leadData as $lead) { ?>[
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
                                    <a href="tel:<?php echo $lead['lead_customer_contact']; ?>"  target="_blank" class="btn btn-soft-success btn-sm" data-bs-toggle="tooltip" data-bs-custom-class="success-tooltip" data-bs-title="Call Now">
                                        <iconify-icon icon="solar:outgoing-call-line-duotone" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                </div>
                            </div>`),

                        gridjs.html(`<?php echo $lead_id = $lead['lead_id']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_alternate_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_whatsapp']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_email']; ?>`),
                        gridjs.html(`<?php echo date('d-m-Y', strtotime($lead['lead_next_followup_date'])); ?>`),
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
                        width: "110px"
                    },
                    {
                        name: "Unique ID",
                        width: "100px"
                    },
                    {
                        name: "Org/Client Name",
                        width: "100px"
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
                        width: "80px",
                    },
                    {
                        name: "Next Followup Time",
                        width: "80px",
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
                        width: "100px"
                    },
                    {
                        name: "Handled By",
                        width: "100px"
                    },
                    {
                        name: "Remarks",
                        width: "280px"
                    }
                ],
                pagination: {
                    limit: 10
                },
                search: true,
                data: leadData,
                style: {
                    table: {
                        'min-width': '2550px',
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
<?php
$conn->close();
?>