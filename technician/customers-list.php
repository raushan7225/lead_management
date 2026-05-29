<?php
include("../config/db.php");
include("../config/session.php");


############### Publish / Unpublish #################
if (isset($_POST['publish'])) {
    $customer_id = $_POST['customer_id'];
    $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
    $updateStatusQuery = "UPDATE `leads` SET `lead_customer_status` = '$new_status', `lead_updatedby` = '$username' WHERE `id` = '$customer_id'";
    $PublishBranch = mysqli_query($conn, $updateStatusQuery);

    if ($PublishBranch) {
        echo "<script>window.location.href='customers-list.php';</script>";
    } else {
        $error_message = mysqli_error($conn);
        echo "<script>alert('Error: $error_message')</script>";
    }
}
############### Publish / Unpublish #################


############### customers Delete / Edit  #################
if (isset($_POST['delete_customer'])) {
    $customer_id = $_POST['customer_id'];
    $deletecustomerQuery = "DELETE FROM `leads` WHERE `lead_for_company` = '$Company' AND `id` = '$customer_id'";
    $checkDelete = mysqli_query($conn, $deletecustomerQuery);

    if ($checkDelete) {
        echo "<script>window.location.href='customers-list.php'</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} elseif (isset($_POST['edit_customer'])) {
    $customer_id = $_POST['customer_id'];
    echo "<script>window.location.href='customer-edit.php?id=$customer_id'</script>";
    exit;
}
############### customers Delete / Edit  #################


######### Fetch all customers from the database ##########
$lead_list = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `assign_technician`='$username' ORDER BY `id` DESC";
$lead_list_result = $conn->query($lead_list);
######### Fetch all customers from the database ##########

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title> Clients / Customers List </title>
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
            <div class="container-xxl">
                <div class="row">
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
            if ($counterValue <= $lead_list_result->num_rows) {
                while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                        gridjs.html(`<?php echo $lead['lead_id']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_alternate_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_whatsapp']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_email']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_type']; ?>`)
                    ],
            <?php }
            } ?>
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
                        width: "150px"
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
                        name: "Email Id",
                        width: "200px"
                    },
                    {
                        name: "Customer Type",
                        width: "100px"
                    }
                ],

                pagination: {
                    limit: 10
                },

                search: true,
                data: customerData,
                style: {
                    table: {
                        'min-width': '1200px',
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