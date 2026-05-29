<?php
include("config/db.php");
include("config/session.php");

// Include PhpSpreadsheet library autoloader 
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if (isset($_POST['importSubmit'])) {

    $lead_for_branch = $Branch;
    ######### Fetch all branches from the database #########
    $branch_list = "SELECT * FROM `branches` WHERE `branch_name` = '$lead_for_branch' AND `branch_status` = '1'";
    $branch_list_result = $conn->query($branch_list);
    if ($branch_list_result->num_rows > 0) {
        $branchData = $branch_list_result->fetch_assoc();
        $branch_of_company = $branchData['branch_of_company'];
    } else {
        // No branch found, handle accordingly
        die("Branch not found.");
    }
    ######### Set default lead values #########
    $lead_createdby = $username;
    $lead_assign_to = "";
    $lead_followup_date = date("Y-m-d"); // Use Y-m-d for SQL date format
    $lead_customer_status = 1;

    // Allowed mime types for Excel files 
    $excelMimes = array(
        'text/xls',
        'text/xlsx',
        'application/excel',
        'application/vnd.msexcel',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    // Validate whether selected file is an Excel file 
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $excelMimes)) {

        // If the file is uploaded 
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $reader = new Xlsx();
            $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet_arr = $worksheet->toArray();

            // Remove header row 
            unset($worksheet_arr[0]);

            // Process each row in the spreadsheet
            foreach ($worksheet_arr as $row) {
                $lead_customer_name = $conn->real_escape_string($row[0]);
                $lead_customer_contact = $conn->real_escape_string($row[1]);
                $lead_email = $conn->real_escape_string($row[2]);
                $lead_whatsapp = $conn->real_escape_string($row[3]);
                $lead_customer_type = $conn->real_escape_string($row[4]);
                $lead_type = $conn->real_escape_string($row[5]);
                $lead_status = $conn->real_escape_string($row[6]);
                $lead_delivery_address = $conn->real_escape_string($row[7]);
                $lead_next_followup_date = $conn->real_escape_string($row[8]);
                $lead_next_followup_time = $conn->real_escape_string($row[9]);


                // Check whether the lead already exists in the database with the same contact
                $prevQuery = "SELECT `id` FROM `importleads` WHERE `lead_customer_contact` = '$lead_customer_contact'";
                $prevResult = $conn->query($prevQuery);

                if ($prevResult->num_rows > 0) {
                    // Update existing lead data in the database
                    $query = "UPDATE `importleads` SET 
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
                } else {
                    ## Insert new lead data into the database ##
                    $query = "INSERT INTO `importleads`(
                        `lead_customer_name`, 
                        `lead_customer_contact`, 
                        `lead_for_company`, 
                        `lead_for_branch`, 
                        `lead_email`, 
                        `lead_whatsapp`, 
                        `lead_customer_type`, 
                        `lead_type`, 
                        `lead_status`, 
                        `lead_followup_date`, 
                        `lead_next_followup_date`, 
                        `lead_next_followup_time`, 
                        `lead_delivery_address`, 
                        `lead_createdby`, 
                        `lead_assign_to`, 
                        `lead_customer_status`
                    ) VALUES (
                        '$lead_customer_name',
                        '$lead_customer_contact',
                        '$branch_of_company',
                        '$lead_for_branch',
                        '$lead_email',
                        '$lead_whatsapp',
                        '$lead_customer_type',
                        '$lead_type',
                        '$lead_status',
                        '$lead_followup_date',
                        '$lead_next_followup_date',
                        '$lead_next_followup_time',
                        '$lead_delivery_address',
                        '$lead_createdby',
                        '$lead_assign_to',
                        '$lead_customer_status'
                    )";
                }

                // Execute the query
                if (!$conn->query($query)) {
                    // Error inserting/updating data
                    echo "Error inserting data: " . $conn->error;
                } else {
                    // Data inserted/updated successfully
                    echo "Data inserted/updated successfully!";
                }
            }

            // Set success status for redirect
            $qstring = '?status=succ';
        } else {
            // Error uploading file
            $qstring = '?status=err';
        }
    } else {
        // Invalid file type
        $qstring = '?status=invalid_file';
    }
}

// Redirect to the listing page
header("Location: lead-upload.php" . $qstring);
exit; // Ensuring no further code is executed after the redirect
