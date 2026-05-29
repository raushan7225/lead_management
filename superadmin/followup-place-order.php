<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

########################  Edit / Delete (Simple PHP) #########################
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mylead_id = isset($_POST['mylead_id']) ? $_POST['mylead_id'] : null;

    if (!empty($mylead_id)) {
        if (isset($_POST['delete_lead'])) {
            $deleteQuery = "DELETE FROM `leads` WHERE `id` = '$mylead_id'";
            $deleteResult = mysqli_query($conn, $deleteQuery);

            if ($deleteResult) {
                ################# Push activity ################
                $activity_id = $guid;
                $activity_content = "The customer " . $lead_customer_name . " has been removed by " . $Role . ".";
                $activity_type = 'Alert';
                $activity_company = "";
                $activity_branch = "";
                $activity_on = date('Y-m-d H:i:s');
                $activity_by = $username;
                $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                ################# Push activity ################
                echo "<script>window.location.href='followup-place-order.php';</script>";
            } else {
                echo "Error deleting lead: " . mysqli_error($conn);
            }
        }

        if (isset($_POST['edit_lead'])) {
            echo "<script>window.location.href='followup-edit.php?id=$mylead_id';</script>";
            exit;
        }
    }
}
########################  Edit / Delete (Simple PHP) #########################


######### Fetch all branches from the database #########
$branchename = [];
$branch_list_result = $conn->query("SELECT * FROM `branches` WHERE `branch_status` = '1' ORDER BY `branch_name` ASC");
if ($branch_list_result && mysqli_num_rows($branch_list_result) > 0) {
    while ($row = $branch_list_result->fetch_assoc()) {
        $branchename[] = $row;
    }
}
######### End fetch branches #########


######### Fetch employees depending on selected branch #########
$Employeename = [];
if (isset($_GET['lead_for_branch'])) {
    $lead_for_branch = $_GET['lead_for_branch'];
    $employee_list_result = $conn->query("SELECT * FROM `employees` WHERE `employee_of_branch`='$lead_for_branch' AND `employee_user_role`='Staff' AND `employee_status`='1' ORDER BY `employee_name` ASC");
} else {
    $employee_list_result = $conn->query("SELECT * FROM `employees` WHERE `employee_user_role`='Staff' AND `employee_status`='1' ORDER BY `employee_name` ASC");
}
if ($employee_list_result) {
    $Employeename = mysqli_fetch_all($employee_list_result, MYSQLI_ASSOC);
}
######### End fetch employees #########


############ Apply Filter On Lead List ##############
if (isset($_POST['search_lead'])) {
    $filters = [
        'lead_for_branch'    => $_POST['lead_for_branch'] ?? '',
        'lead_assign_to'     => $_POST['lead_assign_to'] ?? '',
        'lead_customer_name' => $_POST['lead_customer_name'] ?? '',
    ];

    $urlParams = [];
    foreach ($filters as $key => $value) {
        if (!empty($value)) $urlParams[] = "$key=$value";
    }

    if (!empty($urlParams)) {
        $queryString = implode('&', $urlParams);
        echo "<script>window.location.href='followup-place-order.php?$queryString';</script>";
        exit;
    }
}
############ End Apply Filter ##############


############# Extract Filters from URL #################
$lead_for_branch    = $_GET['lead_for_branch'] ?? '';
$lead_assign_to     = $_GET['lead_assign_to'] ?? '';
$lead_customer_name = $_GET['lead_customer_name'] ?? '';
############# End Extract Filters #################


######### Final filtered 'Place Order' lead list #########
$lead_list_query = "SELECT * FROM `leads` 
    WHERE `lead_assign_to` != '' 
    AND `lead_updatedby` != '' 
    AND `lead_type` = 'Place Order'";

if (!empty($lead_for_branch))    $lead_list_query .= " AND `lead_for_branch` = '$lead_for_branch'";
if (!empty($lead_assign_to))     $lead_list_query .= " AND `lead_assign_to` = '$lead_assign_to'";
if (!empty($lead_customer_name)) $lead_list_query .= " AND `lead_customer_name` = '$lead_customer_name'";

$lead_list_query .= " ORDER BY `id` DESC";

$lead_list_result = $conn->query($lead_list_query);
######### End lead list #########

#################### If set lead for company ####################
if (isset($_GET["lead_for_company"])) {
    $lead_for_company = $_GET["lead_for_company"];
    $lead_list_query = "SELECT * FROM `leads` WHERE `lead_for_company` = '$lead_for_company' AND `lead_assign_to` !='' AND `lead_updatedby` !='' AND `lead_type` = 'Place Order' ORDER BY `id` DESC";
    $lead_list_result = $conn->query($lead_list_query);
}
#################### If set lead for company ####################

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Place Order</title>
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
                    <form action="#" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Lead Search Request</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Employee Branch -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_for_branch" class="form-label">Branch</label>
                                                    <select class="form-control" id="lead_for_branch" name="lead_for_branch" data-choices>
                                                        <option value="">All Branches</option>
                                                        <?php foreach ($branchename as $mybranch) { ?>
                                                            <option value="<?php echo $mybranch['branch_name']; ?>"
                                                                <?php echo ($mybranch['branch_name'] == $lead_for_branch) ? 'selected' : ''; ?>>
                                                                <?php echo $mybranch['branch_name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Handled By Employee Dropdown -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_assign_to" class="form-label">Handled by</label>
                                                    <select class="form-control" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                        <option value="">All Employees</option>
                                                        <?php foreach ($Employeename as $employee): ?>
                                                            <option value="<?php echo $employee['employee_username']; ?>"
                                                                <?php echo (isset($lead_assign_to) && $employee['employee_username'] == $lead_assign_to) ? 'selected' : ''; ?>>
                                                                <?php echo $employee['employee_name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Customer Name Input -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_customer_name" class="form-label text-dark">Customer Name</label>
                                                    <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Contact Person" value="<?php echo htmlspecialchars($lead_customer_name); ?>">
                                                </div>
                                            </div>

                                            <!-- Lead Type Dropdown -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_type" class="form-label text-dark">Lead Status</label>
                                                    <select class="form-control" name="lead_type" id="lead_type" data-choices data-choices-search-false>
                                                        <option value="">Select Lead Status</option>
                                                        <option value="Hot" <?php echo ($lead_type == 'Hot') ? 'selected' : ''; ?>>1. Hot</option>
                                                        <option value="Cold" <?php echo ($lead_type == 'Cold') ? 'selected' : ''; ?>>2. Cold</option>
                                                        <option value="Warm" <?php echo ($lead_type == 'Warm') ? 'selected' : ''; ?>>3. Warm</option>
                                                        <option value="Place Order" <?php echo ($lead_type == 'Place Order') ? 'selected' : ''; ?>>5. Place Order</option>
                                                        <option value="Cancel" <?php echo ($lead_type == 'Cancel') ? 'selected' : ''; ?>>6. Cancel</option>
                                                        <option value="Completed" <?php echo ($lead_type == 'Completed') ? 'selected' : ''; ?>>7. Completed</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="col-lg-12">
                                                <div class="row justify-content-between g-2">
                                                    <div class="col-lg-2">
                                                        <button type="submit" name="search_lead" class="btn btn-soft-success w-100">Search Now</button>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <a href="followup-place-order.php" class="btn btn-primary w-100">Reset</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>


                    <!-- ============= Followups List ============= -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <h4 class="card-title">Place Order </h4>
                                <div class="text-center">
                                    <span class="mx-6"><?php echo date('d-m-Y'); ?> | <span id="time"></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="table-place-order-search"></div>
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

                                 <?php if ($lead['lead_customer_contact']) { ?>
                                <div class="col-5">
                                    <a href="tel:<?php echo $lead['lead_customer_contact']; ?>"  target="_blank" class="btn btn-soft-success btn-sm" data-bs-toggle="tooltip" data-bs-custom-class="success-tooltip" data-bs-title="Call Now">
                                        <iconify-icon icon="solar:outgoing-call-line-duotone" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                </div>
                                <?php } ?>
                            </div>`),

                        gridjs.html(`<?php echo $lead_id = $lead['lead_id']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_alternate_contact']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_for_company'] ?>`),
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

        // Initialize Grid.js Table
        if (document.getElementById("table-place-order-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    {
                        name: "Actions",
                        width: "140px"
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
                        name: "Company",
                        width: "300px"
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
                        name: "Next Folloup Date",
                        width: "100px",
                    },
                    {
                        name: "Next Followup Time",
                        width: "100px",
                    },
                    {
                        name: "Lead Type",
                        width: "150px",
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
                        'min-width': '2800px',
                        'font-size': '15px',
                        'text-align': 'center',
                    },
                    th: {
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    },
                }
            }).render(document.getElementById("table-place-order-search"));
        }
    </script>
</body>

</html>