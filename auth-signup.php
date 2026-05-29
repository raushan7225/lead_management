<?php
include("config/db.php");

################### Show Braanches ######################
$Branches = "SELECT * FROM `branches` WHERE `branch_status` = '1'";
$BranchesQuery = mysqli_query($conn, $Branches);
$branchename = [];
if (mysqli_num_rows($BranchesQuery) > 0) {
     while ($branch = mysqli_fetch_array($BranchesQuery)) {
          $branchename[] = $branch;
     }
}
################### Show Braanches ######################

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
     <title>Signup As Admin</title>
     <?php include("head.php"); ?>
</head>

<body>
     <!-- ============= Signup Form ============== -->
     <div class="d-flex flex-column p-2">
          <div class="d-flex flex-column flex-grow-1">
               <div class="col-xxl-12">
                    <div class="row justify-content-center">
                         <div class="col-lg-4 py-lg-4-3">
                              <div class="d-flex flex-column justify-content-center full-height p-2">
                                   <!-- <div class="auth-logo mb-2">
                                        <a href="index.php" class="logo-dark">
                                             <img src="assets/images/others/logo-dark.png" height="50" alt="logo dark">
                                        </a>

                                        <a href="index.php" class="logo-light">
                                             <img src="assets/images/others/logo-light.png" height="50" alt="logo light">
                                        </a>
                                   </div> -->

                                    <h2 class="fw-bold fs-24">Create an account as <span class="text-primary">"Admin"</span> !</h2>

                                    <div class="mt-2">
                                         <?php if (!empty($error)): ?>
                                              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                         <?php endif; ?>
                                         <?php if (!empty($success)): ?>
                                              <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                                         <?php endif; ?>
                                         <form action="" class="authentication-form" method="POST">
                                             <div class="mb-2">
                                                  <label class="form-label" for="username">Username <small class="text-success">(Required)</small></label>
                                                  <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                                             </div>

                                             <!-- ============== Branch ============= -->
                                             <div class="mb-2">
                                                  <label for="user_branch" class="form-label"> Branch <small class="text-success">(Required)</small></label>
                                                  <select class="form-control" id="user_branch" name="user_branch" data-choices required>
                                                       <option value="">Select Branch </option>
                                                       <?php foreach ($branchename as $mybranch) { ?>
                                                            <option value="<?php echo $mybranch['branch_name']; ?>"><?php echo $mybranch['branch_name']; ?></option>
                                                       <?php } ?>
                                                  </select>
                                             </div>
                                             <!-- =======x====== Branch =======x===== -->

                                             <div class="mb-3">
                                                  <label class="form-label" for="password">Password <small class="text-success">(Required)</small></label>
                                                  <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                                             </div>

                                             <div class="mb-2 text-center d-grid">
                                                  <button type="submit" class="btn btn-soft-primary" value="signup" name="signup">Create Account</button>
                                             </div>
                                        </form>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
     <!-- =======x====== Signup Form =======x======= -->

     <!-- Vendor Javascript (Require in all Page) -->
     <script src="assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="assets/js/app.js"></script>

</body>

</html>

<?php
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     // Check if the form is submitted
     if (isset($_POST['signup'])) {
          // Get the form data
          $employee_username = trim($_POST['username']);
          $employee_branch = trim($_POST['user_branch']);
          $employee_user_password = $_POST['password'];
          $employee_status = 1;  // Active user
          $employee_user_role = "Admin";  // User role

          // Validate inputs
          if (empty($employee_username) || empty($employee_branch) || empty($employee_user_password)) {
               $error = "All fields are required.";
          } else {
               // Function to generate a unique account ID
               function generateAccountId()
               {
                    $prefix = '#ID';
                    $randomNumber = rand(1, 99999);
                    return $prefix . $randomNumber;
               }
               $newAccountId = generateAccountId();

               // Check if username already exists in the database
               $stmt = $conn->prepare("SELECT * FROM `employees` WHERE `employee_username` = ?");
               
               if ($stmt) {
                    $stmt->bind_param("s", $employee_username);
                    $stmt->execute();
                    $stmt->store_result();
                    
                    if ($stmt->num_rows > 0) {
                         $error = "Username already found. Choose another username!";
                    } else {
                         // Hash the password before storing
                         $hashed_password = password_hash($employee_user_password, PASSWORD_DEFAULT);
                         
                         // Create new user with prepared statement
                         $insert_stmt = $conn->prepare("INSERT INTO `employees` (`employee_id`, `employee_username`, `employee_branch`, `employee_user_password`, `employee_status`, `employee_user_role`) VALUES (?, ?, ?, ?, ?, ?)");
                         
                         if ($insert_stmt) {
                              $insert_stmt->bind_param("ssssis", $newAccountId, $employee_username, $employee_branch, $hashed_password, $employee_status, $employee_user_role);
                              
                              if ($insert_stmt->execute()) {
                                   $success = "Account created successfully! You can now sign in.";
                              } else {
                                   $error = "Failed to create user.";
                              }
                              $insert_stmt->close();
                         } else {
                              $error = "Database error. Please try again later.";
                         }
                    }
                    $stmt->close();
               } else {
                    $error = "Database error. Please try again later.";
               }
          }
     }
}
?>