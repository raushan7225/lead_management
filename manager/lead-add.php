<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");



######### Fetch all branches from the database #########
$branch_list = "SELECT * FROM `branches` WHERE `branch_of_company`='$Company' AND `branch_name` = '$Branch' AND `branch_status` = '1'";
$branch_list_result = $conn->query($branch_list);
$branchename = [];
if ($branch_list_result->num_rows > 0) {
     while ($branch = $branch_list_result->fetch_assoc()) {
          $branchename[] = $branch;
     }
}
######### Fetch all branches from the database #########


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


######### Fetch all products from the database #########
$products_list = "SELECT * FROM `products` WHERE `product_for_company`='$Company' AND `status` = '1'";
$products_list_result = $conn->query($products_list);
$productname = [];
if ($products_list_result->num_rows > 0) {
     while ($ProductData = $products_list_result->fetch_assoc()) {
          $productname[] = $ProductData;
     }
}
######### Fetch all products from the database #########


######### Fetch all employees from the database #########
$Employee_list = "SELECT * FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_of_branch`='$Branch' AND `employee_user_role` NOT IN ('Super Admin','Admin','Manager') AND `employee_status` = '1'";
$Employee_list_result = $conn->query($Employee_list);
$Employees = [];
$Technicians = [];
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
######### Fetch all employees from the database #########


######### Fetch all references from the database #########
$reference_list = "SELECT * FROM `references` WHERE `reference_status` = '1'";
$reference_list_result = mysqli_query($conn, $reference_list);
$reference_name = [];
if (mysqli_num_rows($reference_list_result) > 0) {
     while ($reference = $reference_list_result->fetch_assoc()) {
          $reference_name[] = $reference;
     }
}
######### Fetch all references from the database #########


######################### Add Lead ########################
if (isset($_POST['add_lead'])) {

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
     $lead_for_company = $Company;
     $lead_for_branch = $Branch;
     $lead_customer_name = $_POST['lead_customer_name'];
     $lead_customer_contact = $_POST['lead_customer_contact'];
     $lead_alternate_contact = $_POST['lead_alternate_contact'];
     $lead_customer_type = $_POST['lead_customer_type'];
     $lead_customer_gst = $_POST['lead_customer_gst'];
     $lead_customer_cin = $_POST['lead_customer_cin'];
     $lead_email = $_POST['lead_email'];
     $lead_whatsapp = $_POST['lead_whatsapp'];
     $lead_delivery_address = $_POST['lead_delivery_address'];
     $lead_state = $_POST['lead_state'];
     $lead_reference = $_POST['lead_reference'];
     $lead_type = $_POST['lead_type'];
     $lead_followup_date = date("Y-m-d");
     $lead_status = $_POST['lead_status'];
     $lead_remark = $_POST['lead_remark'];
     $lead_customer_status = 1;

     // Assign Technician
     if (!empty($_POST['assign_technician']) && $_POST['lead_type'] == 'Completed') {
          $assign_technician = trim($_POST['assign_technician']);
          $assign_technician_date = date('Y-m-d');
     } else {
          $assign_technician = "";
          $assign_technician_date = '0000-00-00';
     }

     // remarks
     $new_remark = $_POST['lead_remark'];
     if (!empty($lead_remark)) {
          $updated_remark = $lead_remark . ', ' . $new_remark;
     } else {
          $updated_remark = $new_remark;
     }

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
     if ($Role == 'Admin' || $Role == 'Manager') {
          $lead_assign_to = $_POST['lead_assign_to'];
          $lead_createdby = $username;
          $lead_updatedby = "";
     } elseif ($Role == 'Staff') {
          $lead_assign_to = $username;
          $lead_createdby = $username;
          $lead_updatedby = $username;
     }

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
     #################### End Specifications ######################


     $error_contact = '';
     $error_update = '';
     ###################### Validate contact number #######################
     if (!empty($lead_customer_contact)) {
          ###################### Check if contact already exists using prepared statement #######################
          $checkContact = "SELECT `lead_customer_contact` FROM `leads`  WHERE `lead_customer_contact` = '$lead_customer_contact' AND `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' LiMIT 1";
          $checkContactQuery = mysqli_query($conn, $checkContact);

          if (mysqli_num_rows($checkContactQuery) > 0) {
               $error_contact = "Error: Contact already exists! Please try another one.";
          } else {
               ###################### Insert Lead ######################
               $addLead = "INSERT INTO `leads`(
               `lead_id`, 
               `lead_customer_name`, 
               `lead_customer_contact`, 
               `lead_alternate_contact`, 
               `lead_for_company`, 
               `lead_for_branch`, 
               `lead_state`, 
               `lead_reference`, 
               `lead_product_services`, 
               `lead_delivery_address`, 
               `lead_email`, 
               `lead_followup_date`, 
               `lead_next_followup_date`, 
               `lead_next_followup_time`, 
               `lead_delivery_date`, 
               `assign_technician`, 
               `assign_technician_date`, 
               `lead_whatsapp`, 
               `lead_assign_to`, 
               `lead_status`, 
               `lead_remark`, 
               `lead_createdby`, 
               `lead_type`, 
               `lead_customer_type`, 
               `lead_customer_status`
               ) VALUES (
               '$lead_id',
               '$lead_customer_name',
               '$lead_customer_contact',
               '$lead_alternate_contact',
               '$lead_for_company',
               '$lead_for_branch',
               '$lead_state',
               '$lead_reference',
               '$product_services_name',
               '$lead_delivery_address',
               '$lead_email',
               '$lead_followup_date',
               '$lead_next_followup_date',
               '$lead_next_followup_time',
               '$lead_delivery_date',
               '$assign_technician',
               '$assign_technician_date',
               '$lead_whatsapp',
               '$lead_assign_to',
               '$lead_status',
               '$lead_remark',
               '$lead_createdby',
               '$lead_type',
               '$lead_customer_type',
               '$lead_customer_status'
               )";

               $ApplyAddLeadQuery = mysqli_query($conn, $addLead);

               if ($ApplyAddLeadQuery) {

                    ################# Push activity ################
                    $activity_id = $guid;
                    $activity_content = "New customer " . $lead_customer_name . " has beed added by manager " . $username . " of " . $Branch . " branch.";
                    $activity_type = 'Alert';
                    $activity_company = $Company;
                    $activity_branch = $Branch;
                    $activity_on = date('Y-m-d H:i:s');
                    $activity_by = $username;
                    $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                    $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                    ################# Push activity ################

                    echo "<script>window.location.href='lead-list.php';</script>";
               } else {
                    $error_update = 'Something went wrong. Please try again.';
               }
               ###################### Insert Lead ######################
          }
     }
}

######################## Added Lead #######################
?>


<!DOCTYPE html>
<html lang="en-US">

<head>
     <title>Add Lead</title>
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

          <!-- ==================================================== -->
          <!-- Start right Content here -->
          <!-- ==================================================== -->
          <div class="page-content">

               <!-- Start Container Fluid -->
               <div class="container-xxl">
                    <form action="#" method="POST" enctype="multipart/form-data">
                         <div class="row">
                              <div class="col-lg-12">

                                   <!-- Show error message here -->
                                   <?php if ($error_contact): ?>
                                        <div class="alert alert-danger alert-dismissible fade mb-3 rounded-2 show " role="alert">
                                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                             <?= $error_contact; ?>
                                        </div>
                                   <?php endif; ?>

                                   <!-- Show error message here -->
                                   <?php if ($error_update): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                             <?php echo $error_update; ?>
                                        </div>
                                   <?php endif; ?>

                                   <div class="card">
                                        <div class="card-header">
                                             <h4 class="card-title">Basic Details</h4>
                                        </div>

                                        <div class="card-body">
                                             <div class="row">
                                                  <!-- Customer Contact -->
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_customer_contact" class="form-label text-dark">Contact Number <small class="text-success">(Required)</small></label>
                                                            <input type="tel" id="lead_customer_contact" name="lead_customer_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}" required>
                                                       </div>
                                                  </div>

                                                  <!-- Person Whatsapp No. -->
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_whatsapp" class="form-label text-dark">
                                                                 <input type="checkbox" name="sameascontact" id="sameascontact"> Whatsapp No. same as Contact
                                                            </label>
                                                            <input type="tel" id="lead_whatsapp" name="lead_whatsapp" class="form-control" placeholder="Whatsapp No." minlength="10" maxlength="10" pattern="[0-9]{10}">
                                                       </div>
                                                  </div>


                                                  <!--  Customer name -->
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_customer_name" class="form-label text-dark">Organization / Customer Name </label>
                                                            <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Customer Name">
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


                                                  <!-- User Type -->
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_customer_type" class="form-label text-dark"> Customer Type </label>
                                                            <select name="lead_customer_type" id="lead_customer_type" data-choices>
                                                                 <option value="">Selec Customer Type</option>
                                                                 <option value="Client" <?php echo ($lead_customer_type == 'Client') ? ' selected' : ''; ?>>Client</option>
                                                                 <option value="Dealer" <?php echo ($lead_customer_type == 'Dealer') ? ' selected' : ''; ?>>Dealer</option>
                                                            </select>
                                                       </div>
                                                  </div>


                                                  <!-- Alternate Phone -->
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_alternate_contact" class="form-label text-dark"> Alternative Phone No. </label>
                                                            <input type="tel" id="lead_alternate_contact" name="lead_alternate_contact" class="form-control" placeholder="Phone No." value="<?php echo $lead_alternate_contact; ?>" minlength="10" maxlength="10" maxlength="10">
                                                       </div>
                                                  </div>

                                                  <!-- Employee Address -->
                                                  <div class="col-lg-6">
                                                       <div class="mb-3">
                                                            <label for="lead_delivery_address" class="form-label text-dark"> Address </label>
                                                            <textarea type="text" id="lead_delivery_address" name="lead_delivery_address" rows="1" class="form-control" placeholder="Address"><?php echo $lead_delivery_address; ?></textarea>
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
                                                       <div class="col-lg-12">
                                                            <div id="services-container"></div>
                                                       </div>

                                                       <div class="col-lg-4">
                                                            <div id="add-btn" class="btn add-field border-1 border-dark-subtle">Select Product</div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>

                                   <div class="card">
                                        <div class="card-header">
                                             <h4 class="card-title">Followup Status</h4>
                                        </div>

                                        <div class="card-body">
                                             <div class="row">
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
                                                                      <option value="<?php echo $Technassign['employee_name']; ?>" <?php echo ($Technassign['employee_name'] == $assigned_technician) ? 'selected' : ''; ?>>
                                                                           <?php echo $Technassign['employee_name']; ?>
                                                                      </option>
                                                                 <?php } ?>
                                                            </select>
                                                       </div>
                                                  </div>

                                                  <!-- Delivery Date -->
                                                  <div class="col-lg-3" style="display: none;" id="delivery_date">
                                                       <div class="mb-3">
                                                            <label for="lead_delivery_date" class="form-label text-dark">Delivery Date</label>
                                                            <input type="date" id="lead_delivery_date" id="lead_delivery_date" name="lead_delivery_date" class="form-control" placeholder="Delivery Date" value="<?php echo $lead_delivery_date; ?>">
                                                       </div>
                                                  </div>

                                                  <!-- Next Followup Date -->
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_next_followup_date" class="form-label text-dark">Next Followup Date <small class="text-success">(Required)</small></label>
                                                            <input type="date" id="lead_next_followup_date" name="lead_next_followup_date" class="form-control" placeholder="Next Followup Date" required>
                                                       </div>
                                                  </div>

                                                  <!-- Next Followup Time -->
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_next_followup_time" class="form-label text-dark">Next Followup Time <small class="text-success">(Required)</small></label>
                                                            <input type="time" id="lead_next_followup_time" name="lead_next_followup_time" class="form-control" placeholder="Next Followup Time" required>
                                                       </div>
                                                  </div>

                                                  <!-- Lead Status -->
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_status" class="form-label text-dark"> Lead Status <small class="text-success">(Required)</small></label>
                                                            <select class="form-control" name="lead_status" id="lead_status" data-choices required>
                                                                 <option value="">Lead Status</option>
                                                                 <option value="Call" <?php echo ($lead_status == 'Call') ? 'selected' : ''; ?>> 1. Call </option>
                                                                 <option value="Email" <?php echo ($lead_status == 'Email') ? 'selected' : ''; ?>> 2. Email </option>
                                                                 <option value="Whatsapp" <?php echo ($lead_status == 'Whatsapp') ? 'selected' : ''; ?>> 3. Whatsapp </option>
                                                                 <option value="Executive Visit" <?php echo ($lead_status == 'Executive Visit') ? 'selected' : ''; ?>> 4. Executive Visit </option>
                                                                 <option value="Client Visit" <?php echo ($lead_status == 'Client Visit') ? 'selected' : ''; ?>> 5. Client Visit </option>
                                                            </select>
                                                       </div>
                                                  </div>

                                                  <!-- Lead References -->
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_reference" class="form-label text-dark">Select Reference</label>
                                                            <select name="lead_reference" id="lead_reference" class="form-control" data-choices>
                                                                 <option value="">Select Reference</option>
                                                                 <?php foreach ($reference_name as $lead_reference) { ?>
                                                                      <option value="<?php echo $lead_reference['reference_name']; ?>"><?php echo $lead_reference['reference_name']; ?></option>
                                                                 <?php }
                                                                 ?>
                                                            </select>
                                                       </div>
                                                  </div>

                                                  <?php if ($Role == 'Manager') { ?>
                                                       <!-- Employee Name -->
                                                       <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                 <label for="lead_assign_to" class="form-label">Lead Assign to</label>
                                                                 <select class="form-control" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                                      <option value="">Lead Assign to</option>
                                                                      <?php foreach ($Employees as $employee) { ?>
                                                                           <option value="<?php echo $employee['employee_username']; ?>"
                                                                                data-branch="<?php echo $employee['branch_name']; ?>"
                                                                                <?php echo (isset($lead_assign_to) && $employee['employee_username'] == $lead_assign_to) ? 'selected' : ''; ?>>
                                                                                <?php echo $employee['employee_name']; ?>
                                                                           </option>
                                                                      <?php } ?>
                                                                 </select>
                                                            </div>
                                                       </div>
                                                  <?php } ?>

                                                  <!-- Persion Remarks -->
                                                  <?php if ($Role == 'Admin') { ?>
                                                       <div class="col-lg-6">
                                                       <?php } else { ?>
                                                            <div class="col-lg-12">
                                                            <?php } ?>
                                                            <label for="lead_remark" class="form-label text-dark">Remarks</label>
                                                            <textarea type="text" id="lead_remark" name="lead_remark" rows="2" class="form-control" placeholder="Remarks"></textarea>
                                                            </div>
                                                       </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>

                              <div class="p-3 bg-light mb-3 rounded">
                                   <div class="row justify-content-end g-2">
                                        <div class="col-lg-2">
                                             <button type="submit" name="add_lead" class="btn btn-outline-secondary w-100">Add Lead</button>
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

          <!-- ========== Footer Start ========== -->
          <?php include("../footer.php"); ?>
          <!-- ========== Footer End ========== -->

     </div>
     <!-- END Wrapper -->

     <!-- Vendor Javascript (Require in all Page) -->
     <script src="../assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="../assets/js/app.js"></script>

     <!-- datepicker js -->
     <script>
          document.getElementById('lead_delivery_date').flatpickr();
          document.getElementById('lead_next_followup_date').flatpickr();
          document.getElementById('lead_next_followup_time').flatpickr({
               enableTime: true,
               noCalendar: true,
               dateFormat: "H:i"
          });
     </script>

     <!-- ================== Select Product Contact ================== -->
     <script>
          // Get the add button and the container
          const addBtn = document.getElementById('add-btn');
          const servicesContainer = document.getElementById('services-container');
          let servicesCount = <?php echo count($productname); ?>;

          // Function to create a new div with select fields
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
                    <span class="remove-field mx-1 remove-feild border"> - </span>
               </div>`;
               return newDiv;
          }

          // Add event listener to the add button
          addBtn.addEventListener('click', () => {
               const newDiv = createServicesDiv();
               servicesContainer.appendChild(newDiv);

               // Reinitialize Choices.js for the newly added <select>
               const newSelect = newDiv.querySelector('[data-choices]');
               if (newSelect) {
                    new Choices(newSelect);
               }
               servicesCount++;
          });

          // Add event listener to the remove buttons
          servicesContainer.addEventListener('click', (event) => {
               if (event.target.classList.contains('remove-feild')) {
                    const divToRemove = event.target.closest('.col-lg-12');
                    divToRemove.remove();
                    servicesCount--;
               }
          });
     </script>



     <!-- ============= disabled and enabled date ============= -->
     <script>
          function showDeliveryDate(leadType) {
               var nextFollowupDateInput = document.getElementById('lead_next_followup_date');
               var nextFollowupTimeInput = document.getElementById('lead_next_followup_time');
               var deliveryDateSection = document.getElementById('delivery_date');
               var deliveryDateInput = document.getElementById('lead_delivery_date');

               // Show/Hide Delivery Date based on lead type
               if (leadType === "Completed") {
                    deliveryDateSection.style.display = "block";
               } else {
                    deliveryDateSection.style.display = "none";
               }

               if (leadType === "Completed") {
                    if (leadType === "Completed") {
                         AssignTechnician.style.display = "block";
                    } else {
                         AssignTechnician.style.display = "none";
                    }
               } else {
                    AssignTechnician.style.display = "none";
               }
          }

          // Call the function initially to set the correct state on page load (if necessary)
          document.addEventListener('DOMContentLoaded', function() {
               showDeliveryDate(document.getElementById('lead_type').value);
          });
     </script>


     <!-- ============= same as contact number ============= -->
     <script>
          // Get references to the checkbox, contact input, and WhatsApp input
          const sameAsContactCheckbox = document.getElementById('sameascontact');
          const contactInput = document.getElementById('lead_customer_contact');
          const whatsappInput = document.getElementById('lead_whatsapp');

          // Function to update the WhatsApp number
          function updateWhatsapp() {
               if (sameAsContactCheckbox.checked) {
                    // If checkbox is checked, copy the contact number to WhatsApp field
                    whatsappInput.value = contactInput.value;
               } else {
                    // If checkbox is unchecked, clear the WhatsApp field
                    whatsappInput.value = '';
               }
          }

          // Event listeners for checkbox and contact number input
          sameAsContactCheckbox.addEventListener('change', updateWhatsapp);
          contactInput.addEventListener('input', updateWhatsapp); // To update when the contact number changes
     </script>

</body>

</html>