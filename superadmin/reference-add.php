<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################### Show Companies ######################
$Companies = "SELECT * FROM `companies` WHERE `company_status` = '1'";
$CompaniesQuery = mysqli_query($conn, $Companies);
$companyname = [];
if (mysqli_num_rows($CompaniesQuery) > 0) {
     while ($company = mysqli_fetch_array($CompaniesQuery)) {
          $companyname[] = $company;
     }
}
################### Show Companies ######################


################# Add Reference ###################
if (isset($_POST["add_reference"])) {

     ########### Genrate Unique ID #############
     function generateId()
     {
          $prefix = '#ID';
          $randomNumber = rand(1, 9999999);
          return $prefix . $randomNumber;
     }
     $uniqueId = generateId();
     ########### Genrate Unique ID #############

     $reference_id = $uniqueId;
     $reference_name = $_POST['reference_name'];
     $reference_contact = $_POST['reference_contact'];
     $reference_for_company = $_POST['company_name'];
     $reference_createdby = $username;
     $reference_status = 1;

     $add_Reference = "INSERT INTO `references` (`reference_id`, `reference_name`, `reference_contact`, `reference_for_company`, `reference_createdby`, `reference_status`) VALUES ('$reference_id', '$reference_name', '$reference_contact', '$reference_for_company', '$reference_createdby', '$reference_status')";
     $add_Reference_query = mysqli_query($conn, $add_Reference);


     if ($add_Reference_query) {
          ################# Push activity ################
          $activity_id = $guid;
          $activity_content = "New reference " . $reference_name . " has been added by " . $Role . ".";
          $activity_type = 'Alert';
          $activity_company = "";
          $activity_branch = "";
          $activity_on = date('Y-m-d H:i:s');
          $activity_by = $username;
          $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
          $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
          ################# Push activity ################
          echo "<script>window.location.href='references-list.php';</script>";
     } else {
          echo "<script>alert('Error adding reference!');</script>";
     }
}
################# Add Reference ###################
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
                         <form action="#" method="POST" enctype="multipart/form-data">
                              <div class="card">
                                   <div class="card-header">
                                        <h4 class="card-title">Edit Reference</h4>
                                   </div>

                                   <div class="card-body">
                                        <div class="row">
                                             <!-- Employee Company -->
                                             <div class="col-lg-4">
                                                  <div class="mb-3">
                                                       <label for="company_name" class="form-label text-dark">Select a Company <small class="text-success">(Required)</small></label>
                                                       <select name="company_name" id="company_name" class="form-control" data-choices required>
                                                            <option value=""> All Companies </option>
                                                            <?php foreach ($companyname as $company): ?>
                                                                 <option value="<?php echo $company['company_name']; ?>"
                                                                      <?php echo ($company['company_name'] == $ofCompany) ? 'selected' : ''; ?>>
                                                                      <?php echo $company['company_name']; ?>
                                                                 </option>
                                                            <?php endforeach; ?>
                                                       </select>
                                                  </div>
                                             </div>

                                             <!-- Reference Name -->
                                             <div class="col-lg-4">
                                                  <div class="mb-3">
                                                       <label for="reference_name" class="form-label text-dark">Reference Name <small class="text-success">(Required)</small></label>
                                                       <input type="text" id="reference_name" name="reference_name" class="form-control" placeholder="Reference Name" value="<?php echo $reference_name; ?>" required>
                                                  </div>
                                             </div>

                                             <!-- Reference Phone -->
                                             <div class="col-lg-4">
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
                                             <button type="submit" name="add_reference" class="btn btn-outline-secondary w-100">Update Reference</button>
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