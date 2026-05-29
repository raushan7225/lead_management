<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    ################### Show Leads Details ########################
    $checkLeads = "SELECT * FROM `leads` WHERE `lead_customer_status` = '1' AND `id` = '$id'";
    $checkLeadsQuery = mysqli_query($conn, $checkLeads);
    if (mysqli_num_rows($checkLeadsQuery) > 0) {
        while ($lead = mysqli_fetch_assoc($checkLeadsQuery)) {
            $lead_customer_name = $lead['lead_customer_name'];
            $lead_customer_contact = $lead['lead_customer_contact'];
            $lead_for_company = $lead['lead_for_company'];
            $lead_for_branch = $lead['lead_for_branch'];
            $lead_delivery_date = $lead['lead_delivery_date'];
            $assign_technician = $lead['assign_technician'];
            $lead_delivery_subject = $lead['lead_delivery_subject'];
            $lead_delivery_address = $lead['lead_delivery_address'];
            $lead_remark = $lead['lead_remark'];
            $lead_sub_total = $lead['lead_sub_total'];
            $lead_other_charges = $lead['lead_other_charges'];
            $lead_grand_total = $lead['lead_grand_total'];

            $assigned_technician = trim($lead['assign_technician']);
            $assigned_technician_date = trim($lead['assign_technician_date']);

            // Fetch existing specifications 
            $lead_product_services = !empty($lead['lead_product_services']) ? explode(', ', $lead['lead_product_services']) : [];
            $lead_product_regular_price = !empty($lead['lead_product_regular_price']) ? explode(', ', $lead['lead_product_regular_price']) : [];
            $lead_product_quantity = !empty($lead['lead_product_quantity']) ? explode(', ', $lead['lead_product_quantity']) : [];
            $lead_product_offer_price = !empty($lead['lead_product_offer_price']) ? explode(', ', $lead['lead_product_offer_price']) : [];
            $lead_product_remark = !empty($lead['lead_product_remark']) ? explode(', ', $lead['lead_product_remark']) : [];


            // Combine the specifications into an array of rows
            $myServiceLead = [];
            foreach ($lead_product_services as $key => $leadService) {
                $myServiceLead[] = [
                    'leadService' => htmlspecialchars($leadService),
                    'lead_product_regular_price' => isset($lead_product_regular_price[$key]) ? htmlspecialchars($lead_product_regular_price[$key]) : '',
                    'lead_product_quantity' => isset($lead_product_quantity[$key]) ? htmlspecialchars($lead_product_quantity[$key]) : '',
                    'lead_product_offer_price' => isset($lead_product_offer_price[$key]) ? htmlspecialchars($lead_product_offer_price[$key]) : '',
                    'lead_product_remark' => isset($lead_product_remark[$key]) ? htmlspecialchars($lead_product_remark[$key]) : '',
                ];
            }


            ######### Fetch all products from the database #########
            $products_list = "SELECT * FROM `products` WHERE `product_for_company`='$lead_for_company' AND `status` = '1'";
            $products_list_result = $conn->query($products_list);
            $productname = [];
            if ($products_list_result->num_rows > 0) {
                while ($ProductData = $products_list_result->fetch_assoc()) {
                    $productname[] = $ProductData;
                }
            }
            ######### Fetch all products from the database #########

            ######### Fetch technicians and staff based on selected branch #########
            $Employees = [];
            $Technicians = [];
            $Employee_list = "SELECT * FROM `employees` WHERE `employee_of_company`='$lead_for_company' AND `employee_of_branch`='$lead_for_branch' AND `employee_status` = '1'";
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
        }
    }
    ################### Show Leads Details ########################


    ######### Fetch all Technician from the database #########
    $Employee_list = "SELECT * FROM `employees` WHERE `employee_of_company`='$lead_for_company' AND `employee_of_branch`='$lead_for_branch' AND `employee_user_role` = 'Technician' AND `employee_status` = '1'";
    $Employee_list_result = $conn->query($Employee_list);
    $Employeename = [];
    if (mysqli_num_rows($Employee_list_result) > 0) {
        while ($Employee = $Employee_list_result->fetch_assoc()) {
            $Employeename[] = $Employee;
        }
    }
    ######### Fetch all employees from the database #########
?>

    <!DOCTYPE html>
    <html lang="en-US">

    <head>
        <title>Manager Followup</title>
        <?php include("head.php"); ?>
        <style>
            #services-container .choices {
                width: 100%;
                max-width: 400px;
            }

            @media screen and (max-width: 992px) {

                #services-container,
                #services-container .choices {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .resarea {
                    width: 100% !important;
                    max-width: 100% !important;
                    display: grid !important;
                    grid-template-rows: 5 !important;
                }

                .resarea input:nth-child(2),
                .resarea input:nth-child(3),
                .resarea input:nth-child(4),
                .resarea input:nth-child(5),
                .resarea input:last-child {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .productremarks {
                    display: block !important;
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .remove-field {
                    margin-bottom: 10px;
                }
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
                    <form action="#" method="POST" enctype="multipart/form-data" onsubmit="return submitForm()" onsubmit="calculateTotals()" oninput="calculateTotals()">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Followup Status</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <!--  Customer name -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_customer_name" class="form-label text-dark"> Organization / Customer Name </label>
                                                    <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Customer Name" value="<?php echo $lead_customer_name; ?>">
                                                </div>
                                            </div>

                                            <!-- Customer Contact -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_customer_contact" class="form-label text-dark"> Contact Number </label>
                                                    <input type="tel" id="lead_customer_contact" name="lead_customer_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}" value="<?php echo $lead_customer_contact; ?>">
                                                </div>
                                            </div>


                                            <!--Delivery Date -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="lead_delivery_date" class="form-label text-dark">Delivery Date</label>
                                                    <input type="date" id="lead_delivery_date" name="lead_delivery_date" class="form-control" placeholder="Next Followup Date" value="<?php echo $lead_delivery_date; ?>">
                                                </div>
                                            </div>


                                            <!-- Technician Dropdown -->
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="assign_technician" class="form-label text-dark">Technician Assign</label>
                                                    <select name="assign_technician" id="assign_technician" class="form-control" data-choices>
                                                        <option value="">Select Technician</option>
                                                        <?php foreach ($Technicians as $Technassign) { ?>
                                                            <option value="<?php echo $Technassign['employee_name']; ?>" <?php echo ($Technassign['employee_name'] == $assigned_technician) ? 'selected' : ''; ?>>
                                                                <?php echo $Technassign['employee_name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Delivery Address -->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="lead_delivery_address" class="form-label text-dark"> Delivery Address </label>
                                                    <textarea type="text" id="lead_delivery_address" name="lead_delivery_address" rows="3" class="form-control" placeholder="Delivery Address"><?php echo $lead_delivery_address; ?></textarea>
                                                </div>
                                            </div>

                                            <!-- lead Subject -->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="lead_delivery_subject" class="form-label text-dark"> Additional Note </label>
                                                    <textarea type="text" id="lead_delivery_subject" name="lead_delivery_subject" rows="3" class="form-control" placeholder="Additional Note"><?php echo $lead_delivery_subject; ?></textarea>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <!-- =================== Services ================== -->
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Calculate Product Price</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="services-container">
                                                    <?php
                                                    foreach ($myServiceLead as $key => $serviceLead) {
                                                        echo '
                                                        <div class="input-group mb-1 resarea product-row">
                                                            <select name="lead_product_services[]" class="form-control" data-choices>
                                                                <option value="">Select Service</option>';
                                                        foreach ($productname as $product) {
                                                            $selected = ($product["product_name"] == $serviceLead['leadService']) ? 'selected' : '';
                                                            echo '<option value="' . $product["product_name"] . '" ' . $selected . '>' . $product["product_name"] . '</option>';
                                                        }
                                                        echo '</select>
                                                        <input type="text" name="lead_product_regular_price[]" class="form-control" placeholder="Price per unit" style="max-width: 150px; height: 38px;" value="' . $product["product_price"] . '">
                                                        <input type="text" name="lead_product_quantity[]" class="form-control lead_product_quantity" placeholder="Quantity" style="max-width: 150px; height: 38px;" value="' . $serviceLead['lead_product_quantity'] . '">
                                                        <input type="text" step="0.01" name="lead_product_offer_price[]" class="form-control lead_product_offer_price" placeholder="Offer Price" style="max-width: 150px; height: 38px;" value="' . $serviceLead['lead_product_offer_price'] . '">
                                                        <input type="hidden" name="lead_product_price_total[]" class="form-control lead_product_price_total" placeholder="Row Total (Auto)" readonly>
                                                        <input type="text" name="lead_product_remark[]" class="productremarks form-control" style="height: 38px;" placeholder="Remarks" value="' . $serviceLead['lead_product_remark'] . '">
                                                        <span class="remove-field remove-feild border"> - </span>
                                                    </div>';
                                                    }
                                                    ?>
                                                </div>

                                            </div>

                                            <!-- Add Button -->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <div id="add-btn" class="btn add-field border-1 border-dark-subtle">Add Services</div>
                                                </div>
                                            </div>

                                            <!-- Subtotal -->
                                            <div class="col-lg-2">
                                                <div class="mb-3">
                                                    <label for="lead_sub_total" class="form-label text-dark">Sub Total</label>
                                                    <input type="text" id="lead_sub_total" name="lead_sub_total" class="form-control" placeholder="Sub Total" value="<?php echo $lead_sub_total; ?>" readonly>
                                                </div>
                                            </div>

                                            <!-- Other Charges -->
                                            <div class="col-lg-2">
                                                <div class="mb-3">
                                                    <label for="lead_other_charges" class="form-label text-dark">Other Charges</label>
                                                    <input type="text" step="0.01" id="lead_other_charges" name="lead_other_charges" class="form-control" placeholder="Other Charges" value="<?php echo $lead_other_charges; ?>">
                                                </div>
                                            </div>

                                            <!-- Grand Total -->
                                            <div class="col-lg-2">
                                                <div class="mb-3">
                                                    <label for="lead_grand_total" class="form-label text-dark">Grand Total</label>
                                                    <input type="text" id="lead_grand_total" name="lead_grand_total" class="form-control" placeholder="Grand Total" value="<?php echo $lead_grand_total; ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- =========x========== Services =========x========= -->
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


        <script>
            document.getElementById('lead_delivery_date').flatpickr();
        </script>

        <script>
            // Function to show or hide the delivery date field
            function showDeliveryDate(value) {
                if (value == 'Place Order') {
                    document.getElementById('delivery-date-field').style.display = 'block';
                    document.getElementById('lead_next_followup_date').disabled = true;
                    document.getElementById('lead_next_followup_time').disabled = true;
                } else {
                    document.getElementById('delivery-date-field').style.display = 'none';
                    document.getElementById('lead_next_followup_date').disabled = false;
                    document.getElementById('lead_next_followup_time').disabled = false;
                }
            }

            if ('<?php echo $lead_type; ?>' == 'Place Order') {
                document.getElementById('delivery-date-field').style.display = 'block';
            }
        </script>



        <script>
            // Get the add button and the container
            const addBtn = document.getElementById('add-btn');
            const servicesContainer = document.getElementById('services-container');
            let servicesCount = <?php echo count($myServiceLead); ?>; // Start counting from the existing services

            // Function to create a new service row
            function createServicesDiv() {
                const newDiv = document.createElement('div');
                newDiv.className = 'input-group mb-3 resarea product-row';
                newDiv.innerHTML = `
            <select name="lead_product_services[]" class="form-control" data-choices>
                <option value="">Select Service</option>
                <?php foreach ($productname as $product) { ?>
                    <option value="<?php echo $product['product_name']; ?>"><?php echo $product['product_name']; ?></option>
                <?php } ?>
            </select>

            <input type="text" name="lead_product_regular_price[]" class="form-control" placeholder="Price per unit" style="max-width: 150px; height: 38px;">
            <input type="text" name="lead_product_quantity[]" class="form-control lead_product_quantity" placeholder="Quantity" style="max-width: 150px; height: 38px;">
            <input type="text" step="0.01" name="lead_product_offer_price[]" class="form-control lead_product_offer_price" placeholder="Offer Price" style="max-width: 150px; height: 38px;">
            <input type="hidden" name="lead_product_price_total[]" class="form-control lead_product_price_total" placeholder="Row Total (Auto)" readonly>
            <input type="text" name="lead_product_remark[]" class="form-control" style="height: 38px;" placeholder="Remarks">
            <span class="remove-field remove-feild border"> - </span>
        `;
                return newDiv;
            }

            // Add event listener to the add button
            addBtn.addEventListener('click', () => {
                const newDiv = createServicesDiv();
                servicesContainer.appendChild(newDiv);

                // Initialize Choices.js for the newly added select
                const newSelect = newDiv.querySelector('[data-choices]');
                if (newSelect) {
                    new Choices(newSelect);
                }

                servicesCount++;
            });

            // Remove event for dynamically created rows
            servicesContainer.addEventListener('click', (event) => {
                if (event.target.classList.contains('remove-feild')) {
                    event.target.parentNode.remove();
                }
            });

            // Function to calculate totals
            function calculateTotals() {
                let subTotal = 0;

                // Get all product rows
                const rows = document.querySelectorAll('.product-row');

                rows.forEach(row => {
                    const quantity = parseFloat(row.querySelector('.lead_product_quantity').value) || 0;
                    const offerPrice = parseFloat(row.querySelector('.lead_product_offer_price').value) || 0;

                    // Calculate row total
                    const rowTotal = quantity * offerPrice;

                    // Update row total field
                    row.querySelector('.lead_product_price_total').value = rowTotal.toFixed(2);

                    // Add to subtotal
                    subTotal += rowTotal;
                });

                // Update subtotal
                document.getElementById('lead_sub_total').value = subTotal.toFixed(2);

                // Calculate and update grand total
                const otherCharges = parseFloat(document.getElementById('lead_other_charges').value) || 0;
                const grandTotal = subTotal + otherCharges;
                document.getElementById('lead_grand_total').value = grandTotal.toFixed(2);
            }

            // Attach input listener for dynamic calculations
            document.addEventListener('input', calculateTotals);
        </script>


    </body>

    </html>

<?php
    ######################### Update Lead ########################
    if (isset($_POST['update_lead'])) {
        $lead_customer_name = trim($_POST['lead_customer_name']);
        $lead_customer_contact = trim($_POST['lead_customer_contact']);
        $lead_delivery_date = trim($_POST['lead_delivery_date']);
        $assign_technician = trim($_POST['assign_technician']);
        $lead_delivery_subject = trim($_POST['lead_delivery_subject']);
        $lead_delivery_address = trim($_POST['lead_delivery_address']);
        $lead_sub_total = trim($_POST['lead_sub_total']);
        $lead_other_charges = trim($_POST['lead_other_charges']);
        $lead_grand_total = trim($_POST['lead_grand_total']);

        #################### Start Specifications #####################
        $servicespro = [];
        $regular_prices = [];
        $quantities = [];
        $offer_prices = [];
        $remarks = [];

        // Validate and process each field group
        if (isset($_POST['lead_product_services'])) {
            foreach ($_POST['lead_product_services'] as $key => $service) {
                $service = trim($service);
                $regular_price = isset($_POST['lead_product_regular_price'][$key]) ? trim($_POST['lead_product_regular_price'][$key]) : '';
                $quantity = isset($_POST['lead_product_quantity'][$key]) ? trim($_POST['lead_product_quantity'][$key]) : '';
                $offer_price = isset($_POST['lead_product_offer_price'][$key]) ? trim($_POST['lead_product_offer_price'][$key]) : '';
                $remark = isset($_POST['lead_product_remark'][$key]) ? trim($_POST['lead_product_remark'][$key]) : '';

                // Ensure at least the service is not empty to consider this row
                if (!empty($service)) {
                    $servicespro[] = $service;
                    $regular_prices[] = $regular_price;
                    $quantities[] = $quantity;
                    $offer_prices[] = $offer_price;
                    $remarks[] = $remark;
                }
            }
        }

        // Consolidate the data into comma-separated strings for storage
        $product_services_name = implode(', ', $servicespro);
        $product_regular_prices = implode(', ', $regular_prices);
        $product_quantities = implode(', ', $quantities);
        $product_offer_prices = implode(', ', $offer_prices);
        $product_remarks = implode(', ', $remarks);
        #################### End Specifications #####################


        #################### Troubleshooting #####################
        // echo $lead_customer_name . "<br>";
        // echo $lead_customer_contact . "<br>";
        // echo $lead_for_company . "<br>";
        // echo $lead_for_branch . "<br>";
        // echo $lead_delivery_date . "<br>";
        // echo $assign_technician . "<br>";
        // echo $lead_delivery_subject . "<br>";
        // echo $lead_delivery_address . "<br>";
        // echo $product_services_name . "<br>";
        // echo $product_regular_prices . "<br>";
        // echo $product_quantities . "<br>";
        // echo $product_offer_prices . "<br>";
        // echo $product_remarks . "<br>";
        // echo $lead_sub_total . "<br>";
        // echo $lead_other_charges . "<br>";
        // echo $lead_grand_total;
        #################### Troubleshooting #####################


        // Prepare and execute the update query
        $updateLead = "UPDATE `leads` SET 
        `lead_customer_name` = '$lead_customer_name', 
        `lead_customer_contact` = '$lead_customer_contact', 
        `lead_delivery_date`='$lead_delivery_date', 
        `assign_technician`='$assign_technician', 
        `lead_delivery_address`='$lead_delivery_address',
        `lead_delivery_subject`='$lead_delivery_subject',
        `lead_product_services`='$product_services_name',
        `lead_product_regular_price`='$product_regular_prices',
        `lead_product_quantity`='$product_quantities',
        `lead_product_offer_price`='$product_offer_prices',
        `lead_sub_total`='$lead_sub_total',
        `lead_other_charges`='$lead_other_charges',
        `lead_grand_total`='$lead_grand_total',
        `lead_product_remark`='$product_remarks',
        `lead_updatedby`='$username' WHERE `id` = '$id'";
        $updateLeadQuery = mysqli_query($conn, $updateLead);


        //  Execute query and handle response
        if ($updateLeadQuery) {
            ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "Quotation of the customer " . $lead_customer_name . " has been updated by " . $Role . ".";
            $activity_type = 'Alert';
            $activity_company = "";
            $activity_branch = "";
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################
            echo "<script>window.location.href='lead-list.php';</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
    ######################## Updated Lead #######################
} else {
    echo "<script>window.location.href='lead-list.php';</script>";
    exit;
}
$conn->close();
?>