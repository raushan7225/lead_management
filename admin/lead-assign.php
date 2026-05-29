<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################## Fetch All Branches ##################
$branchename = [];
$sql = "SELECT * FROM `branches` WHERE `branch_of_company` = '$Company' AND `branch_status` = '1'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $branchename[] = $row;
    }
}

################## Select Branch ##################
$lead_for_branch = $_POST['lead_for_branch'] ?? '';
$Employeename = [];
$lead_list_result = [];

if ($lead_for_branch) {
    // Get Branch Info
    $GetOurBranch = "SELECT * FROM `branches` WHERE `branch_name` = '$lead_for_branch' AND `branch_status` = '1'";
    $OurBranchQuery = mysqli_query($conn, $GetOurBranch);
    if (mysqli_num_rows($OurBranchQuery) > 0) {
        $branchData = mysqli_fetch_assoc($OurBranchQuery);
        $MyBranch = $branchData['branch_name'];
        $MyCompany = $branchData['branch_of_company'];

        ################## Fetch All Employees ##################
        $sql = "SELECT * FROM `employees` WHERE `employee_of_branch` = '$MyBranch' AND `employee_user_role` = 'Staff' AND `employee_status` = '1'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $Employeename = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        ################## Transfer Logic ##################
        if (isset($_POST['assign_lead'])) {
            $lead_ids = $_POST['lead_id'] ?? [];
            $lead_assign_to = $_POST['lead_assign_to'];
            foreach ($lead_ids as $lead_id) {
                $uid = generateId();
                $leadData = getLeadData($conn, $lead_id);
                if ($leadData) {
                    if (insertLead($conn, $uid, $leadData, $MyBranch, $lead_assign_to)) {
                        updateImportLead($conn, $lead_id, $leadData['lead_createdby'], $MyBranch, $lead_assign_to);
                    }
                }
            }
            ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "Some leads transfer to staff " . $lead_assign_to . " by admin " . $username . " of " . $Company . " company.";
            $activity_type = 'Alert';
            $activity_company = $Company;
            $activity_branch = "$MyBranch";
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################
            echo "<script>window.location.href='lead-assign.php';</script>";
        }

        ################## Fetch Updated Lead List ##################
        $lead_list_sql = "SELECT * FROM `importleads` 
            WHERE `lead_for_company` = '$Company' 
            AND `lead_createdby` = '$username' 
            AND `lead_assign_to` = '' 
            ORDER BY `id` DESC";
        $lead_list_result = mysqli_query($conn, $lead_list_sql);
    }
} else {
    ################## Default Lead List ##################
    $lead_list_sql = "SELECT * FROM `importleads` 
        WHERE `lead_for_company` = '$Company' 
        AND `lead_createdby` = '$username' 
        AND `lead_assign_to` = '' 
        ORDER BY `id` DESC";
    $lead_list_result = mysqli_query($conn, $lead_list_sql);
}

################## Helper Functions ##################
function generateId()
{
    return '#ID' . mt_rand(100000, 9999999);
}

function getLeadData($conn, $id)
{
    $sql = "SELECT * FROM `importleads` WHERE `id` = '$id'";
    $res = mysqli_query($conn, $sql);
    return ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
}

function insertLead($conn, $uid, $data, $branch, $assign_to)
{
    $date = date('Y-m-d');
    $sql = "INSERT INTO `leads` (
        `lead_id`, `lead_customer_name`, `lead_customer_contact`, `lead_for_company`, `lead_for_branch`,
        `lead_delivery_address`, `lead_email`, `lead_followup_date`, `lead_next_followup_date`,
        `lead_next_followup_time`, `lead_delivery_date`, `lead_whatsapp`, `lead_assign_to`,
        `lead_status`, `lead_createdby`, `lead_type`, `lead_customer_type`, `lead_customer_status`
    ) VALUES (
        '$uid', '{$data['lead_customer_name']}', '{$data['lead_customer_contact']}', '{$data['lead_for_company']}',
        '$branch', '{$data['lead_delivery_address']}', '{$data['lead_email']}',
        '{$data['lead_followup_date']}', '{$data['lead_next_followup_date']}', '{$data['lead_next_followup_time']}',
        '$date', '{$data['lead_whatsapp']}', '$assign_to', '{$data['lead_status']}',
        '{$data['lead_createdby']}', '{$data['lead_type']}', '{$data['lead_customer_type']}', '{$data['lead_customer_status']}'
    )";
    return mysqli_query($conn, $sql);
}

function updateImportLead($conn, $id, $createdby, $branch, $assign_to)
{
    $sql = "UPDATE `importleads` SET 
        `lead_createdby` = '$createdby', 
        `lead_for_branch` = '$branch', 
        `lead_assign_to` = '$assign_to' 
        WHERE `id` = '$id'";
    if (!mysqli_query($conn, $sql)) {
        echo "Error: " . mysqli_error($conn);
    } else {
        echo "<script>window.location.href='lead-assign.php';</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Lead Assign</title>
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
                                        <h4 class="card-title">Lead Assign</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Employee Branch -->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="lead_for_branch" class="form-label">Branch <small class="text-success">(Required)</small></label>
                                                    <select class="form-control" id="lead_for_branch" name="lead_for_branch" data-choices onchange="this.form.submit()" required>
                                                        <option value="">All Branches</option>
                                                        <?php foreach ($branchename as $mybranch) { ?>
                                                            <option value="<?php echo $mybranch['branch_name']; ?>"
                                                                <?php echo (isset($_POST['lead_for_branch']) && $mybranch['branch_name'] == $lead_for_branch) ? 'selected' : ''; ?>>
                                                                <?php echo $mybranch['branch_name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                    <small class="text-muted"><strong>Note 3: </strong> Lead assign to branch and staff. If your current uploaded lead not showing then select branch first. </small>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="lead_assign_to" class="form-label">Assign To</label>
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
                                                        <button type="submit" name="assign_lead" class="btn btn-soft-success w-100"> Assign Now </button>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <a href="lead-assign.php" class="btn btn-primary w-100"> Cancel </a>
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
                                <h4 class="card-title">Leads ready for assigning </h4>
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
            if ($counterValue <= $lead_list_result->num_rows) {
                while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`),

                        gridjs.html(`<div class="row gap-2"><div class="col-12">
                        <input type="checkbox" class="form-check-input" name="lead_id[]" value="<?php echo $lead['id']; ?>" form="lead_transfer">
                        </div></div>`),

                        gridjs.html(`<?php echo $lead['lead_id']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
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
                        'min-width': '2700px',
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