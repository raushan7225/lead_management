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
               $statename[] = $state['state_name'];
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
     $Employee_list = "SELECT * FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_of_branch`='$Branch' AND `employee_user_role`='Staff' OR `employee_user_role`='Technician' AND `employee_status` = '1'";
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
     $reference_list = "SELECT * FROM `references` WHERE `reference_for_company`='$Company' AND `reference_status` = '1'";
     $reference_list_result = mysqli_query($conn, $reference_list);
     $reference_name = [];
     if (mysqli_num_rows($reference_list_result) > 0) {
          while ($reference = $reference_list_result->fetch_assoc()) {
               $reference_name[] = $reference;
          }
     }
     ######### Fetch all references from the database #########


     ################### Show Leads Details ########################
     $checkLeads = "SELECT * FROM `leads` WHERE `lead_for_company`='$Company' AND `lead_for_branch`='$Branch' AND `lead_customer_status` = '1' AND `id` = '$id'";
     $checkLeadsQuery = mysqli_query($conn, $checkLeads);
     if (mysqli_num_rows($checkLeadsQuery) > 0) {
          while ($lead = mysqli_fetch_assoc($checkLeadsQuery)) {
               $lead_id = trim($lead['lead_id']);
               $lead_customer_name = trim($lead['lead_customer_name']);
               $lead_customer_contact = trim($lead['lead_customer_contact']);
               $lead_alternate_contact = trim($lead['lead_alternate_contact']);
               $lead_for_company = trim($lead['lead_for_company']);
               $lead_for_branch = trim($lead['lead_for_branch']);
               $lead_state = trim($lead['lead_state']);
               $lead_reference = trim($lead['lead_reference']);
               $lead_type = trim($lead['lead_type']);
               $lead_customer_type = trim($lead['lead_customer_type']);
               $lead_product_services = trim($lead['lead_product_services']);
               $lead_next_followup_date = trim($lead['lead_next_followup_date']);
               $lead_next_followup_time = trim($lead['lead_next_followup_time']);
               $lead_delivery_date = trim($lead['lead_delivery_date']);
               $lead_delivery_address = trim($lead['lead_delivery_address']);
               $lead_whatsapp = trim($lead['lead_whatsapp']);
               $lead_remark = trim($lead['lead_remark']);
               $lead_assign_to = trim($lead['lead_assign_to']);
               $lead_status = $lead['lead_status'];

               // Imploding the remarks to show as a list
               $remarks_array = explode(', ', $lead_remark);

               $assigned_technician = trim($lead['assign_technician']);
               $assigned_technician_date = trim($lead['assign_technician_date']);

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
     ################### Show Leads Details ########################

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

               <!-- ==================================================== -->
               <!-- Start right Content here -->
               <!-- ==================================================== -->
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
                                                                 <label for="lead_customer_contact" class="form-label text-dark">Contact Number <small class="text-success">(Required)</small></label>
                                                                 <input type="tel" id="lead_customer_contact" name="lead_customer_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}" required value="<?php echo $lead_customer_contact; ?>">
                                                            </div>
                                                       </div>

                                                       <!-- Person Whatsapp No. -->
                                                       <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                 <label for="lead_whatsapp" class="form-label text-dark">
                                                                      <input type="checkbox" name="sameascontact" id="sameascontact"> Whatsapp No. same as Contact
                                                                 </label>
                                                                 <input type="tel" id="lead_whatsapp" name="lead_whatsapp" class="form-control" placeholder="Whatsapp No." minlength="10" maxlength="10" pattern="[0-9]{10}" value="<?php echo $lead_whatsapp; ?>">
                                                            </div>
                                                       </div>


                                                       <!--  Customer name -->
                                                       <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                 <label for="lead_customer_name" class="form-label text-dark">Organization / Customer Name </label>
                                                                 <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Customer Name" value="<?php echo $lead_customer_name; ?>">
                                                            </div>
                                                       </div>


                                                       <!-- State List -->
                                                       <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                 <label for="lead_state" class="form-label text-dark">Select State </label>
                                                                 <select name="lead_state" id="lead_state" class="form-control" data-choices>
                                                                      <option value="">Select State</option>
                                                                      <?php foreach ($statename as $statees) { ?>
                                                                           <option value="<?php echo $statees; ?>" <?php echo ($lead_state == $statees) ? 'selected' : ''; ?>><?php echo $statees; ?></option>
                                                                      <?php } ?>
                                                                 </select>
                                                            </div>
                                                       </div>

                                                       <!-- User Type -->
                                                       <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                 <label for="lead_customer_type" class="form-label text-dark"> Customer Type </label>
                                                                 <select name="lead_customer_type" id="lead_customer_type" class="form-control" data-choices>
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
                                                                 <input type="tel" id="lead_alternate_contact" name="lead_alternate_contact" class="form-control" placeholder="Phone No." value="<?php echo $lead_alternate_contact; ?>" minlength="10" maxlength="10" maxlength="10" value="<?php echo $lead_alternate_contact; ?>">
                                                            </div>
                                                       </div>

                                                       <!-- Employee Address -->
                                                       <div class="col-lg-6">
                                                            <label for="lead_delivery_address" class="form-label text-dark"> Address </label>
                                                            <textarea type="text" id="lead_delivery_address" name="lead_delivery_address" rows="1" class="form-control" placeholder="Address"><?php echo $lead_delivery_address; ?></textarea>
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
                                                                      <?php foreach ($Technicians as $Techassign) { ?>
                                                                           <option value="<?php echo $Techassign['employee_name']; ?>" <?php echo (isset($Techassign['employee_name']) && $Techassign['employee_name'] == $assigned_technician) ? 'selected' : ''; ?>>
                                                                                <?php echo $Techassign['employee_name']; ?>
                                                                           </option>
                                                                      <?php } ?>
                                                                 </select>
                                                            </div>
                                                       </div>

                                                       <!-- Delivery Date -->
                                                       <div id="deliveryDate" class="col-lg-3" style="display: none;">
                                                            <div class="mb-3">
                                                                 <label for="lead_delivery_date" class="form-label text-dark">Delivery Date <small class="text-success">(Required)</small></label>
                                                                 <input type="date" id="basic-datepicker1" name="lead_delivery_date" class="form-control" placeholder="Delivery Date" value="<?php echo $lead_delivery_date; ?>" required>
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
                                                                      <?php foreach ($reference_name as $leadReference) { ?>
                                                                           <option value="<?php echo $leadReference['reference_name']; ?>" <?php echo ($leadReference['reference_name'] == $lead_reference) ? 'selected' : ''; ?>><?php echo $leadReference['reference_name']; ?></option>
                                                                      <?php }
                                                                      ?>
                                                                 </select>
                                                            </div>
                                                       </div>


                                                       <!-- Employee Name -->
                                                       <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                 <label for="lead_assign_to" class="form-label">Lead Assign to</label>
                                                                 <select class="form-control" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                                      <option value="">Lead Assign to</option>
                                                                      <?php foreach ($Employees as $employess) { ?>
                                                                           <option value="<?php echo $employess['employee_username']; ?>"
                                                                                data-branch="<?php echo $employess['branch_name']; ?>"
                                                                                <?php echo (isset($lead_assign_to) && $employess['employee_username'] == $lead_assign_to) ? 'selected' : ''; ?>>
                                                                                <?php echo $employess['employee_name']; ?>
                                                                           </option>
                                                                      <?php } ?>
                                                                 </select>
                                                            </div>
                                                       </div>


                                                       <!-- lead Remarks -->
                                                       <div class="col-lg-6">
                                                            <div class="mb-2">
                                                                 <label for="lead_remark" class="form-label text-dark">Remarks</label>
                                                                 <textarea id="lead_remark" class="form-control" name="lead_remark" rows="1" placeholder="Enter your remark here whthout (',')...."><?php echo $lead_remark; ?></textarea>
                                                            </div>
                                                       </div>

                                                       <!-- Remarks list -->
                                                       <div class="col-lg-6">
                                                            <h4>All Remarks</h4>
                                                            <ul style="list-style-type: number">
                                                                 <?php
                                                                 // Display all the remarks as a list
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
               // JavaScript code as explained above
               function showDeliveryDate(leadType) {
                    const nextFollowupDate = document.getElementById("basic-datepicker2");
                    const nextFollowupTime = document.getElementById("basic-timepicker");
                    const deliveryDate = document.getElementById("deliveryDate");
                    const AssignTechnician = document.getElementById("AssignTechnician");

                    if (leadType === "Cancel" || leadType === "Completed") {
                         if (leadType === "Completed") {
                              deliveryDate.style.display = "block";
                         } else {
                              deliveryDate.style.display = "none";
                         }
                    } else {
                         deliveryDate.style.display = "none";
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

               // Run the function on page load for initial state
               window.onload = function() {
                    const leadType = document.getElementById("lead_type").value;
                    showDeliveryDate(leadType);
               };
          </script>


          <script>
               // Get the add button and the container
               const addBtn = document.getElementById('add-btn');
               const servicesContainer = document.getElementById('services-container');
               let servicesCount = <?php echo count($myServiceLead); ?>;

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
                         <span class="remove-field remove-feild border"> - </span>
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

          <script>
               // Get the checkbox and input fields
               var checkbox = document.getElementById('sameascontact');
               var contactNumberInput = document.getElementById('lead_customer_contact');
               var whatsappNumberInput = document.getElementById('lead_whatsapp');

               // Add an event listener to the checkbox
               checkbox.addEventListener('change', function() {
                    if (this.checked) {
                         // Set the WhatsApp number input field to the same value as the contact number input field
                         whatsappNumberInput.value = contactNumberInput.value;
                    } else {
                         // Clear the WhatsApp number input field
                         whatsappNumberInput.value = '';
                    }
               });
          </script>

     </body>

     </html>

<?php
     ######################### Update Lead ########################
     if (isset($_POST['update_lead'])) {
          $lead_for_company = $Company;
          $lead_for_branch = $Branch;
          $lead_customer_contact = trim($_POST['lead_customer_contact']);
          $lead_alternate_contact = trim($_POST['lead_alternate_contact']);
          $lead_whatsapp = trim($_POST['lead_whatsapp']);
          $lead_customer_name = trim($_POST['lead_customer_name']);
          $lead_state = trim($_POST['lead_state']);
          $lead_customer_type = $_POST['lead_customer_type'];
          $lead_delivery_address = trim($_POST['lead_delivery_address']);
          $lead_type = trim($_POST['lead_type']);
          $lead_status = trim($_POST['lead_status']);
          $lead_reference = trim($_POST['lead_reference']);
          $lead_assign_to = trim($_POST['lead_assign_to']);
          $lead_updatedby = "";

          // Assign Technician
          if (!empty($_POST['assign_technician']) && $_POST['lead_type'] == 'Completed') {
               $assign_technician = trim($_POST['assign_technician']);
               $assign_technician_date = trim(date('Y-m-d'));
          } else {
               $assign_technician = "";
               $assign_technician_date = '0000-00-00';
          }

          // remarks
          $new_remark = trim($_POST['lead_remark']);
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
               $lead_next_followup_date = trim($_POST['lead_next_followup_date']);
               $lead_next_followup_time = trim($_POST['lead_next_followup_time']);
          }

          // Delivery Date
          if ($_POST['lead_type'] == 'Completed') {
               $lead_delivery_date = trim($_POST['lead_delivery_date']);
          } elseif ($_POST['lead_type'] == 'Cancel') {
               $lead_delivery_date = "0000-00-00";
          } else {
               $lead_delivery_date = "0000-00-00";
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



          // #################### Troubleshooting #######################
          // echo $lead_id . "<br>";
          // echo $lead_customer_name . "<br>";
          // echo $lead_customer_contact . "<br>";
          // echo $lead_alternate_contact . "<br>";
          // echo $lead_for_company . "<br>";
          // echo $lead_for_branch . "<br>";
          // echo $lead_state . "<br>";
          // echo $lead_reference . "<br>";
          // echo $product_services_name . "<br>";
          // echo $lead_delivery_address . "<br>";
          // echo $lead_next_followup_date . "<br>";
          // echo $lead_delivery_date . "<br>";
          // echo $lead_whatsapp . "<br>";
          // echo $lead_status . "<br>";
          // echo $lead_remark . "<br>";
          // echo $lead_updatedby . "<br>";
          // echo $lead_assign_to . "<br>";
          // echo $lead_type . "<br>";
          // echo $lead_customer_type;
          // #################### Troubleshooting #######################

          // Prepare and execute the update query
          $updateLead = "UPDATE `leads` SET 
          `lead_for_company`='$lead_for_company',
          `lead_for_branch`='$lead_for_branch',
          `lead_state`='$lead_state',
          `lead_reference`='$lead_reference',
          `assign_technician`='$assign_technician',
          `assign_technician_date`='$assign_technician_date',
          `lead_product_services`='$product_services_name',
          `lead_delivery_address`='$lead_delivery_address',
          `lead_next_followup_date`='$lead_next_followup_date',
          `lead_next_followup_time`='$lead_next_followup_time',
          `lead_delivery_date`='$lead_delivery_date',
          `lead_whatsapp`='$lead_whatsapp',
          `lead_status`='$lead_status',
          `lead_remark`='$lead_remark',
          `lead_assign_to`='$lead_assign_to',
          `lead_type`='$lead_type',
          `lead_customer_type`='$lead_customer_type',
          `lead_updatedby`='$lead_updatedby'
          WHERE `id` = '$id'";
          $ApplyUpdateLead = mysqli_query($conn, $updateLead);

          ################# Push activity ################
          $activity_id = $guid;
          $activity_content = "Followup of customer " . $lead_customer_name . " has beed followed up by manager " . $username . " of " . $Branch . " branch.";
          $activity_type = 'Alert';
          $activity_company = $Company;
          $activity_branch = $Branch;
          $activity_on = date('Y-m-d H:i:s');
          $activity_by = $username;
          $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
          $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
          ################# Push activity ################

          // Execute query and handle response
          if ($ApplyUpdateLead) {
               echo "<script>window.location.href='lead-list.php';</script>";
          } else {
               echo "Something went wrong. Please try again.";
          }
     }
     ######################## Updated Lead #######################
} else {
     echo "<script>window.location.href='lead-list.php';</script>";
     exit;
}
$conn->close();
?>