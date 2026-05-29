<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################### Show Branches ######################
$Branches = "SELECT * FROM `branches` WHERE `branch_status` = '1'";
$BranchesQuery = mysqli_query($conn, $Branches);
$branchename = [];
if (mysqli_num_rows($BranchesQuery) > 0) {
    while ($branch = mysqli_fetch_array($BranchesQuery)) {
        $branchename[] = $branch;
    }
}
################### Show Branches ######################

$selected_branch = isset($_POST['branch_filter']) ? $_POST['branch_filter'] : '';

$branch_condition = "";
if (!empty($selected_branch)) {
    $branch_condition = " AND `lead_for_branch` = '" . $selected_branch . "'";
}

$today = date('Y-m-d');
$employee_query = "SELECT `lead_assign_to` FROM `leads` WHERE `lead_assign_to` != '' AND `lead_next_followup_date` = '$today' $branch_condition GROUP BY `lead_assign_to` ORDER BY `lead_assign_to` ASC";
$employee_result = mysqli_query($conn, $employee_query);

$lead_data = [];
$types = ['Hot', 'Warm', 'Cold', 'Place Order', 'Cancel', 'Completed'];
$counter = 1;
$total_counts = array_fill_keys($types, 0);

while ($emp = mysqli_fetch_assoc($employee_result)) {
    $emp_name = $emp['lead_assign_to'];
    if (empty($emp_name)) continue;

    $counts = array_fill_keys($types, 0);

    $query = "SELECT `lead_type`, COUNT(*) AS `total` FROM `leads` WHERE `lead_assign_to` = '$emp_name' AND `lead_updatedby` != '' AND `lead_next_followup_date` = '$today' $branch_condition GROUP BY `lead_type` ORDER BY `lead_type` ASC";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $type = $row['lead_type'];
        if (isset($counts[$type])) {
            $counts[$type] = $row['total'];
            $total_counts[$type] += $row['total'];
        }
    }

    $lead_data[] = array_merge([$counter], [$emp_name], array_values($counts));
    $counter++;
}
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Today's Report</title>
    <?php include("head.php"); ?>
    <style>
        #table th {
            background-color: #ff6c2f;
            color: #fff;
        }

        #table td {
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

    <div class="wrapper">
        <?php include("topnav.php"); ?>
        <?php include("sidenav.php"); ?>

        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Today's Report</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="mb-1">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-10">
                                            <select name="branch_filter" class="form-select" data-choices>
                                                <option value="">-- Select Branch --</option>
                                                <?php foreach ($branchename as $branch) { ?>
                                                    <option value="<?php echo $branch['branch_name']; ?>" <?php if ($selected_branch == $branch['branch_name']) echo 'selected'; ?>>
                                                        <?php echo $branch['branch_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-success w-100">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div id="table-lead-type-count"></div>
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
        const leadCountData = <?php echo json_encode($lead_data); ?>;

        if (document.getElementById("table-lead-type-count")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "130px"
                    },
                    "Employee Name",
                    {
                        name: "Hot (<?php echo $total_counts['Hot']; ?>)",
                        width: "130px"
                    },
                    {
                        name: "Warm (<?php echo $total_counts['Warm']; ?>)",
                        width: "130px"
                    },
                    {
                        name: "Cold (<?php echo $total_counts['Cold']; ?>)",
                        width: "130px"
                    },
                    {
                        name: "Place Order (<?php echo $total_counts['Place Order']; ?>)",
                        width: "130px"
                    },
                    {
                        name: "Cancel (<?php echo $total_counts['Cancel']; ?>)",
                        width: "130px"
                    },
                    {
                        name: "Completed (<?php echo $total_counts['Completed']; ?>)",
                        width: "130px"
                    }
                ],
                data: leadCountData,
                search: true,
                pagination: {
                    limit: 10
                },
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
            }).render(document.getElementById("table-lead-type-count"));
        }
    </script>
</body>

</html>