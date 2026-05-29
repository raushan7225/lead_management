<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

###################### Initialize variables #######################
$error_contact = "";
$branchename = $statename = $productname = $Employeename = $reference_name = [];

###################### Function to fetch data from database with error handling #######################     
function fetchData($conn, $query)
{
     $result = $conn->query($query);
     if (!$result) {
          error_log("Database query failed: " . $conn->error);
          return [];
     }
     $data = [];
     if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
               $data[] = $row;
          }
     }
     return $data;
}

###################### Fetch all required data in a more efficient way #######################
$queries = [
     'branchename' => "SELECT * FROM `branches` WHERE `branch_status` = '1'",
     'statename' => "SELECT * FROM `states` WHERE `state_status` = '1'",
     'productname' => "SELECT * FROM `products` WHERE `product_for_company`='$Company' AND `status` = '1'",
     'Employeename' => "SELECT * FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_of_branch`='$Branch' AND `employee_user_role` = 'Staff' AND `employee_status` = '1'",
     'reference_name' => "SELECT * FROM `references` WHERE `reference_for_company`='$Company' AND `reference_status` = '1'"
];

foreach ($queries as $key => $query) {
     $$key = fetchData($conn, $query);
}

###################### Add Lead functionality #######################
if (isset($_POST['add_lead'])) {
     ###################### Generate unique ID
     $lead_id = '#ID' . random_int(1000000, 9999999); // More secure than rand()

     ###################### Sanitize input data #######################
     $lead_customer_contact = trim($_POST['lead_customer_contact']);
     $lead_customer_name = trim($_POST['lead_customer_name']);
     $lead_alternate_contact = trim($_POST['lead_alternate_contact']);
     $lead_whatsapp = trim($_POST['lead_whatsapp']);
     $lead_delivery_address = trim($_POST['lead_delivery_address']);
     $lead_remark = trim($_POST['lead_remark']);

     ###################### Validate contact number #######################
     if (!empty($lead_customer_contact)) {
          ###################### Check if contact already exists using prepared statement #######################
          $checkContact = "SELECT `lead_customer_contact` FROM `leads` WHERE `lead_customer_contact` = '$lead_customer_contact' AND `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' LiMIT 1";
          $checkContactQuery = mysqli_query($conn, $checkContact);

          if (mysqli_num_rows($checkContactQuery) > 0) {
               $error_contact = "Error: The contact number already exists! Please try another one.";
          } else {
               ###################### Process product services #######################
               $servicespro = array_filter(array_map('trim', $_POST['lead_product_services'] ?? []));
               $product_services_name = implode(', ', $servicespro);

               ###################### Determine dates based on lead type #######################
               $lead_type = $_POST['lead_type'];
               $lead_followup_date = date("Y-m-d");
               $lead_next_followup_date = $lead_followup_date;
               $lead_next_followup_time = date("H:i");
               $lead_delivery_date = "0000-00-00";

               if ($lead_type !== 'Cancel' && $lead_type !== 'Completed') {
                    $lead_next_followup_date = $_POST['lead_next_followup_date'];
                    $lead_next_followup_time = $_POST['lead_next_followup_time'];
               } elseif ($lead_type == 'Completed') {
                    $lead_delivery_date = $_POST['lead_delivery_date'];
               }

               ###################### Insert query #######################
               $query = "INSERT INTO `leads` (
               `lead_id`, `lead_customer_name`, `lead_customer_contact`, `lead_alternate_contact`, 
               `lead_for_company`, `lead_for_branch`, `lead_state`, `lead_reference`, 
               `lead_product_services`, `lead_delivery_address`, `lead_followup_date`, 
               `lead_next_followup_date`, `lead_next_followup_time`, `lead_delivery_date`, 
               `lead_whatsapp`, `lead_assign_to`, `lead_createdby`, `lead_updatedby`, 
               `lead_status`, `lead_remark`, `lead_type`, `lead_customer_type`, `lead_customer_status`
               ) VALUES (
               '$lead_id', '$lead_customer_name', '$lead_customer_contact', '$lead_alternate_contact', 
               '$Company', '$Branch', '" . $_POST['lead_state'] . "', '" . $_POST['lead_reference'] . "', 
               '$product_services_name', '$lead_delivery_address', '$lead_followup_date', 
               '$lead_next_followup_date', '$lead_next_followup_time', '$lead_delivery_date', 
               '$lead_whatsapp', '$username', '$username', '$username', 
               '" . $_POST['lead_status'] . "', '$lead_remark', '$lead_type', '" . $_POST['lead_customer_type'] . "', '1'
               )";

               if (mysqli_query($conn, $query)) {
                    ################# Push activity ################
                    $activity_id = $guid;
                    $activity_content = "New customer " . $lead_customer_name . " has been added by staff " . $username . " of " . $Branch . " branch.";
                    $activity_type = 'Alert';
                    $activity_company = $Company;
                    $activity_branch = $Branch;
                    $activity_on = date('Y-m-d H:i:s');
                    $activity_by = $username;
                    $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                    $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                    ################# Push activity ################

                    header("Location: lead-list.php");
                    exit;
               } else {
                    echo "Error: " . mysqli_error($conn);
                    echo "<script>alert('Something went wrong. Please try again.');</script>";
               }
          }
     }
}
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

          .remove-feild {
               cursor: pointer;
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

               <div class="container-xxl">
                    <form method="POST">
                         <div class="row">
                              <div class="col-lg-12">

                                   <?php if ($error_contact): ?>
                                        <div class="alert alert-danger alert-dismissible fade mb-3 rounded-2 show " role="alert">
                                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                             <?= $error_contact; ?>
                                        </div>
                                   <?php endif; ?>

                                   <div class="card">
                                        <div class="card-header">
                                             <h4 class="card-title">Basic Details</h4>
                                        </div>

                                        <div class="card-body">
                                             <div class="row">
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_customer_contact" class="form-label text-dark">Contact Number <small class="text-success">(Required)</small></label>
                                                            <input type="tel" id="lead_customer_contact" name="lead_customer_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}" required>
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_whatsapp" class="form-label text-dark">
                                                                 <input type="checkbox" name="sameascontact" id="sameascontact"> Whatsapp No. same as Contact
                                                            </label>
                                                            <input type="tel" id="lead_whatsapp" name="lead_whatsapp" class="form-control" placeholder="Whatsapp No." minlength="10" maxlength="10" pattern="[0-9]{10}">
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_customer_name" class="form-label text-dark">Organization / Customer Name</label>
                                                            <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Customer Name">
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_state" class="form-label text-dark">Select State</label>
                                                            <select name="lead_state" id="lead_state" class="form-control" data-choices>
                                                                 <option value="">Select State</option>
                                                                 <?php foreach ($statename as $states): ?>
                                                                      <option value="<?= $states['state_name'] ?>"><?= $states['state_name'] ?></option>
                                                                 <?php endforeach; ?>
                                                            </select>
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_customer_type" class="form-label text-dark">Customer Type</label>
                                                            <select name="lead_customer_type" id="lead_customer_type" data-choices>
                                                                 <option value="">Select Customer Type</option>
                                                                 <option value="Client">Client</option>
                                                                 <option value="Dealer">Dealer</option>
                                                            </select>
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_alternate_contact" class="form-label text-dark">Alternative Phone No.</label>
                                                            <input type="tel" id="lead_alternate_contact" name="lead_alternate_contact" class="form-control" placeholder="Phone No." minlength="10" maxlength="10">
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-6">
                                                       <div class="mb-3">
                                                            <label for="lead_delivery_address" class="form-label text-dark">Address</label>
                                                            <textarea id="lead_delivery_address" name="lead_delivery_address" rows="1" class="form-control" placeholder="Address"></textarea>
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
                                                       <div id="services-container"></div>
                                                       <div id="add-btn" class="btn add-field border-1 border-dark-subtle mt-2">Select Product</div>
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
                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_type" class="form-label text-dark">Lead Type <small class="text-success">(Required)</small></label>
                                                            <select class="form-control" name="lead_type" id="lead_type" data-choices required onchange="showDeliveryDate(this.value)">
                                                                 <option value="">Lead Type</option>
                                                                 <option value="Hot">1. Hot</option>
                                                                 <option value="Cold">2. Cold</option>
                                                                 <option value="Warm">3. Warm</option>
                                                                 <option value="Place Order">4. Place Order</option>
                                                                 <option value="Cancel">5. Cancel</option>
                                                                 <option value="Completed">6. Completed</option>
                                                            </select>
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3" style="display: none;" id="delivery_date">
                                                       <div class="mb-3">
                                                            <label for="lead_delivery_date" class="form-label text-dark">Delivery Date</label>
                                                            <input type="date" id="lead_delivery_date" name="lead_delivery_date" class="form-control">
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_next_followup_date" class="form-label text-dark">Next Followup Date <small class="text-success">(Required)</small></label>
                                                            <input type="date" id="lead_next_followup_date" name="lead_next_followup_date" class="form-control" required>
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_next_followup_time" class="form-label text-dark">Next Followup Time <small class="text-success">(Required)</small></label>
                                                            <input type="time" id="lead_next_followup_time" name="lead_next_followup_time" class="form-control" required>
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_status" class="form-label text-dark">Lead Status <small class="text-success">(Required)</small></label>
                                                            <select class="form-control" name="lead_status" id="lead_status" data-choices required>
                                                                 <option value="">Lead Status</option>
                                                                 <option value="Call">1. Call</option>
                                                                 <option value="Email">2. Email</option>
                                                                 <option value="Whatsapp">3. Whatsapp</option>
                                                                 <option value="Executive Visit">4. Executive Visit</option>
                                                                 <option value="Client Visit">5. Client Visit</option>
                                                            </select>
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-3">
                                                       <div class="mb-3">
                                                            <label for="lead_reference" class="form-label text-dark">Select Reference</label>
                                                            <select name="lead_reference" id="lead_reference" class="form-control" data-choices>
                                                                 <option value="">Select Reference</option>
                                                                 <?php foreach ($reference_name as $ref): ?>
                                                                      <option value="<?= $ref['reference_name'] ?>"><?= $ref['reference_name'] ?></option>
                                                                 <?php endforeach; ?>
                                                            </select>
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-6">
                                                       <div class="mb-3">
                                                            <label for="lead_remark" class="form-label text-dark">Remarks</label>
                                                            <textarea id="lead_remark" name="lead_remark" rows="1" class="form-control" placeholder="Remarks"></textarea>
                                                       </div>
                                                  </div>

                                                  <!-- <div class="col-lg-3" style="display: none;" id="lead_transfers">
                                                       <div class="mb-3">
                                                            <label for="lead_assign_to" class="form-label">Lead Transfer</label>
                                                            <select class="form-control" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                                 <option value="">Lead Transfer</option>
                                                                 <!?php foreach ($Employeename as $employee): ?>
                                                                      <option value="<!?= $employee['employee_username'] ?>">
                                                                           <!?= $employee['employee_name'] ?>
                                                                      </option>
                                                                 <!?php endforeach; ?>
                                                            </select>
                                                       </div>
                                                  </div> -->
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
          <!-- ==================================================== -->
          <!-- End Page Content -->
          <!-- ==================================================== -->

          <!-- ========== Footer Start ========== -->
          <?php include("../footer.php"); ?>
          <!-- ========== Footer End ========== -->

     </div>
     <!-- Vendor Javascript (Require in all Page) -->
     <script src="../assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="../assets/js/app.js"></script>

     <!-- Date Picker -->
     <script>
          document.getElementById('lead_delivery_date').flatpickr();
          document.getElementById('lead_next_followup_date').flatpickr();
          document.getElementById('lead_next_followup_time').flatpickr({
               enableTime: true,
               noCalendar: true,
               dateFormat: "H:i"
          });
     </script>

     <script>
          // Dynamic product services fields
          const addBtn = document.getElementById('add-btn');
          const servicesContainer = document.getElementById('services-container');
          let servicesCount = <?= count($productname) ?>;

          function createServicesDiv() {
               const newDiv = document.createElement('div');
               newDiv.className = 'col-lg-12 mb-2';
               newDiv.innerHTML = `
            <div class="input-group">
                <select name="lead_product_services[]" class="form-control" data-choices>
                    <option value="">Select Product</option>
                    <?php foreach ($productname as $product): ?>
                    <option value="<?= $product['product_name'] ?>"><?= $product['product_name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="remove-field mx-1 remove-feild border"> - </span>
            </div>`;
               return newDiv;
          }

          addBtn.addEventListener('click', () => {
               const newDiv = createServicesDiv();
               servicesContainer.appendChild(newDiv);
               new Choices(newDiv.querySelector('[data-choices]'));
               servicesCount++;
          });

          servicesContainer.addEventListener('click', (e) => {
               if (e.target.classList.contains('remove-feild')) {
                    e.target.closest('.col-lg-12').remove();
                    servicesCount--;
               }
          });

          // Show/hide delivery date based on lead type
          function showDeliveryDate(leadType) {
               const nextFollowupDateInput = document.getElementById('lead_next_followup_date');
               const nextFollowupTimeInput = document.getElementById('lead_next_followup_time');
               const deliveryDateSection = document.getElementById('delivery_date');

               if (leadType === "Cancel" || leadType === "Completed") {
                    nextFollowupDateInput.disabled = true;
                    nextFollowupTimeInput.disabled = true;
               } else {
                    nextFollowupDateInput.disabled = false;
                    nextFollowupTimeInput.disabled = false;
               }

               deliveryDateSection.style.display = leadType === "Completed" ? "block" : "none";
          }

          // Same as contact checkbox functionality
          const sameAsContactCheckbox = document.getElementById('sameascontact');
          const contactInput = document.getElementById('lead_customer_contact');
          const whatsappInput = document.getElementById('lead_whatsapp');

          function updateWhatsapp() {
               whatsappInput.value = sameAsContactCheckbox.checked ? contactInput.value : '';
          }

          sameAsContactCheckbox.addEventListener('change', updateWhatsapp);
          contactInput.addEventListener('input', updateWhatsapp);

          // Initialize on page load
          document.addEventListener('DOMContentLoaded', function() {
               showDeliveryDate(document.getElementById('lead_type').value);
          });
     </script>
</body>

</html>