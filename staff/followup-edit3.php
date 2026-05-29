<?php
include("../config/db.php");
include("../config/session.php");
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

    // Fetch all references from the database
    $reference_list = "SELECT * FROM `references` WHERE `reference_for_company`='$Company' AND `reference_status` = '1'";
    $reference_list_result = mysqli_query($conn, $reference_list);
    $reference_name = [];
    if (mysqli_num_rows($reference_list_result) > 0) {
        while ($reference = mysqli_fetch_assoc($reference_list_result)) {
            $reference_name[] = $reference;
        }
    }

    // Fetch all products from the database
    $products_list = "SELECT * FROM `products` WHERE `status` = '1'";
    $products_list_result = $conn->query($products_list);
    $productname = [];
    if ($products_list_result->num_rows > 0) {
        while ($ProductData = $products_list_result->fetch_assoc()) {
            $productname[] = $ProductData;
        }
    }

    ######### Fetch technicians and staff based on selected branch #########
    $Employees = [];
    $Technicians = [];
    $Employee_list = "SELECT * FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_of_branch`='$Branch' AND `employee_user_role` IN ('Technician', 'Staff') AND `employee_status` = '1'";
    $Employee_list_result = $conn->query($Employee_list);

    if (mysqli_num_rows($Employee_list_result) > 0) {
        while ($Employee = $Employee_list_result->fetch_assoc()) {
            $EmployeeRoles = $Employee['employee_user_role'];
            if ($EmployeeRoles == 'Staff') {
                $Employees[] = $Employee;
            }
            if ($EmployeeRoles == 'Technician') {
                $Technicians[] = $Employee;
            }
        }
    }
    ######### Fetch technicians and staff based on selected branch #########


    // Show Leads Details
    $checkLeads = "SELECT * FROM `importleads` WHERE `id` = '$id'";
    $checkLeadsQuery = mysqli_query($conn, $checkLeads);
    if (mysqli_num_rows($checkLeadsQuery) > 0) {
        $leadinfo = mysqli_fetch_assoc($checkLeadsQuery);
        $leadinfo_customer_name = $leadinfo['lead_customer_name'];
        $leadinfo_customer_contact = trim($leadinfo['lead_customer_contact']);
        $leadinfo_alternate_contact = trim($leadinfo['lead_alternate_contact']);
        $leadinfo_for_the_company = $leadinfo['lead_for_company'];
        $leadinfo_for_the_branch = $leadinfo['lead_for_branch'];
        $leadinfo_type = $leadinfo['lead_type'];
        $leadinfo_customer_type = $leadinfo['lead_customer_type'];
        $leadinfo_product_services = $leadinfo['lead_product_services'];
        $leadinfo_followup_date = trim($leadinfo['lead_followup_date']);
        $leadinfo_next_followup_date = trim($leadinfo['lead_next_followup_date']);
        $leadinfo_next_followup_time = trim($leadinfo['lead_next_followup_time']);
        $leadinfo_delivery_date = trim($leadinfo['lead_delivery_date']);
        $leadinfo_delivery_address = trim($leadinfo['lead_delivery_address']);
        $leadinfo_whatsapp = trim($leadinfo['lead_whatsapp']);
        $leadinfo_email = trim($leadinfo['lead_email']);
        $leadinfo_remark = trim($leadinfo['lead_remark']);
        $leadinfo_assign_to = $leadinfo['lead_assign_to'];
        $leadinfo_status = $leadinfo['lead_status'];
        $leadinfo_createdby_found = $leadinfo['lead_createdby'];
        // All remarks
        $remarks_array = explode(', ',  $leadinfo_remark);
        // Assign Technician
        $assigned_technician = trim($leadinfo['assign_technician']);
        $assigned_technician_date = trim($leadinfo['assign_technician_date']);

        // Fetch existing specifications
        $service_lead = !empty($lead_product_services) ? explode(', ', $lead_product_services) : [];
        $myServiceLead = [];
        foreach ($service_lead as $leadService) {
            $myServiceLead[] = [
                'leadService' => htmlspecialchars($leadService),
            ];
        }
    }
?>

    <!DOCTYPE html>
    <html lang="en-US">

    <head>
        <title>Manage Followup</title>
        <?php include("head.php"); ?>
        <style>
            #services-container .choices {
                width: 97%;
            }
        </style>
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
            <div class="page-content">

                <!-- Start Container Fluid -->
                <div class="container-xxl">
                    <form action="#" method="POST" enctype="multipart/form-data" id="remarkForm">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Basic Details</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Customer Contact -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_customer_contact" class="form-label text-dark">Contact Number</label>
                                                    <input type="tel" id="lead_customer_contact" name="lead_customer_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}" value="<?php echo $leadinfo_customer_contact; ?>">
                                                </div>
                                            </div>

                                            <!-- Person Whatsapp No. -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_whatsapp" class="form-label text-dark">
                                                        <input type="checkbox" name="sameascontact" id="sameascontact"> Whatsapp No. same as Contact
                                                    </label>
                                                    <input type="tel" id="lead_whatsapp" name="lead_whatsapp" class="form-control" placeholder="Whatsapp No." minlength="10" maxlength="10" pattern="[0-9]{10}" value="<?php echo $leadinfo_whatsapp; ?>">
                                                </div>
                                            </div>

                                            <!-- Customer name -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_customer_name" class="form-label text-dark">Organization / Customer Name</label>
                                                    <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Customer Name" value="<?php echo $leadinfo_customer_name; ?>">
                                                </div>
                                            </div>

                                            <!-- Alternate Phone -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_alternate_contact" class="form-label text-dark">Alternative Phone No.</label>
                                                    <input type="tel" id="lead_alternate_contact" name="lead_alternate_contact" class="form-control" placeholder="Phone No." value="<?php echo $leadinfo_alternate_contact; ?>" minlength="10" maxlength="10">
                                                </div>
                                            </div>

                                            <!-- State List -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_state" class="form-label text-dark">Select State </label>
                                                    <select name="lead_state" id="lead_state" class="form-control" data-choices>
                                                        <option value="">Select State</option>
                                                        <?php foreach ($statename as $states) { ?>
                                                            <option value="<?php echo $states['state_name']; ?>"><?php echo $states['state_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Lead Type -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_type" class="form-label text-dark">Lead Type <small class="text-success">(Required)</small></label>
                                                    <select class="form-control" name="lead_type" id="lead_type" data-choices required onchange="showDeliveryDate(this.value)">
                                                        <option value="">Lead Type</option>
                                                        <option value="Hot" <?php echo ($leadinfo_type == 'Hot') ? ' selected' : ''; ?>>1. Hot</option>
                                                        <option value="Cold" <?php echo ($leadinfo_type == 'Cold') ? ' selected' : ''; ?>>2. Cold</option>
                                                        <option value="Warm" <?php echo ($leadinfo_type == 'Warm') ? ' selected' : ''; ?>>3. Warm</option>
                                                        <option value="Place Order" <?php echo ($leadinfo_type == 'Place Order') ? ' selected' : ''; ?>>4. Place Order</option>
                                                        <option value="Cancel" <?php echo ($leadinfo_type == 'Cancel') ? ' selected' : ''; ?>>5. Cancel</option>
                                                        <option value="Completed" <?php echo ($leadinfo_type == 'Completed') ? ' selected' : ''; ?>>6. Completed</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Technician Dropdown -->
                                            <div class="col-lg-3" id="AssignTechnician" style="display: none;">
                                                <div class="mb-3">
                                                    <label for="assign_technician" class="form-label text-dark">Technician Assign</label>
                                                    <select name="assign_technician" id="assign_technician" class="form-control" data-choices>
                                                        <option value="">Select Technician</option>
                                                        <?php foreach ($Technicians as $Technassign) { ?>
                                                            <option value="<?php echo $Technassign['employee_username']; ?>" <?php echo ($Technassign['employee_name'] == $assigned_technician) ? 'selected' : ''; ?>>
                                                                <?php echo $Technassign['employee_name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>


                                            <!-- Delivery Date -->
                                            <div id="deliveryDate" class="col-lg-3" style="display: none;">
                                                <div class="mb-3">
                                                    <label for="lead_delivery_date" class="form-label text-dark">Delivery Date <small class="text-success">(Required)</small></label>
                                                    <input type="date" id="basic-datepicker1" name="lead_delivery_date" class="form-control" placeholder="Delivery Date" value="<?php echo $leadinfo_delivery_date; ?>">
                                                </div>
                                            </div>

                                            <!-- Next Followup Date -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_next_followup_date" class="form-label text-dark">Next Followup Date <small class="text-success">(Required)</small></label>
                                                    <input type="date" id="basic-datepicker2" name="lead_next_followup_date" class="form-control" placeholder="Next Followup Date" value="<?php echo $leadinfo_next_followup_date; ?>" required>
                                                </div>
                                            </div>

                                            <!-- Next Followup Time -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_next_followup_time" class="form-label text-dark">Next Followup Time <small class="text-success">(Required)</small></label>
                                                    <input type="time" id="basic-timepicker" name="lead_next_followup_time" class="form-control" placeholder="Next Followup Time" value="<?php echo $leadinfo_next_followup_time; ?>" required>
                                                </div>
                                            </div>

                                            <!-- Lead Status -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_status" class="form-label text-dark">Lead Status <small class="text-success">(Required)</small></label>
                                                    <select class="form-control" name="lead_status" id="lead_status" data-choices required>
                                                        <option value="">Lead Status</option>
                                                        <option value="Call" <?php echo ($leadinfo_status == 'Call') ? 'selected' : ''; ?>>1. Call</option>
                                                        <option value="Email" <?php echo ($leadinfo_status == 'Email') ? 'selected' : ''; ?>>2. Email</option>
                                                        <option value="Whatsapp" <?php echo ($leadinfo_status == 'Whatsapp') ? 'selected' : ''; ?>>3. Whatsapp</option>
                                                        <option value="Executive Visit" <?php echo ($leadinfo_status == 'Executive Visit') ? 'selected' : ''; ?>>4. Executive Visit</option>
                                                        <option value="Client Visit" <?php echo ($leadinfo_status == 'Client Visit') ? 'selected' : ''; ?>>5. Client Visit</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Lead Remarks -->
                                            <div class="col-lg-3">
                                                <div class="mb-2">
                                                    <label for="lead_delivery_address" class="form-label text-dark">Address</label>
                                                    <textarea id="lead_delivery_address" class="form-control" name="lead_delivery_address" rows="2" placeholder="Address"><?php echo $leadinfo_delivery_address; ?></textarea>
                                                </div>
                                            </div>

                                            <!-- Lead Remarks -->
                                            <div class="col-lg-3">
                                                <div class="mb-2">
                                                    <label for="lead_remark" class="form-label text-dark">Remarks</label>
                                                    <textarea id="lead_remark" class="form-control" name="lead_remark" rows="2" placeholder="Enter your remark here..."></textarea>
                                                </div>
                                            </div>

                                            <!-- Remarks list -->
                                            <div class="col-lg-6">
                                                <div class="mb-2">
                                                    <h4>All Remarks</h4>
                                                    <ul style="list-style-type: number">
                                                        <?php
                                                        if (!empty($remarks_array)) {
                                                            foreach ($remarks_array as $remark) {
                                                                echo "<li>" . $remark . "</li>";
                                                            }
                                                        } else {
                                                            echo "<li>No remarks available.</li>";
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Select Product Service</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="services-container">
                                                    <?php
                                                    foreach ($myServiceLead as $key => $SearviceLead) {
                                                        echo '<div class="col-lg-12">
                                                    <div class="input-group mb-1">
                                                    <select id="lead_product_services' . $key . '" name="lead_product_services[]" class="form-control" data-choices>
                                                        <option value="">Select Product</option>';
                                                        foreach ($productname as $product) {
                                                            $selected = ($product["product_name"] == $SearviceLead['leadService']) ? 'selected' : '';
                                                            echo '<option value="' . $product["product_name"] . '" ' . $selected . '>' . $product["product_name"] . '</option>';
                                                        }
                                                        echo '</select>
                                                        <span class="remove-field mx-2 remove-feild border"> - </span>
                                                    </div>
                                                    </div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div id="add-btn" class="btn add-field border-1 border-dark-subtle">Add Product Services</div>
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
                                        <a href="new-data.php" class="btn btn-primary w-100">Cancel</a>
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

        <!-- datepicker js -->
        <script>
            document.getElementById('basic-datepicker1').flatpickr();
            document.getElementById('basic-datepicker2').flatpickr();
            document.getElementById('basic-timepicker').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i"
            });
        </script>

        <!-- next followup date/time and Delivery date -->
        <script>
            function showDeliveryDate(leadType) {
                const nextFollowupDate = document.getElementById("basic-datepicker2");
                const nextFollowupTime = document.getElementById("basic-timepicker");
                const deliveryDate = document.getElementById("deliveryDate");
                const AssignTechnician = document.getElementById("AssignTechnician");

                if (leadType === "Cancel" || leadType === "Completed") {
                    nextFollowupDate.disabled = true;
                    nextFollowupTime.disabled = true;

                    if (leadType === "Completed") {
                        deliveryDate.style.display = "block";
                    } else {
                        deliveryDate.style.display = "none";
                    }
                } else {
                    nextFollowupDate.disabled = false;
                    nextFollowupTime.disabled = false;
                    deliveryDate.style.display = "none";
                }

                AssignTechnician.style.display = (leadType === "Completed") ? "block" : "none";
            }

            window.onload = function() {
                const leadType = document.getElementById("lead_type").value;
                showDeliveryDate(leadType);
            };
        </script>

        <script>
            const addBtn = document.getElementById('add-btn');
            const servicesContainer = document.getElementById('services-container');
            let servicesCount = <?php echo count($myServiceLead); ?>;

            function createServicesDiv() {
                const newDiv = document.createElement('div');
                newDiv.className = 'col-lg-12';
                newDiv.innerHTML = `
            <div class="input-group mb-1">
                <select id="lead_product_services${servicesCount}" name="lead_product_services[]" class="form-control" data-choices>
                    <option value="">Select Product</option>
                    <?php foreach ($productname as $product) { ?>
                        <option value="<?php echo $product['product_name']; ?>"><?php echo $product['product_name']; ?></option>
                    <?php } ?>
                </select>
                <span class="remove-field remove-feild border"> - </span>
            </div>`;
                return newDiv;
            }

            addBtn.addEventListener('click', () => {
                const newDiv = createServicesDiv();
                servicesContainer.appendChild(newDiv);

                const newSelect = newDiv.querySelector('[data-choices]');
                if (newSelect) {
                    new Choices(newSelect);
                }

                servicesCount++;
            });

            servicesContainer.addEventListener('click', (event) => {
                if (event.target.classList.contains('remove-feild')) {
                    const divToRemove = event.target.closest('.col-lg-12');
                    divToRemove.remove();
                    servicesCount--;
                }
            });
        </script>

        <script>
            var checkbox = document.getElementById('sameascontact');
            var contactNumberInput = document.getElementById('lead_customer_contact');
            var whatsappNumberInput = document.getElementById('lead_whatsapp');

            checkbox.addEventListener('change', function() {
                whatsappNumberInput.value = this.checked ? contactNumberInput.value : '';
            });
        </script>

        <script>
            var selectElement = document.getElementById('assign_technician');
            var assignedTechnician = '<?php echo $assigned_technician; ?>';
            for (var i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value == assignedTechnician) {
                    selectElement.options[i].selected = true;
                    break;
                }
            }
        </script>

    </body>

    </html>

<?php
    // Update Lead
    if (isset($_POST['update_lead'])) {
        ########### Generate Unique ID #############
        function generateId()
        {
            $prefix = '#ID';
            $randomNumber = rand(1, 9999999);
            return $prefix . $randomNumber;
        }
        $uniqueId = generateId();
        $lead_id = $uniqueId;
        ########### Generate Unique ID #############


        ############### Get All Data of the page ###############
        $lead_for_company = $leadinfo_for_the_company;
        $lead_for_branch = $leadinfo_for_the_branch;
        $lead_customer_name = $_POST['lead_customer_name'];
        $lead_customer_contact = $_POST['lead_customer_contact'];
        $lead_alternate_contact = $_POST['lead_alternate_contact'];
        $lead_customer_type = $leadinfo_customer_type;
        $lead_email = $leadinfo_email;
        $lead_whatsapp = $_POST['lead_whatsapp'];
        $lead_state = $_POST['lead_state'];
        $lead_delivery_address = $_POST['lead_delivery_address'];
        $lead_type = $_POST['lead_type'];
        $lead_followup_date = $leadinfo_followup_date;
        $lead_status = $_POST['lead_status'];
        $lead_remark = $_POST['lead_remark'];
        $lead_customer_status = 1;


        // Next Followup Date
        if ($_POST['lead_type'] == 'Cancel' || $_POST['lead_type'] == 'Completed') {
            $lead_next_followup_date = date("Y-m-d");
            $lead_next_followup_time = date("H:i");
        } else {
            $lead_next_followup_date = $_POST['lead_next_followup_date'];
            $lead_next_followup_time = $_POST['lead_next_followup_time'];
        }

        // Delivery Date
        if ($_POST['lead_type'] == 'Completed') {
            $lead_delivery_date = $_POST['lead_delivery_date'];
        } elseif ($_POST['lead_type'] == 'Cancel') {
            $lead_delivery_date = "0000-00-00";
        } else {
            $lead_delivery_date = "0000-00-00";
        }

        // Assign To
        $lead_createdby = $leadinfo_createdby_found;
        $lead_assign_to = $username;
        $lead_updatedby = $username;


        #################### Start Specifications #####################
        $servicespro = [];
        if (isset($_POST['lead_product_services'])) {
            foreach ($_POST['lead_product_services'] as $service) {
                $service = trim($service);
                if (!empty($service)) {
                    $servicespro[] = $service;
                }
            }
        }
        $product_services_name = implode(', ', $servicespro);
        // #################### End Specifications ######################



        ############# troubleshooting #############
        // echo $lead_for_company . "<br>";
        // echo $lead_for_branch . "<br>";
        // echo $lead_customer_name . "<br>";
        // echo $lead_customer_contact . "<br>";
        // echo $lead_alternate_contact . "<br>";
        // echo $lead_customer_type . "<br>";
        // echo $lead_email . "<br>";
        // echo $lead_whatsapp . "<br>";
        // echo $lead_state . "<br>";
        // echo $lead_delivery_address . "<br>";
        // echo $lead_type . "<br>";
        // echo $lead_followup_date . "<br>";
        // echo $lead_status . "<br>";
        // echo $lead_remark . "<br>";
        // echo $lead_customer_status . "<br>";
        // echo $lead_next_followup_date . "<br>";
        // echo $lead_next_followup_time . "<br>";
        // echo $lead_delivery_date . "<br>";
        // echo $lead_createdby . "<br>";
        // echo $lead_assign_to . "<br>";
        // echo $lead_updatedby . "<br>";
        // echo $product_services_name . "<br>";
        ############# troubleshooting #############




        ###################### Insert Lead ######################
        $addLead = "INSERT INTO `leads` (`lead_id`, `lead_customer_name`, `lead_customer_contact`, `lead_alternate_contact`, `lead_for_company`, `lead_for_branch`, `lead_product_services`, `lead_state`, `lead_delivery_address`, `lead_email`, `lead_followup_date`, `lead_next_followup_date`, `lead_next_followup_time`, `lead_delivery_date`, `lead_whatsapp`, `lead_assign_to`, `lead_status`, `lead_remark`, `lead_createdby`, `lead_updatedby`, `lead_type`, `lead_customer_type`, `lead_customer_status`) VALUES ('$lead_id','$lead_customer_name','$lead_customer_contact','$lead_alternate_contact','$lead_for_company','$lead_for_branch','$product_services_name', '$lead_state','$lead_delivery_address','$lead_email','$lead_followup_date','$lead_next_followup_date','$lead_next_followup_time','$lead_delivery_date','$lead_whatsapp','$lead_assign_to','$lead_status','$lead_remark','$lead_createdby','$lead_updatedby','$lead_type','$lead_customer_type','$lead_customer_status')";

        $leadUpdates = "UPDATE `importleads` SET `lead_createdby`='$lead_createdby', `lead_assign_to`='$lead_assign_to' WHERE `id`= '$id'";

        // Execute the insert query
        if (mysqli_query($conn, $addLead)) {

           ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "Followups of customer " . $lead_customer . " has been followed up by staff " . $username . " of " . $lead_for_branch . " branch.";
            $activity_type = 'Alert';
            $activity_company = $Company;
            $activity_branch = $Branch;
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################

            // If insert is successful, execute the update query
            if (mysqli_query($conn, $leadUpdates)) {
                echo "<script>window.location.href='lead-upload.php';</script>";
            } else {
                // Debugging output for the update query
                echo "Error updating lead: " . mysqli_error($conn) . "<br>";
                echo "Update Query: " . $leadUpdates; // Show the query for debugging
            }
        } else {
            // If insert fails, show the error
            echo "Error inserting lead: " . mysqli_error($conn) . "<br>";
            echo "Insert Query: " . $addLead; // Show the query for debugging
        }
        ###################### Insert Lead ######################
    }
} else {
    echo "<script>window.location.href='lead-upload.php';</script>";
    exit;
}
$conn->close();
?>