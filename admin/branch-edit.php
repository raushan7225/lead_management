<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
     $id = $_GET['id'];

     ################# Fetch all branches from the database ###################
     $branch_list_query = "SELECT * FROM `branches` WHERE `branch_of_company`='$Company' AND `branch_status` = '1' AND `id` = '$id'";
     $branch_list_result = mysqli_query($conn, $branch_list_query);
     $branch = mysqli_fetch_assoc($branch_list_result);
     $branch_name = $branch["branch_name"];
     $branch_address = $branch["branch_address"];
     $branch_state_code = $branch["branch_state_code"];
     $branch_gst = $branch["branch_gst"];
     $branch_contact = $branch["branch_contact"];
     $branch_email = $branch["branch_email"];
     $branch_registration_number = $branch["branch_registration_number"];
     $branch_bank_name = $branch["branch_bank_name"];
     $branch_bank_branch = $branch["branch_bank_branch"];
     $branch_account_number = $branch["branch_account_number"];
     $branch_ifsc_code = $branch["branch_ifsc_code"];
     $branch_term_and_condition = $branch["branch_term_and_condition"];
     ################# Fetch all branches from the database ###################
?>

     <!DOCTYPE html>
     <html lang="en-US">

     <head>
          <title>Edit Branch Details</title>
          <?php include("head.php"); ?>
     </head>

     <body>
          <!-- Loader -->
          <div id="loader-wrapper">
               <div class="loader"></div>
          </div>

          <!-- Wrapper Start -->
          <div class="wrapper">

               <!-- ========== Topbar Start ========== -->
               <?php include("topnav.php"); ?>
               <!-- ========== Topbar End ========== -->

               <!-- ========== App Menu Start ========== -->
               <?php include("sidenav.php"); ?>
               <!-- ========== App Menu End ========== -->

               <!-- Content Start -->
               <div class="page-content">
                    <div class="container-xxl">
                         <div class="row">
                              <div class="col-lg-12">
                                   <div class="card">
                                        <div class="card-header">
                                             <h4 class="card-title">Edit Branch Details</h4>
                                        </div>
                                        <form action="#" method="POST" enctype="multipart/form-data" onsubmit="return submitForm()">
                                             <div class="card-body">
                                                  <div class="row">

                                                       <!-- Branch Name -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-name" class="form-label text-dark">Branch Name <small class="text-success">(Required)</small></label>
                                                                 <input type="text" id="branch-name" name="branch_name" class="form-control" placeholder="Branch Name" value="<?php echo $branch_name; ?>" required>
                                                            </div>
                                                       </div>

                                                       <!-- Contact Number -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-contact" class="form-label text-dark">Contact Number <small class="text-success">(Required)</small></label>
                                                                 <input type="tel" id="branch-contact" name="branch_contact" class="form-control" placeholder="Contact Number" minlength="10" maxlength="10" value="<?php echo $branch_contact; ?>" required>
                                                            </div>
                                                       </div>

                                                       <!-- Email ID -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-email" class="form-label text-dark">Email ID</label>
                                                                 <input type="email" id="branch-email" name="branch_email" class="form-control" placeholder="Email ID" value="<?php echo $branch_email; ?>">
                                                            </div>
                                                       </div>

                                                       <!-- Registration Number -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-registration" class="form-label text-dark">Registration Number</label>
                                                                 <input type="text" id="branch-registration" name="branch_registration_number" class="form-control" placeholder="Registration Number" value="<?php echo $branch_registration_number; ?>">
                                                            </div>
                                                       </div>

                                                       <!-- GST Number -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-gst" class="form-label text-dark">GST Number</label>
                                                                 <input type="text" id="branch-gst" name="branch_gst" class="form-control" placeholder="GST Number" value="<?php echo $branch_gst; ?>">
                                                            </div>
                                                       </div>

                                                       <!-- Bank Details -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-bank-name" class="form-label text-dark">Bank Name</label>
                                                                 <input type="text" id="branch-bank-name" name="branch_bank_name" class="form-control" placeholder="Bank Name" value="<?php echo $branch_bank_name; ?>">
                                                            </div>
                                                       </div>
                                                       <!-- Bank Details -->
                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-bank-branch" class="form-label text-dark">Bank Branch</label>
                                                                 <input type="text" id="branch-bank-branch" name="branch_bank_branch" class="form-control" placeholder="Bank Name" value="<?php echo $branch_bank_branch; ?>">
                                                            </div>
                                                       </div>

                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-account-number" class="form-label text-dark">Account Number</label>
                                                                 <input type="text" id="branch-account-number" name="branch_account_number" class="form-control" placeholder="Account Number" value="<?php echo $branch_account_number; ?>">
                                                            </div>
                                                       </div>

                                                       <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                 <label for="branch-ifsc-code" class="form-label text-dark">IFSC Code</label>
                                                                 <input type="text" id="branch-ifsc-code" name="branch_ifsc_code" class="form-control" placeholder="IFSC Code" value="<?php echo $branch_ifsc_code; ?>">
                                                            </div>
                                                       </div>


                                                       <!-- Address -->
                                                       <div class="col-lg-12">
                                                            <label for="branch-address" class="form-label text-dark">Address</label>
                                                            <textarea id="branch-address" name="branch_address" class="form-control" placeholder="Branch Address" rows="3"><?php echo $branch_address; ?></textarea>
                                                       </div>
                                                  </div>
                                             </div>

                                             <div class="card-footer border-top">
                                                  <button type="submit" name="update_branch" class="btn btn-primary">Update Branch</button>
                                             </div>
                                        </form>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
               <!-- Content End -->

               <!-- Footer -->
               <?php include("../footer.php"); ?>
          </div>
          <!-- Wrapper End -->

          <!-- Vendor Javascript (Require in all Page) -->
          <script src="../assets/js/vendor.js"></script>

          <!-- App Javascript (Require in all Page) -->
          <script src="../assets/js/app.js"></script>

     </body>

     </html>

<?php
     if (isset($_POST['update_branch'])) {
          $branch_of_company = $Company;
          $branch_name = trim($_POST['branch_name']);
          $branch_contact = trim($_POST['branch_contact']);
          $branch_email = trim($_POST['branch_email']);
          $branch_registration_number = trim($_POST['branch_registration_number']);
          $branch_gst = trim($_POST['branch_gst']);
          $branch_bank_name = trim($_POST['branch_bank_name']);
          $branch_bank_branch = trim($_POST['branch_bank_branch']);
          $branch_account_number = trim($_POST['branch_account_number']);
          $branch_ifsc_code = trim($_POST['branch_ifsc_code']);
          $branch_address = trim($_POST['branch_address']);
          $branch_status = 1;

          $branch_update_details = "UPDATE `branches` SET `branch_of_company` = '$branch_of_company', `branch_name` = '$branch_name', `branch_address` = '$branch_address', `branch_gst` = '$branch_gst', `branch_contact` = '$branch_contact', `branch_email` = '$branch_email', `branch_registration_number` = '$branch_registration_number', `branch_bank_name` = '$branch_bank_name', `branch_bank_branch` = '$branch_bank_branch', `branch_account_number` = '$branch_account_number', `branch_ifsc_code` = '$branch_ifsc_code', `branch_status` = '$branch_status'  WHERE `id` = '$id'";
          $branch_update_details_apply = mysqli_query($conn, $branch_update_details);

          if ($branch_update_details_apply) {

               ################# Push activity ################
               $activity_id = $guid;
               $activity_content = "Details of branch" . $branch_name . " has been updated by admin " . $username . " of " . $Company . " company.";
               $activity_type = 'Alert';
               $activity_company = $Company;
               $activity_branch = "";
               $activity_on = date('Y-m-d H:i:s');
               $activity_by = $username;
               $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
               $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
               ################# Push activity ################

               echo "<script>window.location.href='branch-list.php';</script>";
          } else {
               echo "<script>alert('Branch Details Update Error!');</script>";
          }
     }
} else {
     echo "<script>window.location.href='branch-list.php';</script>";
     exit;
}
?>