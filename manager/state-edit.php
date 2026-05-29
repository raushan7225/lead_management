<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
     $stateId = $_GET['id'];

     ############### Fetch all states from the database ##############
     $state_list = "SELECT * FROM `states` WHERE `id` = '$stateId'";
     $state_list_result = mysqli_query($conn, $state_list);
     if (mysqli_num_rows($state_list_result) > 0) {
          while ($state = mysqli_fetch_array($state_list_result)) {
               $state_name = $state['state_name'];
          }
     }
     ############### Fetch all states from the database ##############


     ######################### Update State ##########################
     if (isset($_POST['update_state'])) {
          $state_name = $_POST['state_name'];
          $update_state = "UPDATE `states` SET `state_name`='$state_name' WHERE `id`='$stateId'";
          $update_state_query = mysqli_query($conn, $update_state);

          if ($update_state_query) {
               echo "<script>window.location.href='state-list.php';</script>";
          } else {
               echo "<script>alert('Error updating state!')</script>";
          }
     }
     ######################### Update State ##########################
} else {
     echo "<script>window.location.href='state-list.php';</script>";
     exit;
}
?>


<!DOCTYPE html>
<html lang="en-US">

<head>
     <title>Edit State</title>
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

          <!-- ==================================================== -->
          <!-- Start right Content here -->
          <!-- ==================================================== -->
          <div class="page-content">
               <div class="container-xxl">
                    <div class="row">
                         <div class="col-lg-12">
                              <div class="card">
                                   <div class="card-header">
                                        <h4 class="card-title">Edit State</h4>
                                   </div>
                                   <form action="#" method="POST" enctype="multipart/form-data">
                                        <div class="card-body">
                                             <div class="row">
                                                  <!-- State Name -->
                                                  <div class="col-lg-12">
                                                       <div class="mb-3">
                                                            <input type="text" id="state_name" name="state_name" class="form-control" placeholder="State Name" value="<?php echo $state_name; ?>" required>
                                                       </div>
                                                  </div>

                                                  <div class="col-lg-2">
                                                       <button type="submit" name="update_state" class="btn btn-primary">Update State</button>
                                                  </div>
                                             </div>
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