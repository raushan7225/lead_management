<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


if (isset($_POST['add_branch'])) {
     ########### Genrate Unique ID #############
     function generateId()
     {
          $prefix = '#ID';
          $randomNumber = rand(1, 9999999);
          return $prefix . $randomNumber;
     }
     $uniqueId = generateId();
     ########### Genrate Unique ID #############


     $branch_id = $uniqueId;
     $branch_of_company = $_POST['branch_of_company'];
     $branch_name = $_POST['branch_name'];
     $branch_contact = $_POST['branch_contact'];
     $branch_email = $_POST['branch_email'];
     $branch_registration_number = $_POST['branch_registration_number'];
     $branch_gst = $_POST['branch_gst'];
     $branch_state_code = $_POST['branch_state_code'];
     $branch_bank_name = $_POST['branch_bank_name'];
     $branch_bank_branch = $_POST['branch_bank_branch'];
     $branch_account_number = $_POST['branch_account_number'];
     $branch_ifsc_code = $_POST['branch_ifsc_code'];
     $branch_address = trim($_POST['branch_address']);
     $branch_term_and_condition = trim($_POST['branch_term_and_condition']);
     $branch_status = 1;

     ################# Branch Add Query ################
     $add_branch = "INSERT INTO `branches`(`branch_id`, `branch_of_company`, `branch_name`, `branch_gst`, `branch_contact`, `branch_email`, `branch_address`, `branch_registration_number`, `branch_bank_name`, `branch_bank_branch`, `branch_account_number`, `branch_ifsc_code`, `branch_term_and_condition`, `branch_status`) VALUES ('$branch_id','$branch_of_company','$branch_name','$branch_gst','$branch_contact','$branch_email','$branch_address','$branch_registration_number','$branch_bank_name','$branch_bank_branch','$branch_account_number','$branch_ifsc_code','$branch_term_and_condition','$branch_status')";

     if (mysqli_query($conn, $add_branch)) {

          ################# Push activity ################
          $activity_id = $guid;
          $activity_content = "New branch " . $branch_name . " has been added by " . $Role . ".";
          $activity_type = 'Alert';
          $activity_company = "";
          $activity_branch = "";
          $activity_on = date('Y-m-d H:i:s');
          $activity_by = $username;
          $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
          $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
          ################# Push activity ################

          echo "<script>window.location.href = 'branch-list.php';</script>";
     } else {
          echo "<script>alert('Something went wrong!');</script>";
     }
     ################# Branch Add Query ################
}

################### Show Branches ######################
$Companies_list = "SELECT * FROM `companies` WHERE `company_status` = 1";
$CompaniesQuery = mysqli_query($conn, $Companies_list);
################### Show Branches ######################
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
     <title>Branch Add</title>
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
                         <div class="row">
                              <div class="col-lg-12">
                                   <div class="card">
                                        <div class="card-header">
                                             <h4 class="card-title">Add Branch</h4>
                                        </div>
                                        <form action="#" method="POST" enctype="multipart/form-data" onsubmit="return submitForm()">
                                             <div class="card-body">
                                                  <div class="row">

                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch_of_company" class="form-label text-dark">Company <small class="text-success">(Required)</small></label>
                                                                 <select name="branch_of_company" id="branch_of_company" data-choices required>
                                                                      <option value="">Select Company</option>
                                                                      <?php if (mysqli_num_rows($CompaniesQuery) > 0) {
                                                                           while ($myCompany = mysqli_fetch_array($CompaniesQuery)) { ?>
                                                                                <option value="<?php echo $myCompany['company_name']; ?>"> <?php echo $myCompany['company_name']; ?> </option>
                                                                      <?php }
                                                                      } ?>
                                                                 </select>
                                                            </div>
                                                       </div>

                                                       <!-- Branch Name (Initially hidden) -->
                                                       <div class="col-lg-4" id="branch-container" style="display: none;">
                                                            <div class="mb-3">
                                                                 <label for="branch_name" class="form-label text-dark">Branch Name <small class="text-success">(Required)</small></label>
                                                                 <input type="text" id="branch_name" name="branch_name" class="form-control" placeholder="Branch Name" required>
                                                            </div>
                                                       </div>



                                                       <!-- Contact Number -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-contact" class="form-label text-dark">Contact Number <small class="text-success">(Required)</small></label>
                                                                 <input type="tel" id="branch-contact" name="branch_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" required>
                                                            </div>
                                                       </div>

                                                       <!-- Email ID -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-email" class="form-label text-dark">Email ID</label>
                                                                 <input type="email" id="branch-email" name="branch_email" class="form-control" placeholder="Email ID">
                                                            </div>
                                                       </div>

                                                       <!-- Registration Number -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-registration" class="form-label text-dark">Registration Number</label>
                                                                 <input type="text" id="branch-registration" name="branch_registration_number" class="form-control" placeholder="Registration Number">
                                                            </div>
                                                       </div>

                                                       <!-- GST Number -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-gst" class="form-label text-dark">GST Number</label>
                                                                 <input type="text" id="branch-gst" name="branch_gst" class="form-control" placeholder="GST Number">
                                                            </div>
                                                       </div>

                                                       <!-- State Code -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-state-code" class="form-label text-dark">State Code</label>
                                                                 <input type="text" id="branch-state-code" name="branch_state_code" class="form-control" placeholder="State Code">
                                                            </div>
                                                       </div>

                                                       <!-- Bank Name -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-bank-name" class="form-label text-dark">Bank Name</label>
                                                                 <input type="text" id="branch-bank-name" name="branch_bank_name" class="form-control" placeholder="Bank Name">
                                                            </div>
                                                       </div>

                                                       <!-- Account Number -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-account-number" class="form-label text-dark">Account Number</label>
                                                                 <input type="text" id="branch-account-number" name="branch_account_number" class="form-control" placeholder="Account Number">
                                                            </div>
                                                       </div>

                                                       <!-- IFSC Code -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-ifsc-code" class="form-label text-dark">IFSC Code</label>
                                                                 <input type="text" id="branch-ifsc-code" name="branch_ifsc_code" class="form-control" placeholder="IFSC Code">
                                                            </div>
                                                       </div>

                                                       <!-- Address -->
                                                       <div class="col-lg-8">
                                                            <div class="mb-3">
                                                                 <label for="branch-address" class="form-label text-dark">Address</label>
                                                                 <textarea id="branch-address" name="branch_address" class="form-control" placeholder="Branch Address" rows="1"></textarea>
                                                            </div>
                                                       </div>

                                                       <!-- Terms & Conditions -->
                                                       <div class="col-lg-12">
                                                            <div>
                                                                 <label for="terms-condition" class="form-label">Terms & Conditions</label>
                                                                 <div id="term-conditions-editor" class="editor" style="height: 150px;"></div>
                                                                 <textarea name="branch_term_and_condition" id="terms-conditions" style="display: none;"></textarea>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>

                                             <div class="card-footer border-top">
                                                  <button type="submit" name="add_branch" class="btn btn-primary">Add Branch</button>
                                             </div>
                                        </form>
                                   </div>
                              </div>
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

          <!-- Required JQuery -->
          <script src="../assets/js/jquery-3.7.1.min.js"></script>

          <script>
               // jQuery to show/hide branch name input field based on selected company
               $(document).ready(function() {
                    // When the company dropdown changes
                    $('#branch_of_company').on('change', function() {
                         var selectedCompany = $(this).val(); // Get the selected company

                         // If a company is selected, show the branch name input field
                         if (selectedCompany !== "") {
                              $('#branch-container').show(); // Show the branch input field container
                              $('#branch_name').prop('required', true); // Make branch name field required
                         } else {
                              $('#branch-container').hide(); // Hide the branch input field container
                              $('#branch_name').prop('required', false); // Remove the required attribute from branch name field
                         }
                    });
               });
          </script>

          <!-- Quill Editor JS -->
          <script>
               var termsConditionsQuill = new Quill('#term-conditions-editor', {
                    theme: 'snow',
                    modules: {
                         toolbar: [
                              [{
                                   'header': [false, 1, 2, 3, 4, 5, 6]
                              }, 'blockquote', 'code-block'],
                              ['bold', 'italic', 'underline', 'strike'],
                              [{
                                   'color': []
                              }, {
                                   'background': []
                              }],
                              [{
                                   'script': 'super'
                              }, {
                                   'script': 'sub'
                              }],
                              [{
                                   'list': 'ordered'
                              }, {
                                   'list': 'bullet'
                              }],
                              ['direction', {
                                   'align': []
                              }],
                              ['link'],
                              ['clean']
                         ]
                    }
               });

               function submitForm() {
                    var termsConditionsContent = termsConditionsQuill.root.innerHTML;
                    document.getElementById('terms-conditions').value = termsConditionsContent;
                    return true;
               }
          </script>
</body>

</html>

<?php
$conn->close();
?>