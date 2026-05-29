<?php
session_start();
if (empty($_SESSION['employee_username'])) {
    header("Location: auth-signin.php");
    exit;
}

include_once __DIR__ . '/db.php'; // Include the database connection cleanly using absolute path

$username = mysqli_real_escape_string($conn, $_SESSION['employee_username']);
$userData = "SELECT `id`, `employee_name`, `employee_of_company`, `employee_of_branch`, `employee_user_role`, `profile_pic` FROM `employees` WHERE `employee_username` = '$username' AND `employee_status` = 1 LIMIT 1";
$result = mysqli_query($conn, $userData);

if ($userDataResult = mysqli_fetch_assoc($result)) {
    // Assign variables from the result
    $uid = $userDataResult['id'];
    $fullname = $userDataResult['employee_name'];
    $Company = $userDataResult['employee_of_company'];
    $Branch = $userDataResult['employee_of_branch'];
    $Role = $userDataResult['employee_user_role'];
    $Profile_pic = $userDataResult['profile_pic'];

    // Get Settings
    $MaintainMode = "SELECT * FROM `settings` WHERE `id` = '1' LIMIT 1";
    $MaintainMode_result = $conn->query($MaintainMode);
    if ($MaintainMode_result->num_rows > 0) {
        $MaintainModeData = $MaintainMode_result->fetch_assoc();
        $ModesID = $MaintainModeData['id'];
        $ModeStatus = $MaintainModeData['setting_status'];
    }
    

} else {
    // Invalid session or inactive account
    session_unset();
    session_destroy();
    header("Location: auth-signin.php");
    exit;
}
