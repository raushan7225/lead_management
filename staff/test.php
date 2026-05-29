<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################# Push activity ################
$activity_id = $guid;
$activity_contrent = "The new customer '" . $lead_customer_name . "' has been added by staff " . $username . " of " . $Branch . " branch.";
$activity_type = 'Alert';
$activity_company = $Company;
$activity_branch = $Branch;
$activity_on = date('Y-m-d H:i:s');
$activity_by = $username;
$NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_contrent`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_contrent','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
$ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
################# Push activity ################

?>
