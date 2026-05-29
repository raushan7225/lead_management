<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

######### Fetch all branches from the database #########
$branch_list = "SELECT * FROM `branches` WHERE `branch_status` = '1'";
$branch_list_result = $conn->query($branch_list);
$branchename = [];
if (mysqli_num_rows($branch_list_result) > 0) {
    while ($branch = $branch_list_result->fetch_assoc()) {
        $branchename[] = $branch;
    }
}
######### Fetch all branches from the database #########


################### Branch and Company Selection ###################
if (isset($_POST['lead_for_branch'])) {
    $lead_for_branch = $_POST['lead_for_branch'];
} else {
    $lead_for_branch = '';
}

if ($lead_for_branch) {
    $FindCompany = "SELECT * FROM `branches` WHERE `branch_name`='$lead_for_branch' AND `branch_status` = '1'";
    $FindCompanyResult = $conn->query($FindCompany);
    if (mysqli_num_rows($FindCompanyResult) > 0) {
        while ($CompanyData = $FindCompanyResult->fetch_assoc()) {
            $MyCompany = $CompanyData['branch_of_company'];
            $MyBranch = $CompanyData['branch_name'];

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

                $employee_contact = $_POST['employee_contact'];
                $employee_personal_contact = $_POST['employee_personal_contact'];

                $error_contact = "";
                $error_user_add = "";
                $checkContact = "SELECT `employee_contact` FROM `employees` WHERE `employee_contact` = '$employee_contact'";
                $checkContactQuery = mysqli_query($conn, $checkContact);
                if (mysqli_num_rows($checkContactQuery) > 0) {
                    $error_contact = "Error: This contact already exists! Maximum $userLimit users allowed.";
                } else {
                    $employee_id = $uniqueId;
                    $employee_of_company = $MyCompany;
                    $employee_of_branch = $MyBranch;
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


                    $CheckEmployee = "SELECT `employee_username` FROM `employees` WHERE `employee_username`='$employee_username'";
                    $CheckEmployeeQuery = mysqli_query($conn, $CheckEmployee);

                    if (mysqli_num_rows($CheckEmployeeQuery) > 0) {
                        echo "<script>window.location.href = 'employees-list.php';</script>";
                    } else {
                        $addEmployee = "INSERT INTO `employees`(`employee_id`, `employee_name`, `employee_address`, `employee_email`, `employee_contact`, `employee_personal_contact`, `employee_of_company`, `employee_of_branch`, `employee_dob`, `employee_user_role`, `employee_username`, `employee_user_password`, `employee_father_name`, `employee_father_phone`, `employee_bank_name`, `employee_bank_account`, `employee_bank_ifsc`, `employee_adhar_number`, `employee_status`, `created_by`) VALUES ('$employee_id','$employee_name','$employee_address','$employee_email','$employee_contact','$employee_personal_contact','$employee_of_company','$employee_of_branch','$employee_dob','$employee_user_role','$employee_username','$employee_password','$employee_father_name','$employee_father_phone','$employee_bank_name','$employee_bank_account','$employee_bank_ifsc','$employee_adhar_number','$employee_status','$created_by')";
                        $addEmployeeQuery = mysqli_query($conn, $addEmployee);

                        if ($addEmployeeQuery) {
                            ################# Push activity ################
                            $activity_id = $guid;
                            $activity_content = "The employee " . $employee_name . " has been added by " . $Role . ".";
                            $activity_type = 'Alert';
                            $activity_company = "";
                            $activity_branch = "";
                            $activity_on = date('Y-m-d H:i:s');
                            $activity_by = $username;
                            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                            ################# Push activity ################
                            echo "<script>window.location.href = 'employees-list.php';</script>";
                        } else {
                            $error_user_add = "Error: Something went wrong. Please try again.";
                        }
                    }
                }
            }
        }
    }
}
################### Branch and Company Selection ###################

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

        <!-- Start right Content here -->
        <div>
            <div class="page-content">

                <!-- Start Container Fluid -->
                <div class="container-xxl">
                    <form action="#" method="POST" enctype="multipart/form-data" onsubmit="return submitForm()">
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- Show error message here -->
                                <?php if ($error_contact): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        <?php echo $error_contact; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Show error message here -->
                                <?php if ($error_user_add): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        <?php echo $error_user_add; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Update Employee Details</h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Employee Branch -->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="lead_for_branch" class="form-label">Branch <small class="text-success">(Required)</small></label>
                                                    <select class="form-control" id="lead_for_branch" name="lead_for_branch" data-choices onchange="this.form.submit()" required>
                                                        <option value="">All Branches</option>
                                                        <?php foreach ($branchename as $mybranch) { ?>
                                                            <option value="<?php echo $mybranch['branch_name']; ?>"
                                                                <?php echo (isset($lead_for_branch) && $mybranch['branch_name'] == $lead_for_branch) ? 'selected' : ''; ?>>
                                                                <?php echo $mybranch['branch_name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

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
                                                        <option value="Super Admin" <?php echo ($user_data['employee_user_role'] == 'Super Admin') ? 'selected' : ''; ?>>1. Super Admin</option>
                                                        <option value="Admin" <?php echo ($user_data['employee_user_role'] == 'Admin') ? 'selected' : ''; ?>>2. Admin</option>
                                                        <option value="Manager" <?php echo ($user_data['employee_user_role'] == 'Manager') ? 'selected' : ''; ?>>3. Manager</option>
                                                        <option value="Staff" <?php echo ($user_data['employee_user_role'] == 'Staff') ? 'selected' : ''; ?>>4. Staff</option>
                                                        <option value="Technician" <?php echo ($user_data['employee_user_role'] == 'Technician') ? 'selected' : ''; ?>>5. Technician</option>
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
                                                <div class="mb-3">
                                                    <label for="employee_address" class="form-label text-dark">Address</label>
                                                    <textarea type="text" id="employee_address" name="employee_address" class="form-control" rows="5" placeholder="Address"></textarea>
                                                </div>
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