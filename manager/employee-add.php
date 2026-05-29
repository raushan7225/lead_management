<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


############ Initialize error variables ############
$error_contact = "";  // Initialize the error contact
$error_username = "";  // Initialize the error username
$error_user_add = "";  // Initialize the error user
############ Initialize error variables ############

if (isset($_POST['add_employee'])) {
    ########### Genrate Unique ID #############
    function generateId()
    {
        $prefix = '#ID';
        $randomNumber = rand(1, 9999999);
        return $prefix . $randomNumber;
    }
    $uniqueId = generateId();
    ########### Genrate Unique ID #############

    // First check user limit for the company
    $checkUserLimit = "SELECT `user_limit`, (SELECT COUNT(*) FROM `employees` WHERE `employee_of_company` = '$Company') AS user_count FROM `companies` WHERE `company_name` = '$Company'";
    $userLimitResult = mysqli_query($conn, $checkUserLimit);

    if (mysqli_num_rows($userLimitResult) > 0) {
        $limitData = mysqli_fetch_assoc($userLimitResult);
        $userLimit = $limitData['user_limit'];
        $currentUsers = $limitData['user_count'];

        if ($userLimit !== "Unlimited" && $currentUsers >= $userLimit) {
            $error_user_add = "Error: User limit reached for this company! Please contact to admin.";
        }
    }

    // Only proceed if user limit hasn't been reached
    if (empty($error_user_add)) {
        $employee_contact = $_POST['employee_contact'];
        $employee_personal_contact = $_POST['employee_personal_contact'];
        $employee_id = $uniqueId;
        $employee_of_company = $Company;
        $employee_of_branch = $Branch;
        $employee_name = $_POST['employee_name'];
        $employee_email = $_POST['employee_email'];
        $employee_dob = $_POST['employee_dob'];
        $employee_username = $_POST['employee_username'];
        $employee_password = $_POST['employee_password'];
        $employee_address = $_POST['employee_address'];
        $employee_father_name = $_POST['employee_father_name'];
        $employee_father_phone = $_POST['employee_father_phone'];
        $employee_bank_name = $_POST['employee_bank_name'];
        $employee_bank_account = $_POST['employee_bank_account'];
        $employee_bank_ifsc = $_POST['employee_bank_ifsc'];
        $employee_adhar_number = $_POST['employee_adhar_number'];
        $employee_user_role = $_POST['employee_user_role'];
        $created_by = $username;
        $employee_status = 1;

        $checkContact = "SELECT `employee_contact`, `employee_username` FROM `employees` WHERE `employee_contact` = '$employee_contact' OR `employee_username`='$employee_username'";
        $checkContactQuery = mysqli_query($conn, $checkContact);

        if (mysqli_num_rows($checkContactQuery) > 0) {
            while ($row = mysqli_fetch_assoc($checkContactQuery)) {
                if ($row['employee_contact'] == $employee_contact) {
                    $error_contact = "Error: This contact already exists! Please try another contact.";
                }
                if ($row['employee_username'] == $employee_username) {
                    $error_username = "Error: This username already exists! Please try another username.";
                }
            }
        } else {
            $addEmployee = "INSERT INTO `employees`(`employee_id`, `employee_name`, `employee_address`, `employee_email`, `employee_contact`, `employee_personal_contact`, `employee_of_company`, `employee_of_branch`, `employee_dob`, `employee_user_role`, `employee_username`, `employee_user_password`, `employee_father_name`, `employee_father_phone`, `employee_bank_name`, `employee_bank_account`, `employee_bank_ifsc`, `employee_adhar_number`, `employee_status`, `created_by`) VALUES ('$employee_id','$employee_name','$employee_address','$employee_email','$employee_contact','$employee_personal_contact','$employee_of_company','$employee_of_branch','$employee_dob','$employee_user_role','$employee_username','$employee_password','$employee_father_name','$employee_father_phone','$employee_bank_name','$employee_bank_account','$employee_bank_ifsc','$employee_adhar_number','$employee_status','$created_by')";
            $addEmployeeQuery = mysqli_query($conn, $addEmployee);

            ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "New Employee " . $employee_name . " has been added by manager " . $username . " of " . $Branch . " branch.";
            $activity_type = 'Alert';
            $activity_company = $Company;
            $activity_branch = $Branch;
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################


            if ($addEmployeeQuery) {
                echo "<script>window.location.href = 'employees-list.php';</script>";
            } else {
                $error_user_add = "Error: Unable to add user. Please try again.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Add Employee</title>
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

            <!-- Start Container Fluid -->
            <div class="container-xxl">
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <!-- Show error message here -->
                        <?php if ($error_contact): ?>
                            <div class="alert alert-danger alert-dismissible fade show overflow-hidden rounded-3" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <?php echo $error_contact; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Show error message here -->
                        <?php if ($error_username): ?>
                            <div class="alert alert-danger alert-dismissible fade show overflow-hidden rounded-3" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <?php echo $error_username; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Show error message here -->
                        <?php if ($error_user_add): ?>
                            <div class="alert alert-danger alert-dismissible fade show overflow-hidden rounded-3" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <?php echo $error_user_add; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <form action="#" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Update Employee Details</h4>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <!-- employee_name -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_name" class="form-label text-dark">Personal Employee Name <small class="text-success">(Required)</small></label>
                                                <input type="text" id="employee_name" name="employee_name" class="form-control" placeholder="Employee Name" required>
                                            </div>
                                        </div>

                                        <!-- Employee Office Contact -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_contact" class="form-label text-dark">Office Contact Number <small class="text-success">(Required)</small></label>
                                                <input type="tel" id="employee_contact" name="employee_contact" class="form-control" placeholder="Office Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}" required>
                                            </div>
                                        </div>


                                        <!-- Employee Personal Contact -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_personal_contact" class="form-label text-dark">Personal Contact Number </label>
                                                <input type="tel" id="employee_personal_contact" name="employee_personal_contact" class="form-control" placeholder="Office Contact Number" minlength="10" maxlength="10" pattern="[0-9]{10}">
                                            </div>
                                        </div>

                                        <!-- Employee Email -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_email" class="form-label text-dark">Employee Email</label>
                                                <input type="email" id="employee_email" name="employee_email" class="form-control" placeholder="Employee Email">
                                            </div>
                                        </div>

                                        <!-- Employee DOB -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_dob" class="form-label text-dark">Employee D.O.B</label>
                                                <input type="date" id="employee_dob" name="employee_dob" class="form-control" placeholder="Employee D.O.B">
                                            </div>
                                        </div>

                                        <!-- Employee Username -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_username" class="form-label text-dark"> Username <small class="text-success">(Required)</small></label>
                                                <input type="text" id="employee_username" name="employee_username" class="form-control" placeholder="Employee Username" required>
                                            </div>
                                        </div>

                                        <!-- Employee Password -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_password" class="form-label text-dark">Password <small class="text-success">(Required)</small></label>
                                                <input type="text" id="employee_password" name="employee_password" class="form-control" placeholder="Password" required>
                                            </div>
                                        </div>


                                        <!-- Employee Role -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_user_role" class="form-label text-dark">Role <small class="text-success">(Required)</small></label>
                                                <select class="form-control" name="employee_user_role" id="employee_user_role" data-choices data-choices-search="false" required>
                                                    <option value="Staff" <?php echo ($user_data['employee_user_role'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                                                    <option value="Technician" <?php echo ($user_data['employee_user_role'] == 'Technician') ? 'selected' : ''; ?>>Technician</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Additional Information</h4>
                                </div>

                                <div class="card-body">
                                    <div class="row">


                                        <!-- Employee Father Name -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_father_name" class="form-label text-dark">Father's Name</label>
                                                <input type="text" id="employee_father_name" name="employee_father_name" class="form-control" placeholder="Father's Name">
                                            </div>
                                        </div>

                                        <!-- Employee Father Name -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_father_phone" class="form-label text-dark">Father's Phone No.</label>
                                                <input type="tel" id="employee_father_phone" name="employee_father_phone" class="form-control" placeholder="Father's Phone No." minlength="10" maxlength="10" maxlength="10" pattern="[0-9]{10}">
                                            </div>
                                        </div>

                                        <!-- Employee Bank Name -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_bank_name" class="form-label text-dark">Bank Name</label>
                                                <input type="text" id="employee_bank_name" name="employee_bank_name" class="form-control" placeholder="Bank Name">
                                            </div>
                                        </div>

                                        <!-- Employee Bank Account Number -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_bank_account" class="form-label text-dark">Account Number</label>
                                                <input type="text" id="employee_bank_account" name="employee_bank_account" class="form-control" placeholder="Account Number">
                                            </div>
                                        </div>

                                        <!-- Employee Bank IFSC Code -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_bank_ifsc" class="form-label text-dark">IFSC Code</label>
                                                <input type="text" id="employee_bank_ifsc" name="employee_bank_ifsc" class="form-control" placeholder="IFSC Code">
                                            </div>
                                        </div>

                                        <!-- Employee Adhar Number -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_adhar_number" class="form-label text-dark">Adhar Number</label>
                                                <input type="text" id="employee_adhar_number" name="employee_adhar_number" class="form-control" placeholder="Adhar Number" minlength="12" maxlength="12">
                                            </div>
                                        </div>

                                        <!-- Employee Address -->
                                        <div class="col-lg-12">
                                            <label for="employee_address" class="form-label text-dark">Address</label>
                                            <textarea type="text" id="employee_address" name="employee_address" class="form-control" rows="2" placeholder="Address"></textarea>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light mb-3 rounded">
                        <div class="row justify-content-end g-2">
                            <div class="col-lg-2">
                                <button type="submit" name="add_employee" class="btn btn-outline-secondary w-100">Create Employee</button>
                            </div>
                            <div class="col-lg-2">
                                <a href="employees-list.php" class="btn btn-primary w-100">Cancel</a>
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
        document.getElementById('employee_dob').flatpickr();
    </script>
</body>

</html>