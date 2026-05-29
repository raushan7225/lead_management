<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

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


    ################### Show Leads Details ########################
    $checkLeads = "SELECT * FROM `leads` WHERE `id` = '$id'";
    $checkLeadsQuery = mysqli_query($conn, $checkLeads);
    if (mysqli_num_rows($checkLeadsQuery) > 0) {
        while ($lead = mysqli_fetch_assoc($checkLeadsQuery)) {
            $lead_id = trim($lead['lead_id']);
            $lead_customer_name = $lead['lead_customer_name'];
            $lead_customer_contact = trim($lead['lead_customer_contact']);
            $lead_alternate_contact = trim($lead['lead_alternate_contact']);
            $lead_customer_type = $lead['lead_customer_type'];
            $lead_of_company = $lead['lead_of_company'];
            $lead_for_branch = $lead['lead_for_branch'];
            $lead_state = $lead['lead_state'];
            $lead_customer_type = $lead['lead_customer_type'];
            $lead_customer_gst = trim($lead['lead_customer_gst']);
            $lead_customer_cin = trim($lead['lead_customer_cin']);
            $lead_email = trim($lead['lead_email']);
            $lead_delivery_address = trim($lead['lead_delivery_address']);
            $lead_whatsapp = trim($lead['lead_whatsapp']);
        }
    }
    ################### Show Leads Details ########################
?>

    <!DOCTYPE html>
    <html lang="en-US">

    <head>
        <title>Update Client / Customer</title>
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

            <!-- Start right Content here -->
            <div>
                <div class="page-content">

                    <!-- Start Container Fluid -->
                    <div class="container-xxl">
                        <form action="#" method="POST" enctype="multipart/form-data" onsubmit="return submitForm()">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Update Customers</h4>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">

                                                <!--  Customer name -->
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="lead_customer_name" class="form-label text-dark"> Organization / Customer Name </label>
                                                        <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>">
                                                        <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Customer Name" value="<?php echo $lead_customer_name; ?>">
                                                    </div>
                                                </div>

                                                <!-- User Type -->
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="lead_customer_type" class="form-label text-dark">Customer Type <small class="text-success">(Required)</small></label>
                                                        <select name="lead_customer_type" id="lead_customer_type" data-choices required>
                                                            <option value="">Selec Customer Type</option>
                                                            <option value="Client" <?php echo ($lead_customer_type == 'Client') ? 'selected' : ''; ?>>Client</option>
                                                            <option value="Dealer" <?php echo ($lead_customer_type == 'Dealer') ? 'selected' : ''; ?>>Dealer</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Customer Contact -->
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="lead_customer_contact" class="form-label text-dark"> Contact Number <small class="text-success">(Required)</small></label>
                                                        <input type="tel" id="lead_customer_contact" name="lead_customer_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}" value="<?php echo $lead_customer_contact; ?>" required>
                                                    </div>
                                                </div>

                                                <!-- Alternate Phone -->
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="lead_alternate_contact" class="form-label text-dark"> Alternative Phone No. </label>
                                                        <input type="tel" id="lead_alternate_contact" name="lead_alternate_contact" class="form-control" placeholder="Phone No." value="<?php echo $lead_alternate_contact; ?>" minlength="10" maxlength="10" maxlength="10">
                                                    </div>
                                                </div>

                                                <!-- Customer GST -->
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="lead_customer_gst" class="form-label text-dark"> GST No. </label>
                                                        <input type="text" id="lead_customer_gst" name="lead_customer_gst" class="form-control" placeholder="GST No." value="<?php echo $lead_customer_gst; ?>">
                                                    </div>
                                                </div>

                                                <!-- customer CIN No. -->
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="lead_customer_cin" class="form-label text-dark">CIN No.</label>
                                                        <input type="text" id="lead_customer_cin" name="lead_customer_cin" class="form-control" placeholder="CIN No." value="<?php echo $lead_customer_cin; ?>">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Additional Information</h4>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">

                                                <!-- State List -->
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="lead_state" class="form-label text-dark">Select State </label>
                                                        <select name="lead_state" id="lead_state" class="form-control" data-choices>
                                                            <option value="">Select State</option>
                                                            <?php foreach ($statename as $states) { ?>
                                                                <option value="<?php echo $states['state_name']; ?>" <?php echo ($states['state_name'] == $lead_state) ? 'selected' : ''; ?>><?php echo $states['state_name']; ?></option> <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>


                                                <!-- Customer Whatsapp -->
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="lead_whatsapp" class="form-label text-dark"> Whatsapp No. </label>
                                                        <input type="tel" id="lead_whatsapp" name="lead_whatsapp" class="form-control" placeholder="Phone No." value="<?php echo $lead_whatsapp; ?>" minlength="10" maxlength="10">
                                                    </div>
                                                </div>

                                                <!-- customer Email -->
                                                <div class="col-lg-12">
                                                    <div class="mb-3">
                                                        <label for="lead_email" class="form-label text-dark">Email</label>
                                                        <input type="email" id="lead_email" name="lead_email" class="form-control" placeholder="Email" value="<?php echo $lead_email; ?>">
                                                    </div>
                                                </div>

                                                <!-- Employee Address -->
                                                <div class="col-lg-12">
                                                    <label for="lead_delivery_address" class="form-label text-dark">Delivery Address </label>
                                                    <textarea type="text" id="lead_delivery_address" name="lead_delivery_address" rows="2" class="form-control" placeholder="Address"><?php echo $lead_delivery_address; ?></textarea>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-light mb-3 rounded">
                                <div class="row justify-content-end g-2">
                                    <div class="col-lg-2">
                                        <button type="submit" name="update_customer" class="btn btn-outline-secondary w-100">Update Customer</button>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="customers-list.php" class="btn btn-primary w-100">Cancel</a>
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
        </div>
        <!-- END Wrapper -->

        <!-- Vendor Javascript (Require in all Page) -->
        <script src="../assets/js/vendor.js"></script>

        <!-- App Javascript (Require in all Page) -->
        <script src="../assets/js/app.js"></script>
    </body>

    </html>

<?php
    if (isset($_POST['update_customer'])) {
        $lead_customer_name = $_POST['lead_customer_name'];
        $lead_customer_type = $_POST['lead_customer_type'];
        $lead_customer_contact = trim($_POST['lead_customer_contact']);
        $lead_alternate_contact = trim($_POST['lead_alternate_contact']);
        $lead_customer_gst = trim($_POST['lead_customer_gst']);
        $lead_customer_cin = trim($_POST['lead_customer_cin']);
        $lead_state = $_POST['lead_state'];
        $lead_whatsapp = trim($_POST['lead_whatsapp']);
        $lead_email = trim($_POST['lead_email']);
        $lead_delivery_address = trim($_POST['lead_delivery_address']);
        $lead_customer_status = 1;

        // echo $lead_for_company . "<br>";
        // echo $lead_for_branch . "<br>";
        // echo $lead_customer_name . "<br>";
        // echo $lead_customer_type . "<br>";
        // echo $lead_customer_contact . "<br>";
        // echo $lead_alternate_contact . "<br>";
        // echo $lead_customer_gst . "<br>";
        // echo $lead_customer_cin . "<br>";
        // echo $lead_state . "<br>";
        // echo $lead_whatsapp . "<br>";
        // echo $lead_email . "<br>";
        // echo $lead_delivery_address . "<br>";
        // echo $lead_updatedby . "<br>";
        // echo $lead_customer_status;


        $UpdateCustomer = "UPDATE `leads` SET 
        `lead_customer_name`='$lead_customer_name',
        `lead_customer_contact`='$lead_customer_contact',
        `lead_alternate_contact`='$lead_alternate_contact',
        `lead_state`='$lead_state',
        `lead_whatsapp`='$lead_whatsapp',
        `lead_email`='$lead_email',
        `lead_customer_gst`='$lead_customer_gst',
        `lead_customer_cin`='$lead_customer_cin',
        `lead_delivery_address`='$lead_delivery_address',
        `lead_customer_type`='$lead_customer_type',
        `lead_customer_status`='$lead_customer_status' WHERE `id` = '$id'";
        $UpdateCustomerQuery = mysqli_query($conn, $UpdateCustomer);

        if ($UpdateCustomerQuery) {

            ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "Details of customer " . $lead_customer_name . " has been updated by admin " . $username . " of " . $Company . " company.";
            $activity_type = 'Alert';
            $activity_company = $Company;
            $activity_branch = "";
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################

            echo "<script>window.location.href = 'customers-list.php';</script>";
        } else {
            echo "<script>alert('Customer not updated, please try again.');</script>";
        }
    }
    ##################### Customer Edit ####################
} else {
    echo "<script>window.location.href='customers-list.php';</script>";
    exit;
}
$conn->close();
?>