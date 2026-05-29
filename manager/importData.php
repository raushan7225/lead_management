<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if (isset($_POST['importSubmit']) && !empty($_FILES['file']['name'])) {
    $fileType = $_FILES['file']['type'];
    $allowedTypes = [
        'text/xls',
        'text/xlsx',
        'application/excel',
        'application/vnd.msexcel',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (in_array($fileType, $allowedTypes)) {
        $tmpFile = $_FILES['file']['tmp_name'];

        if (is_uploaded_file($tmpFile)) {
            try {
                $reader = new Xlsx();
                $spreadsheet = $reader->load($tmpFile);
                $rows = $spreadsheet->getActiveSheet()->toArray();
                unset($rows[0]); // Skip header row

                // Get company info for current branch
                $branchQuery = "SELECT `branch_of_company` FROM `branches` WHERE `branch_name` = '$Branch' AND `branch_status` = '1'";
                $branchData = mysqli_fetch_assoc(mysqli_query($conn, $branchQuery));
                $company = $branchData['branch_of_company'] ?? '';

                $lead_followup_date = date("Y-m-d");

                foreach ($rows as $row) {
                    $lead_customer_name        = $conn->real_escape_string($row[0] ?? '');
                    $lead_customer_contact     = $conn->real_escape_string($row[1] ?? '');
                    $lead_email                = $conn->real_escape_string($row[2] ?? '');
                    $lead_whatsapp             = $conn->real_escape_string($row[3] ?? '');
                    $lead_customer_type        = $conn->real_escape_string($row[4] ?? '');
                    $lead_type                 = $conn->real_escape_string($row[5] ?? '');
                    $lead_status               = $conn->real_escape_string($row[6] ?? '');
                    $lead_delivery_address     = $conn->real_escape_string($row[7] ?? '');
                    $lead_next_followup_date   = $conn->real_escape_string($row[8] ?? '');
                    $lead_next_followup_time   = $conn->real_escape_string($row[9] ?? '');

                    // Skip empty phone number
                    if (empty($lead_customer_contact)) continue;

                    $checkExist = mysqli_query($conn, "SELECT `id` FROM `importleads` WHERE `lead_customer_contact` = '$lead_customer_contact'");

                    if (mysqli_num_rows($checkExist) > 0) {
                        // Update existing record
                        $updateQuery = "UPDATE `importleads` SET 
                            `lead_customer_name` = '$lead_customer_name',
                            `lead_email` = '$lead_email',
                            `lead_whatsapp` = '$lead_whatsapp',
                            `lead_customer_type` = '$lead_customer_type',
                            `lead_type` = '$lead_type',
                            `lead_status` = '$lead_status',
                            `lead_delivery_address` = '$lead_delivery_address',
                            `lead_next_followup_date` = '$lead_next_followup_date',
                            `lead_next_followup_time` = '$lead_next_followup_time'
                            WHERE `lead_customer_contact` = '$lead_customer_contact'";
                        mysqli_query($conn, $updateQuery);
                    } else {
                        // Insert new record
                        $insertQuery = "INSERT INTO `importleads` (
                            `lead_customer_name`, `lead_customer_contact`, `lead_email`, `lead_whatsapp`,
                            `lead_for_company`, `lead_for_branch`, `lead_customer_type`, `lead_type`,
                            `lead_status`, `lead_followup_date`, `lead_next_followup_date`,
                            `lead_next_followup_time`, `lead_delivery_address`, `lead_createdby`,
                            `lead_assign_to`, `lead_customer_status`
                        ) VALUES (
                            '$lead_customer_name', '$lead_customer_contact', '$lead_email', '$lead_whatsapp',
                            '$company', '$Branch', '$lead_customer_type', '$lead_type',
                            '$lead_status', '$lead_followup_date', '$lead_next_followup_date',
                            '$lead_next_followup_time', '$lead_delivery_address', '$username',
                            '', '1'
                        )";
                        mysqli_query($conn, $insertQuery);
                    }
                }

                ################# Push activity ################
                $activity_id = $guid;
                $activity_content = "Some leads imported by manager " . $username . " of " . $Branch . " branch.";
                $activity_type = 'Alert';
                $activity_company = $Company;
                $activity_branch = $Branch;
                $activity_on = date('Y-m-d H:i:s');
                $activity_by = $username;
                $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                ################# Push activity ################

                echo "<script>window.location.href = 'lead-assign.php';</script>";
            } catch (Exception $e) {
                echo "<script>window.location.href = 'lead-assign.php';</script>";
            }
        } else {
            echo "<script>window.location.href = 'lead-assign.php';</script>";
        }
    } else {
        echo "<script>window.location.href = 'lead-assign.php';</script>";
    }
    exit;
}
