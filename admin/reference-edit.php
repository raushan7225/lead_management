<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
     $id = $_GET['id'];

     $error = "";  // Initialize the error user
     ############ Fetch all Branch of the company ############
     $BranchList = "SELECT * FROM `branches` WHERE `branch_of_company` = '$Company' AND `branch_status` = '1'";
     $BranchListResult = mysqli_query($conn, $BranchList);
     $BranchData = [];

     if (mysqli_num_rows($BranchListResult) > 0) {
          while ($Branches = mysqli_fetch_assoc($BranchListResult)) {
               $BranchData[] = $Branches;
          }
     }
     ############ Fetch all Branch of the company ############


     #################### Fetch All references #######################
     $reference_list = "SELECT * FROM `references` WHERE `id` = '$id'";
     $reference_list_result = mysqli_query($conn, $reference_list);
     if (mysqli_num_rows($reference_list_result) > 0) {
          while ($reference = $reference_list_result->fetch_assoc()) {
               $reference_name = $reference['reference_name'];
               $reference_contact = $reference['reference_contact'];
               $reference_for_company = $Company;
          }
     }
     #################### Fetch All references #######################


     #################### Update the references ######################
     if (isset($_POST['update_reference'])) {
          $reference_name = $_POST['reference_name'];
          $reference_contact = $_POST['reference_contact'];
          $reference_for_company = $Company;

          $update_reference = "UPDATE `references` SET `reference_name` = '$reference_name', `reference_contact` = '$reference_contact', `reference_for_company`='$reference_for_company', `reference_updatedby`='$username' WHERE `id` = '$id'";
          $update_reference_query = mysqli_query($conn, $update_reference);

          if ($update_reference_query) {
               ################# Push activity ################
               $activity_id = $guid;
               $activity_content = "Details of referrence " . $reference_name . " has been updated by admin " . $username . " of " . $Company . " company.";
               $activity_type = 'Alert';
               $activity_company = $Company;
               $activity_branch = "";
               $activity_on = date('Y-m-d H:i:s');
               $activity_by = "$username";
               $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
               $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
               ################# Push activity ################

               echo "<script>window.location.href='references-list.php';</script>";
          } else {
               $error = "Error: Something went wrong. Please try again after sometime.";
          }
     }
     #################### Update the references ######################
} else {
     echo "<script>window.location.href='references-list.php';</script>";
     exit;
}
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
     <title>Edit Reference</title>
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

                         <!-- Show error message here -->
                         <?php if ($error): ?>
                              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                   <?php $error; ?>
                              </div>
                         <?php endif; ?>

                         <form action="#" method="POST" enctype="multipart/form-data">
                              <div class="card">
                                   <div class="card-header">
                                        <h4 class="card-title">Edit Reference</h4>
                                   </div>

                                   <div class="card-body">
                                        <div class="row">

                                             <!-- Reference Name -->
                                             <div class="col-lg-6">
                                                  <div class="mb-3">
                                                       <label for="reference_name" class="form-label text-dark">Reference Name <small class="text-success">(Required)</small></label>
                                                       <input type="text" id="reference_name" name="reference_name" class="form-control" placeholder="Reference Name" value="<?php echo $reference_name; ?>" required>
                                                  </div>
                                             </div>

                                             <!-- Reference Phone -->
                                             <div class="col-lg-6">
                                                  <div class="mb-3">
                                                       <label for="reference_contact" class="form-label text-dark">Phone No.</label>
                                                       <input type="tel" id="reference_contact" name="reference_contact" class="form-control" placeholder="Phone No." value="<?php echo $reference_contact; ?>" minlength="10" maxlength="10" pattern="[0-9]{10}">
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>

                              <div class="p-3 bg-light mb-3 rounded">
                                   <div class="row justify-content-end g-2">
                                        <div class="col-lg-2">
                                             <button type="submit" name="update_reference" class="btn btn-outline-secondary w-100">Update Reference</button>
                                        </div>
                                        <div class="col-lg-2">
                                             <a href="references-list.php" class="btn btn-primary w-100">Cancel</a>
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
     </div>
     <!-- END Wrapper -->

     <!-- Vendor Javascript (Require in all Page) -->
     <script src="../assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="../assets/js/app.js"></script>
</body>

</html>
<?php $conn->close(); ?>