<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    ###################### Show Users #######################
    $MyData = "SELECT * FROM `employees` WHERE `id` = '$id'";
    $MyDataQuery = mysqli_query($conn, $MyData);
    if (mysqli_num_rows($MyDataQuery) > 0) {
        while ($usersData = mysqli_fetch_assoc($MyDataQuery)) {
            $employee_name = $usersData['employee_name'];
            $employee_address = $usersData['employee_address'];
            $employee_email = $usersData['employee_email'];
            $employee_contact = $usersData['employee_contact'];
            $employee_personal_contact = $usersData['employee_personal_contact'];
            $employee_dob = $usersData['employee_dob'];
            $employee_of_company = $usersData['employee_of_company'];
            $employee_of_branch = $usersData['employee_of_branch'];
            $employee_user_role = $usersData['employee_user_role'];
            $employee_username = $usersData['employee_username'];
            $employee_user_password = $usersData['employee_user_password'];
            $employee_father_name = $usersData['employee_father_name'];
            $employee_father_phone = $usersData['employee_father_phone'];
            $employee_bank_name = $usersData['employee_bank_name'];
            $employee_bank_account = $usersData['employee_bank_account'];
            $employee_bank_ifsc = $usersData['employee_bank_ifsc'];
            $employee_adhar_number = $usersData['employee_adhar_number'];
        }
    }
    ##################### Show Users ######################


    ###################### Insert Lead ######################
    if (isset($_POST['update_employee'])) {
        $employee_contact = $_POST['employee_contact'];
        $employee_personal_contact = $_POST['employee_personal_contact'];
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
        $updated_by = $username;
        $employee_status = 1;

        // echo $employee_id . "<br>";
        // echo $employee_name . "<br>";
        // echo $employee_email . "<br>";
        // echo $employee_contact . "<br>";
        // echo $employee_dob . "<br>";
        // echo $employee_of_company . "<br>";
        // echo $employee_of_branch . "<br>";
        // echo $employee_username . "<br>";
        // echo $employee_password . "<br>";
        // echo $employee_address . "<br>";
        // echo $employee_father_name . "<br>";
        // echo $employee_father_phone . "<br>";
        // echo $employee_bank_name . "<br>";
        // echo $employee_bank_account . "<br>";
        // echo $employee_bank_ifsc . "<br>";
        // echo $employee_adhar_number . "<br>";
        // echo $employee_user_role . "<br>";
        // echo $employee_status . "<br>";
        // echo $updated_by . "<br>";


        $updateEmployee = "UPDATE `employees` SET 
        `employee_name`='$employee_name',
        `employee_address`='$employee_address',
        `employee_email`='$employee_email',
        `employee_contact`='$employee_contact',
        `employee_personal_contact`='$employee_personal_contact',
        `employee_dob`='$employee_dob',
        `employee_of_company`='$employee_of_company',
        `employee_of_branch`='$employee_of_branch',
        `employee_user_role`='$employee_user_role',
        `employee_username`='$employee_username',
        `employee_user_password`='$employee_password',
        `employee_father_name`='$employee_father_name',
        `employee_father_phone`='$employee_father_phone',
        `employee_bank_name`='$employee_bank_name',
        `employee_bank_account`='$employee_bank_account',
        `employee_bank_ifsc`='$employee_bank_ifsc',
        `employee_adhar_number`='$employee_adhar_number',
        `employee_status`='$employee_status',
        `updated_by`='$username' 
        WHERE `id` = '$id'";

        $error_user = "";  // Initialize the error user
        $addEmployeeQuery = mysqli_query($conn, $updateEmployee);

        ################# Push activity ################
        $activity_id = $guid;
        $activity_content = "Details of employee " . $employee_name . " has been updated by " . $username . " manager of " . $Branch . " branch.";
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
            // If contact exists, set the error message
            $error_user = "Error: This user not updated! Please try again.";
        }
    }
} else {
    echo "<script>window.location.href = 'employees-list.php';</script>";
}

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Employee Details Update</title>
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
                <form action="#" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-6">
                            <!-- Show error message here -->
                            <?php if ($error_user): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    <?php $error_user; ?>
                                </div>
                            <?php endif; ?>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Update Employee Details</h4>
                                </div>

                                <div class="card-body">
                                    <div class="row">

                                        <!-- Employee Name -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_name" class="form-label text-dark">Employee Name</label>
                                                <input type="text" id="employee_name" name="employee_name" class="form-control" placeholder="Employee Name" value="<?php echo $employee_name; ?>">
                                            </div>
                                        </div>

                                        <!-- Employee Contact -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_contact" class="form-label text-dark">Office Contact No. <small class="text-success">(Required)</small></label>
                                                <input type="tel" id="employee_contact" name="employee_contact" class="form-control" placeholder="Office Contact Number" value="<?php echo $employee_contact; ?>" minlength="10" maxlength="10" pattern="[0-9]{10}" required>
                                            </div>
                                        </div>

                                        <!-- Employee Personal Contact -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_personal_contact" class="form-label text-dark">Personal Contact No. </label>
                                                <input type="tel" id="employee_personal_contact" name="employee_personal_contact" class="form-control" placeholder="Personal Contact Number" value="<?php echo $employee_personal_contact; ?>" minlength="10" maxlength="10" pattern="[0-9]{10}">
                                            </div>
                                        </div>

                                        <!-- Employee Email -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_email" class="form-label text-dark">Employee Email</label>
                                                <input type="email" id="employee_email" name="employee_email" class="form-control" placeholder="Employee Email" value="<?php echo $employee_email; ?>">
                                            </div>
                                        </div>

                                        <!-- Employee DOB -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_dob" class="form-label text-dark">Employee D.O.B</label>
                                                <input type="date" id="employee_dob" name="employee_dob" class="form-control" placeholder="Employee D.O.B" value="<?php echo $employee_dob; ?>">
                                            </div>
                                        </div>

                                        <!-- Employee Username -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_username" class="form-label text-dark"> Username <small class="text-success">(Required)</small></label>
                                                <input type="text" id="employee_username" name="employee_username" class="form-control" placeholder="Employee Username" value="<?php echo $employee_username; ?>" required>
                                            </div>
                                        </div>

                                        <!-- Employee Password -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_password" class="form-label text-dark">Password <small class="text-success">(Required)</small></label>
                                                <input type="text" id="employee_password" name="employee_password" class="form-control" placeholder="Password" value="<?php echo $employee_user_password; ?>" required>
                                            </div>
                                        </div>


                                        <!-- Employee Role -->
                                        <div class="col-lg-6">
                                            <label for="employee_user_role" class="form-label text-dark">Role <small class="text-success">(Required)</small></label>
                                            <select class="form-control" name="employee_user_role" id="employee_user_role" data-choices data-choices-search="false" required>
                                                <option value="Staff" <?php echo ($employee_user_role == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                                                <option value="Technician" <?php echo ($employee_user_role == 'Technician') ? 'selected' : ''; ?>>Technician</option>
                                            </select>
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
                                                <input type="text" id="employee_father_name" name="employee_father_name" class="form-control" placeholder="Father's Name" value="<?php echo $employee_father_name; ?>">
                                            </div>
                                        </div>

                                        <!-- Employee Father Name -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_father_phone" class="form-label text-dark">Father's Phone No.</label>
                                                <input type="tel" id="employee_father_phone" name="employee_father_phone" class="form-control" placeholder="Father's Phone No." value="<?php echo $employee_father_phone; ?>" minlength="10" maxlength="10" maxlength="10" pattern="[0-9]{10}">
                                            </div>
                                        </div>

                                        <!-- Employee Bank Name -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_bank_name" class="form-label text-dark">Bank Name</label>
                                                <input type="text" id="employee_bank_name" name="employee_bank_name" class="form-control" placeholder="Bank Name" value="<?php echo $employee_bank_name; ?>">
                                            </div>
                                        </div>

                                        <!-- Employee Bank Account Number -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_bank_account" class="form-label text-dark">Account Number</label>
                                                <input type="text" id="employee_bank_account" name="employee_bank_account" class="form-control" placeholder="Account Number" value="<?php echo $employee_bank_account; ?>">
                                            </div>
                                        </div>

                                        <!-- Employee Bank IFSC Code -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_bank_ifsc" class="form-label text-dark">IFSC Code</label>
                                                <input type="text" id="employee_bank_ifsc" name="employee_bank_ifsc" class="form-control" placeholder="IFSC Code" value="<?php echo $employee_bank_ifsc; ?>">
                                            </div>
                                        </div>

                                        <!-- Employee Adhar Number -->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="employee_adhar_number" class="form-label text-dark">Adhar Number</label>
                                                <input type="text" id="employee_adhar_number" name="employee_adhar_number" class="form-control" placeholder="Adhar Number" value="<?php echo $employee_adhar_number; ?>" minlength="12" maxlength="12">
                                            </div>
                                        </div>

                                        <!-- Employee Address -->
                                        <div class="col-lg-12">
                                            <label for="employee_address" class="form-label text-dark">Address</label>
                                            <textarea type="text" id="employee_address" name="employee_address" class="form-control" rows="2" placeholder="Address"><?php echo $employee_address; ?></textarea>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light mb-3 rounded">
                        <div class="row justify-content-end g-2">
                            <div class="col-lg-2">
                                <button type="submit" name="update_employee" class="btn btn-outline-secondary w-100">Update Employee</button>
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