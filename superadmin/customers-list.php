<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################ Common redirect function ################
function redirect($url)
{
    echo "<script>window.location.href='$url';</script>";
    exit;
}
################ Common redirect function ################


################# Handle Publish / Unpublish ####################
if (isset($_POST['publish'])) {
    $customer_id = (int)$_POST['customer_id'];
    $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
    $query = "UPDATE `leads` SET `lead_customer_status` = $new_status, `lead_updatedby` = '$username' WHERE `id` = $customer_id";
    if (mysqli_query($conn, $query)) {
        redirect('customers-list.php');
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
################# Handle Publish / Unpublish ####################


################## Handle Customers Delete / Edit ######################
if (isset($_POST['delete_customer'])) {
    $customer_id = (int)$_POST['customer_id'];
    $lead_customer_name = $_POST['lead_customer_name'];

    $query = "DELETE FROM `leads` WHERE `id` = $customer_id";
    $deleteQuery = mysqli_query($conn, $query);

    if ($deleteQuery) {
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
        redirect('customers-list.php');
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
} elseif (isset($_POST['edit_customer'], $_POST['customer_id'])) {
    $customer_id = (int)$_POST['customer_id'];
    redirect("customer-edit.php?id=$customer_id");
}
################## Handle Customers Delete / Edit ######################


################### Fetch all branches #####################
$branchename = [];
$branch_query = "SELECT * FROM `branches` ORDER BY `branch_name` ASC";
$branch_result = mysqli_query($conn, $branch_query);

if ($branch_result) {
    while ($row = mysqli_fetch_assoc($branch_result)) {
        $branchename[] = $row;
    }
}
################### Fetch all branches #####################


################### Fetch all employees #####################
$Employeename = [];
$employee_where = "`employee_user_role` = 'Staff'";

if (!empty($_GET['lead_for_branch'])) {
    $lead_for_branch = $_GET['lead_for_branch'];
    $employee_where .= " AND `employee_of_branch` = '$lead_for_branch'";
}

$employee_query = "SELECT * FROM `employees` WHERE $employee_where ORDER BY `employee_name` DESC";
$employee_result = mysqli_query($conn, $employee_query);

if ($employee_result) {
    while ($row = mysqli_fetch_assoc($employee_result)) {
        $Employeename[] = $row;
    }
}
################### Fetch all employees #####################


################### Apply Lead Filters #####################
if (isset($_POST['search_lead'])) {
    $filters = [
        'lead_for_company'    => trim($_POST['lead_for_company'] ?? ''),
        'lead_for_branch'    => trim($_POST['lead_for_branch'] ?? ''),
        'lead_assign_to'     => trim($_POST['lead_assign_to'] ?? ''),
        'lead_customer_name' => trim($_POST['lead_customer_name'] ?? ''),
    ];

    $urlParams = [];
    foreach ($filters as $key => $value) {
        if ($value !== '') {
            $urlParams[] = "$key=" . urlencode($value);
        }
    }

    if (!empty($urlParams)) {
        $final_url = 'customers-list.php?' . implode('&', $urlParams);
        redirect($final_url);
    }
}
################### Apply Lead Filters #####################


################### Fetch Leads with filters #####################
$lead_data = [];
$lead_filter_query = "SELECT * FROM `leads` WHERE 1";

$lead_for_company = $_GET['lead_for_company'] ?? '';
$lead_for_branch = $_GET['lead_for_branch'] ?? '';
$lead_assign_to = $_GET['lead_assign_to'] ?? '';
$lead_customer_name = $_GET['lead_customer_name'] ?? '';

if ($lead_for_company !== '') {
    $lead_filter_query .= " AND `lead_for_company` = '$lead_for_company'";
}
if ($lead_for_branch !== '') {
    $lead_filter_query .= " AND `lead_for_branch` = '$lead_for_branch'";
}
if ($lead_assign_to !== '') {
    $lead_filter_query .= " AND `lead_assign_to` = '$lead_assign_to'";
}
if ($lead_customer_name !== '') {
    $lead_filter_query .= " AND `lead_customer_name` LIKE '%$lead_customer_name%'";
}

$lead_filter_query .= " ORDER BY `id` DESC";

$lead_result = mysqli_query($conn, $lead_filter_query);

if ($lead_result) {
    while ($row = mysqli_fetch_assoc($lead_result)) {
        $lead_data[] = $row;
    }
}
################### Fetch Leads with filters #####################
?>






<!DOCTYPE html>
<html lang="en-US">

<head>
    <title> Clients / Customers List </title>
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
                                        <h4 class="card-title">Filter Customers</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Employee Branch -->
                                            <div class="col-lg-4">
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
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label for="lead_assign_to" class="form-label">Handled by</label>
                                                    <select class="form-control" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                        <option value="">All Employees</option>
                                                        <?php foreach ($Employeename as $employee): ?>
                                                            <option value="<?php echo $employee['employee_username']; ?>"
                                                                <?php echo (isset($lead_assign_to) && $employee['employee_username'] == $lead_assign_to) || ($employee['employee_username'] == $_SESSION['username']) ? 'selected' : ''; ?>>
                                                                <?php echo $employee['employee_name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Customer Name Input -->
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label for="lead_customer_name" class="form-label text-dark">Customer Name</label>
                                                    <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Contact Person" value="<?php echo htmlspecialchars($lead_customer_name); ?>">
                                                </div>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="col-lg-12">
                                                <div class="row justify-content-between g-2">
                                                    <div class="col-lg-2">
                                                        <button type="submit" name="search_lead" class="btn btn-soft-success w-100">Search Now</button>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <a href="customers-list.php" class="btn btn-primary w-100">Reset</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>


                    <!-- =============== Customers list ================= -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Customers List</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="table-customer-search"></div>
                            </div>
                        </div>
                    </div>
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
        // customer Table Data
        const customerData = [
            <?php
            $counterValue = 1;
            foreach ($lead_data as $lead) { ?>[
                    gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                    gridjs.html(`<?php echo $lead['lead_id']; ?>`),
                    gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                    gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                    gridjs.html(`<?php echo $lead['lead_alternate_contact'] ?>`),
                    gridjs.html(`<?php echo $lead['lead_whatsapp']; ?>`),
                    gridjs.html(`<?php echo $lead['lead_email']; ?>`),
                    gridjs.html(`<?php echo $lead['lead_for_branch'] ?>`),
                    gridjs.html(`<?php echo $lead['lead_customer_gst']; ?>`),
                    gridjs.html(`<?php echo $lead['lead_customer_cin']; ?>`),
                    gridjs.html(`<?php echo $lead['lead_delivery_address']; ?>`),
                    gridjs.html(`<?php echo $lead['lead_customer_type']; ?>`),

                    gridjs.html(`
            <form action="#" method="post">
                <input type="hidden" name="customer_id" value="<?php echo $lead['id']; ?>">
                <input type="hidden" name="publish" value="0">
                <div class="form-check form-switch flex-box justify-content-center align-items-center">
                    <input class="form-check-input" name="publish" type="checkbox" role="switch" id="flexSwitchCheckChecked<?php echo $lead['id']; ?>" <?php echo ($lead['lead_customer_status'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                </div>
            </form>
            `),

                    gridjs.html(`
            <div class="row gap-1">
                <div class="col-lg-5">
                    <form action="#" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="customer_id" value="<?php echo $lead['id']; ?>">
                        <button type="submit" name="edit_customer" value="<?php echo $lead['id']; ?>" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></button>
                    </form>
                </div>
                <div class="col-lg-5">
                    <form action="#" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="customer_id" value="<?php echo $lead['id']; ?>">
                        <input type="hidden" name="lead_customer_name" value="<?php echo $lead['lead_customer_name']; ?>">
                        <button type="submit" name="delete_customer" value="<?php echo $lead['id']; ?>" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                    </form>
                </div>    
            </div>
        `)
                ],
            <?php }
            ?>
        ];

        // Initialize Grid.js Table
        if (document.getElementById("table-customer-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    {
                        name: "Customer ID",
                        width: "100px"
                    },
                    {
                        name: "Customer Name",
                        width: "100px"
                    },
                    {
                        name: "Contact No.",
                        width: "100px"
                    },
                    {
                        name: "2nd Contact No.",
                        width: "100px"
                    },
                    {
                        name: "Whatsapp No.",
                        width: "100px"
                    },
                    {
                        name: "Email Id",
                        width: "200px"
                    },
                    {
                        name: "Branch",
                        width: "300px"
                    },
                    {
                        name: "GST No.",
                        width: "200px"
                    },
                    {
                        name: "CIN No.",
                        width: "200px"
                    },
                    {
                        name: "Address",
                        width: "200px"
                    },
                    {
                        name: "Customer Type",
                        width: "100px"
                    },
                    {
                        name: "Status",
                        width: "50px"
                    },
                    {
                        name: "Actions",
                        width: "120px"
                    }
                ],

                pagination: {
                    limit: 10
                },

                search: true,
                data: customerData,
                style: {
                    table: {
                        'min-width': '2400px',
                        'font-size': '15px',
                        'text-align': 'center',
                    },
                    th: {
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    },
                }
            }).render(document.getElementById("table-customer-search"));
        }
    </script>

</body>

</html>