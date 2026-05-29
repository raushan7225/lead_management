<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

###############  Fetch all employees from the database #################
$Employee_list = "SELECT * FROM `employees` WHERE `employee_of_branch` = '$Branch' AND `employee_user_role` = 'Staff' AND `employee_status` = '1'";
$Employee_list_result = $conn->query($Employee_list);
if ($Employee_list_result) {
    $Employeename = mysqli_fetch_all($Employee_list_result, MYSQLI_ASSOC);
}
###############  Fetch all employees from the database #################

###############  Fetch all leads from the database #################
$lead_list = "SELECT * FROM `leads` WHERE `lead_for_branch` = '$Branch' AND `lead_assign_to` = '$username' AND `lead_updatedby` = '$username' AND `lead_customer_status` = '1' ORDER BY `id` DESC";
$lead_list_result = $conn->query($lead_list);

if (isset($_POST['transfer_lead'])) {
    $lead_ids = $_POST['lead_id'] ?? [];
    $lead_assign_to = $_POST['lead_assign_to'];

    foreach ($lead_ids as $lead_id) {
        $lead_transfer = "UPDATE `leads` SET `lead_for_branch` = '$Branch', `lead_for_company` = '$Company', `lead_assign_to` = '$lead_assign_to', `lead_updatedby` = '' WHERE `id` = '$lead_id'";
        $conn->query($lead_transfer);
    }

    ################# Push activity ################
    $activity_id = $guid;
    $activity_content = "Some leads of transfer to another staff by staff " . $username . " of " . $Branch . " branch.";
    $activity_type = 'Alert';
    $activity_company = $Company;
    $activity_branch = $Branch;
    $activity_on = date('Y-m-d H:i:s');
    $activity_by = $username;
    $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
    $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
    ################# Push activity ################

    echo "<script>window.location.href = 'lead-transfer.php';</script>";
}
###############  Fetch all leads from the database #################
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

    <div class="wrapper">
        <?php include("topnav.php"); ?>
        <?php include("sidenav.php"); ?>

        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Show error message here -->
                        <?php if (isset($error_transfer)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <?php echo $error_transfer; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="#" method="POST" enctype="multipart/form-data" id="lead_transfer">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Lead Transfer</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="lead_assign_to" class="form-label">Transfer To</label>
                                                    <select class="form-control" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                        <option value="">All Employees</option>
                                                        <?php foreach ($Employeename as $employee): ?>
                                                            <option value="<?php echo $employee['employee_username']; ?>" <?php echo (isset($lead_assign_to) && $employee['employee_username'] == $lead_assign_to) ? 'selected' : ''; ?>>
                                                                <?php echo $employee['employee_name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="row justify-content-between g-2">
                                                    <div class="col-lg-2">
                                                        <button type="submit" name="transfer_lead" class="btn btn-soft-success w-100"> Transfer </button>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <a href="lead-transfer.php" class="btn btn-primary w-100"> Cancel </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <h4 class="card-title">All Lead List </h4>
                                <div class="text-center">
                                    <span class="mx-6"><?php echo date('d-m-Y'); ?> | <span id="time"></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="table-lead-search"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("../footer.php"); ?>
    </div>

    <script src="../assets/js/vendor.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

    <script>
        const leadData = [
            <?php
            $counterValue = 1;
            if ($lead_list_result && $lead_list_result->num_rows > 0) {
                while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`),

                        gridjs.html(`<div class="row gap-2"><div class="col-12">
                        <input type="checkbox" class="form-check-input" name="lead_id[]" value="<?php echo $lead['id']; ?>" form="lead_transfer">
                        </div></div>`),

                        gridjs.html(`<?php echo $lead['lead_id']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_for_branch'] ?>`),
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

        if (document.getElementById("table-lead-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    {
                        name: "Select",
                        width: "80px"
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
                        name: "Branch",
                        width: "300px"
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
                        name: "Next Followup Date",
                        width: "100px"
                    },
                    {
                        name: "Next Followup Time",
                        width: "100px"
                    },
                    {
                        name: "Lead Type",
                        width: "100px"
                    },
                    {
                        name: "Lead Status",
                        width: "100px"
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
                        'min-width': '2600px',
                        'font-size': '15px',
                        'text-align': 'center'
                    },
                    th: {
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    }
                }
            }).render(document.getElementById("table-lead-search"));
        }
    </script>
</body>

</html>