<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch All States from the database
    $state_list = "SELECT * FROM `states` WHERE `state_status` = '1'";
    $state_list_result = mysqli_query($conn, $state_list);
    $statename = [];
    if (mysqli_num_rows($state_list_result) > 0) {
        while ($state = mysqli_fetch_array($state_list_result)) {
            $statename[] = $state['state_name'];
        }
    }

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
    $products_list = "SELECT * FROM `products` WHERE `product_for_company`='$Company' AND `status` = '1'";
    $products_list_result = $conn->query($products_list);
    $productname = [];
    if ($products_list_result->num_rows > 0) {
        while ($ProductData = $products_list_result->fetch_assoc()) {
            $productname[] = $ProductData;
        }
    }

    // Fetch all employees from the database
    $Employee_list = "SELECT * FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_of_branch`='$Branch' AND `employee_status` = '1'";
    $Employee_list_result = $conn->query($Employee_list);
    $Employees = [];
    $Technicians = [];
    if (mysqli_num_rows($Employee_list_result) > 0) {
        while ($Employee = $Employee_list_result->fetch_assoc()) {
            if ($Employee['employee_user_role'] == 'Staff') {
                $Employees[] = $Employee;
            } elseif ($Employee['employee_user_role'] == 'Technician') {
                $Technicians[] = $Employee;
            }
        }
    }

    // Show Leads Details
    $checkLeads = "SELECT * FROM `leads` WHERE `id` = '$id'";
    $checkLeadsQuery = mysqli_query($conn, $checkLeads);
    if (mysqli_num_rows($checkLeadsQuery) > 0) {
        $lead = mysqli_fetch_assoc($checkLeadsQuery);
        $lead_id = $lead['lead_id'];
        $lead_customer_name = $lead['lead_customer_name'];
        $lead_customer_contact = trim($lead['lead_customer_contact']);
        $lead_alternate_contact = trim($lead['lead_alternate_contact']);
        $lead_for_company = $lead['lead_for_company'];
        $lead_for_branch = $lead['lead_for_branch'];
        $lead_state = trim($lead['lead_state']);
        $lead_reference = $lead['lead_reference'];
        $lead_type = $lead['lead_type'];
        $lead_customer_type = $lead['lead_customer_type'];
        $lead_product_services = $lead['lead_product_services'];
        $lead_next_followup_date = trim($lead['lead_next_followup_date']);
        $lead_next_followup_time = trim($lead['lead_next_followup_time']);
        $lead_delivery_date = trim($lead['lead_delivery_date']);
        $lead_delivery_address = trim($lead['lead_delivery_address']);
        $lead_whatsapp = trim($lead['lead_whatsapp']);
        $lead_email = trim($lead['lead_email']);
        $lead_remark = trim($lead['lead_remark']);
        $lead_assign_to = $lead['lead_assign_to'];
        $lead_status = $lead['lead_status'];
        $lead_createdby_found = $lead['lead_createdby'];
        $remarks_array = explode(', ', $lead_remark);
        $assigned_technician = trim($lead['assign_technician']);
        $assigned_technician_date = trim($lead['assign_technician_date']);

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
                                                    <input type="tel" id="lead_customer_contact" name="lead_customer_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}" value="<?php echo $lead_customer_contact; ?>" disabled>
                                                </div>
                                            </div>

                                            <!-- Person Whatsapp No. -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_whatsapp" class="form-label text-dark">
                                                        <input type="checkbox" name="sameascontact" id="sameascontact"> Whatsapp No. same as Contact
                                                    </label>
                                                    <input type="tel" id="lead_whatsapp" name="lead_whatsapp" class="form-control" placeholder="Whatsapp No." minlength="10" maxlength="10" pattern="[0-9]{10}" value="<?php echo $lead_whatsapp; ?>" disabled>
                                                </div>
                                            </div>

                                            <!-- Customer name -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_customer_name" class="form-label text-dark">Organization / Customer Name</label>
                                                    <input type="text" id="lead_customer_name" class="form-control" placeholder="Customer Name" value="<?php echo $lead_customer_name; ?>" disabled>
                                                </div>
                                            </div>

                                            <!-- Alternate Phone -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_alternate_contact" class="form-label text-dark">Alternative Phone No.</label>
                                                    <input type="tel" id="lead_alternate_contact" name="lead_alternate_contact" class="form-control" placeholder="Phone No." value="<?php echo $lead_alternate_contact; ?>" minlength="10" maxlength="10">
                                                </div>
                                            </div>

                                            <!-- Lead Type -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_type" class="form-label text-dark">Lead Type <small class="text-success">(Required)</small></label>
                                                    <select class="form-control" name="lead_type" id="lead_type" data-choices required onchange="showDeliveryDate(this.value)">
                                                        <option value="">Lead Type</option>
                                                        <option value="Hot" <?php echo ($lead_type == 'Hot') ? ' selected' : ''; ?>>1. Hot</option>
                                                        <option value="Cold" <?php echo ($lead_type == 'Cold') ? ' selected' : ''; ?>>2. Cold</option>
                                                        <option value="Warm" <?php echo ($lead_type == 'Warm') ? ' selected' : ''; ?>>3. Warm</option>
                                                        <option value="Place Order" <?php echo ($lead_type == 'Place Order') ? ' selected' : ''; ?>>4. Place Order</option>
                                                        <option value="Cancel" <?php echo ($lead_type == 'Cancel') ? ' selected' : ''; ?>>5. Cancel</option>
                                                        <option value="Completed" <?php echo ($lead_type == 'Completed') ? ' selected' : ''; ?>>6. Completed</option>
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
                                                    <input type="date" id="basic-datepicker1" name="lead_delivery_date" class="form-control" placeholder="Delivery Date" value="<?php echo $lead_delivery_date; ?>">
                                                </div>
                                            </div>

                                            <!-- Next Followup Date -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_next_followup_date" class="form-label text-dark">Next Followup Date <small class="text-success">(Required)</small></label>
                                                    <input type="date" id="basic-datepicker2" name="lead_next_followup_date" class="form-control" placeholder="Next Followup Date" value="<?php echo $lead_next_followup_date; ?>" required>
                                                </div>
                                            </div>

                                            <!-- Next Followup Time -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_next_followup_time" class="form-label text-dark">Next Followup Time <small class="text-success">(Required)</small></label>
                                                    <input type="time" id="basic-timepicker" name="lead_next_followup_time" class="form-control" placeholder="Next Followup Time" value="<?php echo $lead_next_followup_time; ?>" required>
                                                </div>
                                            </div>

                                            <!-- Lead Status -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_status" class="form-label text-dark">Lead Status <small class="text-success">(Required)</small></label>
                                                    <select class="form-control" name="lead_status" id="lead_status" data-choices required>
                                                        <option value="">Lead Status</option>
                                                        <option value="Call" <?php echo ($lead_status == 'Call') ? 'selected' : ''; ?>>1. Call</option>
                                                        <option value="Email" <?php echo ($lead_status == 'Email') ? 'selected' : ''; ?>>2. Email</option>
                                                        <option value="Whatsapp" <?php echo ($lead_status == 'Whatsapp') ? 'selected' : ''; ?>>3. Whatsapp</option>
                                                        <option value="Executive Visit" <?php echo ($lead_status == 'Executive Visit') ? 'selected' : ''; ?>>4. Executive Visit</option>
                                                        <option value="Client Visit" <?php echo ($lead_status == 'Client Visit') ? 'selected' : ''; ?>>5. Client Visit</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Lead Remarks -->
                                            <div class="col-lg-12">
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
                                                                echo "<li>" . htmlspecialchars($remark) . "</li>";
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
                                        <a href="lead-list.php" class="btn btn-primary w-100">Cancel</a>
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

       $lead_for_company = $Company;
       $lead_for_branch = $Branch;
       $lead_customer = $lead_customer_name;
       $lead_alternate_contact = $_POST['lead_alternate_contact'];
       $lead_email = $_POST['lead_email'];
       $lead_type = $_POST['lead_type'];
       $lead_status = $_POST['lead_status'];
       $lead_reference = $_POST['lead_reference'];

        // Assign Technician
        if (!empty($_POST['assign_technician']) && $_POST['lead_type'] == 'Completed') {
            $assign_technician = trim($_POST['assign_technician']);
            $assign_technician_date = date('Y-m-d');
        } else {
            $assign_technician = "";
            $assign_technician_date = '0000-00-00';
        }

        // Remarks
        $new_remark = $_POST['lead_remark'];
        $updated_remark = !empty($lead_remark) ? $lead_remark . ', ' . $new_remark : $new_remark;

        // Next Followup Date
        if ($_POST['lead_type'] == 'Cancel' || $_POST['lead_type'] == 'Completed') {
            $lead_next_followup_date = date("Y-m-d");
            $lead_next_followup_time = date("H:i");
        } else {
            $lead_next_followup_date = $_POST['lead_next_followup_date'];
            $lead_next_followup_time = $_POST['lead_next_followup_time'];
        }

        // Delivery Date
        $lead_delivery_date = ($_POST['lead_type'] == 'Completed') ? $_POST['lead_delivery_date'] : "0000-00-00";

        $lead_createdby = !empty($lead_createdby_found) ? $lead_createdby_found : $username;
        $lead_assign_to = $username;
        $lead_updatedby = $username;

        // Prepare product services
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

        // Prepare and execute the update query
        $updateLead = "UPDATE `leads` SET 
            `lead_for_company`='$lead_for_company',
            `lead_for_branch`='$lead_for_branch',
            `lead_alternate_contact`='$lead_alternate_contact',
            `lead_product_services`='$product_services_name',
            `lead_next_followup_date`='$lead_next_followup_date',
            `lead_next_followup_time`='$lead_next_followup_time',
            `lead_delivery_date`='$lead_delivery_date',
            `assign_technician`='$assign_technician',
            `assign_technician_date`='$assign_technician_date',
            `lead_status`='$lead_status',
            `lead_remark`='$updated_remark',
            `lead_type`='$lead_type',
            `lead_createdby`='$lead_createdby',
            `lead_assign_to`='$lead_assign_to',
            `lead_updatedby`='$lead_updatedby'
            WHERE `id` = '$id'";
        $ApplyUpdatedLead = mysqli_query($conn, $updateLead);

        // Execute query and handle response
        if ($ApplyUpdatedLead) {

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

            echo "<script>window.location.href='followup-list.php';</script>";
        } else {
            echo "<script>window.location.href='lead-management.php';</script>";
        }
    }
} else {
    echo "<script>window.location.href='followup-list.php';</script>";
    exit;
}
$conn->close();
?>