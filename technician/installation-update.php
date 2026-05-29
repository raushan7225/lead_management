<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    ################## Fetch All Leads ####################
    $FindData = "SELECT * FROM `leads` WHERE `lead_for_company`='$Company' AND `lead_for_branch`='$Branch' AND `id`='$id'";
    $FindDataQuery = mysqli_query($conn, $FindData);
    if (mysqli_num_rows($FindDataQuery) > 0) {
        while ($LeadData = mysqli_fetch_assoc($FindDataQuery)) {
            $lead_customer_name = $LeadData['lead_customer_name'];
            $lead_customer_contact = trim($LeadData['lead_customer_contact']);
            $lead_state = trim($LeadData['lead_state']);
            $lead_type = $LeadData['lead_type'];
            $lead_product_services = $LeadData['lead_product_services'];
            $lead_person = $LeadData['lead_person'];
            $lead_contact = trim($LeadData['lead_contact']);
            $lead_email = $LeadData['lead_email'];
            $lead_instalation_date = $LeadData['lead_instalation_date'];
            $lead_delivery_address = trim($LeadData['lead_delivery_address']);
            $lead_whatsapp = trim($LeadData['lead_whatsapp']);
            $lead_remark = trim($LeadData['lead_remark']);


            // Fetch existing specifications
            $searvice_lead = !empty($lead_product_services) ? explode(', ', $lead_product_services) : [];
            $myServiceLead = [];
            foreach ($searvice_lead as $key => $leadService) {
                $myServiceLead[] = [
                    'leadService' => htmlspecialchars($leadService),
                ];
            }
        }
    }
    ################## Fetch All Leads ####################


    ############### Fetch All States from the database ###############
    $state_list = "SELECT * FROM `states` WHERE `state_status` = '1'";
    $state_list_result = mysqli_query($conn, $state_list);
    $statename = [];
    if (mysqli_num_rows($state_list_result) > 0) {
        while ($state = mysqli_fetch_array($state_list_result)) {
            $statename[] = $state;
        }
    }
    ############### Fetch All States from the database ###############

?>

    <!DOCTYPE html>
    <html lang="en-US">

    <head>
        <title> Installation Update </title>
        <?php include("head.php"); ?>
        <style>
            #services-container .choices {
                width: 98%;
            }
        </style>
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

            <!-- Start right Content here -->
            <div class="page-content">

                <!-- Start Container Fluid -->
                <div class="container-xxl">
                    <form action="#" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Basic Details</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <!--  Customer name -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_customer_name" class="form-label text-dark"> Organization / Customer Name </label>
                                                    <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Customer Name" value="<?php echo $lead_customer_name; ?>" disabled>
                                                </div>
                                            </div>

                                            <!-- Customer Contact -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_customer_contact" class="form-label text-dark"> Contact Number </label>
                                                    <input type="tel" id="lead_customer_contact" name="lead_customer_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}" value="<?php echo $lead_customer_contact; ?>" disabled>
                                                </div>
                                            </div>

                                            <!-- Person Whatsapp No. -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_whatsapp" class="form-label text-dark">Whatsapp No.</label>
                                                    <input type="tel" id="lead_whatsapp" name="lead_whatsapp" class="form-control" placeholder="Whatsapp No." minlength="10" maxlength="10" pattern="[0-9]{10}" value="<?php echo $lead_whatsapp; ?>" disabled>
                                                </div>
                                            </div>


                                            <!-- State List -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_state" class="form-label text-dark">Select State </label>
                                                    <input type="text" id="lead_state" name="lead_state" class="form-control" placeholder="State Name" value="<?php echo $lead_state; ?>" disabled>
                                                </div>
                                            </div>

                                            <!-- Installation Product -->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="lead_product" class="form-label text-dark"> Product Service </label>
                                                    <input type="text" id="lead_product" name="lead_product" class="form-control" placeholder="Instalation Product" value="<?php echo $lead_product_services ?>" disabled>
                                                </div>
                                            </div>


                                            <!-- Agreement Upload -->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="lead_instalation_aggreement" class="form-label text-dark">Installation Agreement Upload <small class="text-success">(Required)</small></label>
                                                    <input type="file" id="lead_instalation_aggreement" name="lead_instalation_aggreement" rows="3" class="form-control" placeholder="Upload Your Aggreement Document" required>
                                                </div>
                                            </div>



                                            <!-- Employee Address -->
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="lead_delivery_address" class="form-label text-dark">Delivery Address </label>
                                                    <textarea type="text" id="lead_delivery_address" name="lead_delivery_address" rows="3" class="form-control" placeholder="Address"><?php echo $lead_delivery_address; ?></textarea>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light mb-3 rounded">
                            <div class="row justify-content-end g-2">
                                <div class="col-lg-2">
                                    <button type="submit" name="update_lead" class="btn btn-outline-secondary w-100">Update Lead</button>
                                </div>
                                <div class="col-lg-2">
                                    <a href="installations.php" class="btn btn-primary w-100">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Container Fluid -->

            <!-- Footer -->
            <?php include("../footer.php"); ?>

        </div>
        <!-- END Wrapper -->

        <!-- Vendor Javascript (Require in all Page) -->
        <script src="../assets/js/vendor.js"></script>

        <!-- App Javascript (Require in all Page) -->
        <script src="../assets/js/app.js"></script>

        <!-- Product List -->
        <script src="../assets/js/productlist.js"></script>
    </body>

    </html>

<?php
    ######################### Update Lead ########################
    if (isset($_POST['update_lead'])) {

        ################### Installation Agreement #####################
        if ($_FILES['lead_instalation_aggreement']['size'] > 0) {
            // Define the file name and temporary name
            $lead_instalation_aggreement = $_FILES['lead_instalation_aggreement']['name'];
            $tmp_name = $_FILES['lead_instalation_aggreement']['tmp_name'];
            $upload_dir = '../uploads/agreement/';
            $upload_file = $upload_dir . basename($lead_instalation_aggreement);

            // Move the uploaded file to the desired directory
            if (move_uploaded_file($tmp_name, $upload_file)) {
                echo "<script>alert('Your agreement uploaded successfully!');</script>";
            }
        }
        ################### Installation Agreement #####################


        $lead_instalation_date = date("Y-m-d");
        ############# Prepare and execute the update query #############
        $updateLead = "UPDATE `leads` SET `lead_delivery_address`='$lead_delivery_address', `lead_instalation_aggreement`='$lead_instalation_aggreement', `lead_instalation_date`='$lead_instalation_date' WHERE `id` = '$id'";
        $ApplyUpdateLeadQuery = mysqli_query($conn, $updateLead);


        ############## handle response ###############
        if ($ApplyUpdateLeadQuery) {
            ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "Machine installation aggreement of customer " . $lead_customer_name . " has been uploaded by technician " . $username . " of " . $Company . ".";
            $activity_type = 'Alert';
            $activity_company = $Company;
            $activity_branch = $Branch;
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################
            echo "<script>window.location.href='index.php'</script>";
            exit;
        } else {
            echo "<script>window.location.href='index.php'</script>";
            exit;
        }
    }
    ######################## Updated Lead #######################
} else {
    echo "<script>window.location.href='installation.php';</script>";
    exit;
}
$conn->close();
?>