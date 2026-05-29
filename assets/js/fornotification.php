<?php
include("../config/db.php");
include("../config/session.php");

if (isset($_POST['CurrentDateAndTime'])) {
     $currentDateTime = $_POST['CurrentDateAndTime'];

     if ($Role == 'Super Admin') {
          $forNotify = "SELECT `id`, `lead_next_followup_date`, `lead_next_followup_time` 
          FROM `leads`
          AND `lead_type` NOT IN ('Completed', 'Cancel') 
          AND `lead_customer_status` = '1'
          AND CONCAT(`lead_next_followup_date`, ' ', `lead_next_followup_time`) <= '$currentDateTime'
          ORDER BY `lead_next_followup_date` DESC, `lead_next_followup_time` DESC";
     } elseif ($Role == 'Admin') {
          $forNotify = "SELECT `id`, `lead_next_followup_date`, `lead_next_followup_time` 
          FROM `leads` 
          WHERE `lead_for_company` = '$Company'
          AND `lead_type` NOT IN ('Completed', 'Cancel') 
          AND `lead_customer_status` = '1'
          AND CONCAT(`lead_next_followup_date`, ' ', `lead_next_followup_time`) <= '$currentDateTime'
          ORDER BY `lead_next_followup_date` DESC, `lead_next_followup_time` DESC";
     } elseif ($Role == 'Manager') {
          $forNotify = "SELECT `id`, `lead_next_followup_date`, `lead_next_followup_time` 
          FROM `leads` 
          WHERE `lead_for_company` = '$Company' 
          AND `lead_for_branch` = '$Branch'
          AND `lead_type` NOT IN ('Completed', 'Cancel') 
          AND `lead_customer_status` = '1'
          AND CONCAT(`lead_next_followup_date`, ' ', `lead_next_followup_time`) <= '$currentDateTime'
          ORDER BY `lead_next_followup_date` DESC, `lead_next_followup_time` DESC";
     } else {
          $forNotify = "SELECT `id`, `lead_next_followup_date`, `lead_next_followup_time` 
          FROM `leads` 
          WHERE `lead_for_company` = '$Company' 
          AND `lead_for_branch` = '$Branch' 
          AND `lead_assign_to` = '$username'
          AND `lead_type` NOT IN ('Completed', 'Cancel') 
          AND `lead_customer_status` = '1'
          AND CONCAT(`lead_next_followup_date`, ' ', `lead_next_followup_time`) <= '$currentDateTime'
          ORDER BY `lead_next_followup_date` DESC, `lead_next_followup_time` DESC";
     }

     $forNotifyReault = mysqli_query($conn, $forNotify);
     $forNotifyCount = $forNotifyReault ? $forNotifyReault->num_rows : 0;

     if ($forNotifyCount > 0) {
          while ($row = mysqli_fetch_assoc($forNotifyReault)) {
               $scheduledDatetime = $row['lead_next_followup_date'] . ' ' . $row['lead_next_followup_time'];
               echo '<div class="alert alert-dismissible notification border-bottom px-2 py-1 bg-primary fade show" role="alert">
                    <p class="notification-item">
                        <a href="followup-edit2.php?id=' . $row['id'] . '" style="text-decoration: none; color: #fff;">
                            ⏰ Your scheduled followup is on ' . $scheduledDatetime . '
                        </a>
                    </p>
                </div>';
          }
          exit;
     } else {
          echo '<div class="no-alerts">No follow-ups due at this time.</div>';
     }

}
?>



 <!-- <div id="notificationContainer"></div> -->



<script>
     function updateDateTime() {
          const now = new Date();
          const formatted = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;

          fetch("#", {
                    method: "POST",
                    headers: {
                         'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `CurrentDateAndTime=${encodeURIComponent(formatted)}`
               })
               .then(response => response.text())
               .then(data => {
                    document.getElementById("notificationContainer").innerHTML = data;
               })
               .catch(error => console.error('Error:', error));
     }

     // Run once immediately, then every 10 seconds
     updateDateTime();
     setInterval(updateDateTime, 1000);
</script>