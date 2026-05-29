<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

####################  Manage Followup / Delete  ######################
if (isset($_POST['delete_lead'])) {
    $myleadid = $_POST['myleadid'];
    $lead_customer_name = $_POST['lead_customer_name'];

    $deleteLeadQuery = "DELETE FROM `importleads` WHERE `id` = '$myleadid'";
    $checkDelete = mysqli_query($conn, $deleteLeadQuery);

    if ($checkDelete) {
        ################# Push Notification ################
        $notify_id = $guid;
        $notification = "The Lead / Customer [ " . $lead_customer_name . " ] removed from imported list by " . $Role . " of " . $Company . ".";
        $notify_type = 'Alert';
        $notify_company = $Company;
        $notify_on = date('Y-m-d');
        $notify_by = $username;
        $notify_action = 'Not Seen';
        $notify_veiwer = $username;
        $PushBranchAddNotification = "INSERT INTO `notifications`(`notify_id`, `notification`, `notify_type`, `notify_company`, `notify_branch`, `notify_on`, `notify_by`, `notify_action`, `notify_veiwer`) VALUES ('$notify_id','$notification','$notify_type','$notify_company','$notify_branch','$notify_on','$notify_by','$notify_action', '$notify_veiwer')";
        $ApplynifyQuery = mysqli_query($conn, $PushBranchAddNotification);
        ################# Push Notification ################
        echo "<script>window.location.href='lead-upload.php'</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
####################  Manage Followup / Delete  ######################


############## Fetch Leads From DB ################
$lead_list = "SELECT * FROM `importleads` WHERE `lead_for_company` = '$Company' AND `lead_createdby` = '$username' AND `lead_assign_to` = '' ORDER BY `id` DESC";
$lead_list_result = $conn->query($lead_list);
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Upload Leads</title>
    <?php include("head.php"); ?>
</head>

<body>
    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- START Wrapper -->
    <div class="wrapper">

        <div>
            <!-- ========== Topbar Start ========== -->
            <?php include("topnav.php"); ?>
            <!-- ========== Topbar End ========== -->

            <!-- ========== App Menu Start ========== -->
            <?php include("sidenav.php"); ?>
            <!-- ========== App Menu End ========== -->

            <!-- Start right Content here -->
            <div class="page-content">

                <!-- Start Container Fluid -->
                <div class="container-xxl">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Upload Leads</div>
                                </div>
                                <div class="card-body">
                                    <!-- Excel file upload form -->
                                    <div class="col-md-12">
                                        <form class="row" action="importData.php" method="post" enctype="multipart/form-data">
                                            <div class="col-lg-10">
                                                <div class="mb-3">
                                                    <label for="fileInput" class="visually-hidden">File</label>
                                                    <input type="file" class="form-control" name="file" id="fileInput" required />
                                                </div>
                                                <small class="text-muted"><strong>Note 1: </strong> <a href="../files/sample.xlsx" download>Download Formate Sample.</a> You can import same as sample data.</small><br>
                                                <small class="text-muted"><strong>Note 2: </strong> We are accepting only .xlsx file. </small>
                                            </div>
                                            <div class="col-2">
                                                <input type="submit" class="btn btn-primary mb-3" name="importSubmit" value="Import Data">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Uploaded Leads Data</div>
                                </div>
                                <div class="card-body">
                                    <div class="col-lg-12">
                                        <div id="table-leads-search"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php include("../footer.php"); ?>
        </div>
    </div>
    <!-- Vendor Javascript (Required on all pages) -->
    <script src="../assets/js/vendor.js"></script>

    <!-- App Javascript (Required on all pages) -->
    <script src="../assets/js/app.js"></script>

    <!-- Import XLSX / XLS File Import JS -->
    <script src="../assets/js/vendor/xlxs/xlsx.full.min.js"></script>

    <!-- Grid.js -->
    <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

    <script>
        // Lead Table Data
        const LeadData = [
            <?php
            $counterValue = 1;
            if ($lead_list_result->num_rows > 0) {
                while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`),

                        gridjs.html(`
                              <div class="col-sm-12">
                                    <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="danger-tooltip" data-bs-title="Delete the lead.">
                                        <input type="hidden" name="myleadid" value="<?php echo $lead['id']; ?>">
                                        <input type="hidden" name="lead_customer_name" value="<?php echo $lead['lead_customer_name']; ?>">
                                        <button type="submit" name="delete_lead" value="<?php echo $lead['id']; ?>" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                    </form>
                              </div>`),

                        gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_whatsapp']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_email']; ?>`),
                        gridjs.html(`<?php echo date('d-m-Y', strtotime($lead['lead_next_followup_date'])); ?>`),
                        gridjs.html(`<?php echo date('H:i', strtotime($lead['lead_next_followup_time'])); ?>`),
                        gridjs.html(`<?php echo $lead['lead_type']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_status']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_assign_to']; ?>`)
                    ],
            <?php }
            } ?>
        ];

        // Initialize Grid.js Table
        if (document.getElementById("table-leads-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    {
                        name: "Actions",
                        width: "30px"
                    },
                    {
                        name: "Org/Client Name",
                        width: "150px"
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
                    }
                ],

                pagination: {
                    limit: 10
                },
                search: true,
                data: LeadData,
                style: {
                    table: {
                        'min-width': '1600px',
                        'font-size': '15px',
                        'text-align': 'center'
                    },
                    th: {
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    }
                }
            }).render(document.getElementById("table-leads-search"));
        }
    </script>

</body>

</html>