<?php
include_once __DIR__ . '/db.php';
include_once __DIR__ . '/session.php';

if (isset($_POST['CurrentDateAndTime'])) {
    $currentDateTime = mysqli_real_escape_string($conn, $_POST['CurrentDateAndTime']);
    
    // Parse date and time separately to allow database index usage
    $currentDate = date('Y-m-d', strtotime($currentDateTime));
    $currentTime = date('H:i:s', strtotime($currentDateTime));


    if ($Role == 'Super Admin') {
        $forNotify = "SELECT `id`, `lead_next_followup_date`, `lead_next_followup_time` 
        FROM `leads` 
        WHERE `lead_type` NOT IN ('Completed', 'Cancel') 
        AND `lead_customer_status` = '1'
        AND (`lead_next_followup_date` < '$currentDate' OR (`lead_next_followup_date` = '$currentDate' AND `lead_next_followup_time` <= '$currentTime'))
        ORDER BY `lead_next_followup_date` DESC, `lead_next_followup_time` DESC LIMIT 50";
    } elseif ($Role == 'Admin') {
        $forNotify = "SELECT `id`, `lead_next_followup_date`, `lead_next_followup_time`
        FROM `leads` 
        WHERE `lead_for_company` = '$Company' 
        AND `lead_type` NOT IN ('Completed', 'Cancel') 
        AND `lead_customer_status` = '1'
        AND (`lead_next_followup_date` < '$currentDate' OR (`lead_next_followup_date` = '$currentDate' AND `lead_next_followup_time` <= '$currentTime'))
        ORDER BY `lead_next_followup_date` DESC, `lead_next_followup_time` DESC LIMIT 50";
    } elseif ($Role == 'Manager') {
        $forNotify = "SELECT `id`, `lead_next_followup_date`, `lead_next_followup_time`, `lead_assign_to`  
        FROM `leads` 
        WHERE `lead_for_company` = '$Company' 
        AND `lead_for_branch` = '$Branch'
        AND `lead_type` NOT IN ('Completed', 'Cancel') 
        AND `lead_customer_status` = '1'
        AND (`lead_next_followup_date` < '$currentDate' OR (`lead_next_followup_date` = '$currentDate' AND `lead_next_followup_time` <= '$currentTime'))
        ORDER BY `lead_next_followup_date` DESC, `lead_next_followup_time` DESC LIMIT 50";
    } else {
        $forNotify = "SELECT `id`, `lead_next_followup_date`, `lead_next_followup_time` 
        FROM `leads` 
        WHERE `lead_for_company` = '$Company' 
        AND `lead_for_branch` = '$Branch' 
        AND `lead_assign_to` = '$username'
        AND `lead_type` NOT IN ('Completed', 'Cancel') 
        AND `lead_customer_status` = '1'
        AND (`lead_next_followup_date` < '$currentDate' OR (`lead_next_followup_date` = '$currentDate' AND `lead_next_followup_time` <= '$currentTime'))
        ORDER BY `lead_next_followup_date` DESC, `lead_next_followup_time` DESC LIMIT 50";
    }

    $forNotifyReault = mysqli_query($conn, $forNotify);
    $forNotifyCount = mysqli_num_rows($forNotifyReault);

    // echo '<div id="notify-count" style="display:none;">' . $forNotifyCount . '</div>'; // count container
    echo '<div id="notify-count" style="display:none;">' . (($forNotifyCount >= 50) ? '50+' : $forNotifyCount) . '</div>'; // count container

    if ($forNotifyCount > 0) {
        while ($NotiData = mysqli_fetch_assoc($forNotifyReault)) {
            $scheduledDatetime = $NotiData['lead_next_followup_date'] . ' ' . $NotiData['lead_next_followup_time'];
            if ($Role == 'Super Admin') {
                echo '<div class="alert alert-dismissible notification border-bottom px-2 py-1 bg-primary fade show" role="alert">
                      <a href="followup-view.php?id=' . $NotiData['id'] . '" style="text-decoration: none; color: #fff; width: 100%;"> 
                           <p class="notification-item">⏰ Follow up schedule for your employees is scheduled on ' . $scheduledDatetime . '.</p>
                      </a>
                  </div>';
            } elseif ($Role == 'Admin') {
                echo '<div class="alert alert-dismissible notification border-bottom px-2 py-1 bg-primary fade show" role="alert">
                      <a href="followup-view.php?id=' . $NotiData['id'] . '" style="text-decoration: none; color: #fff; width: 100%;"> 
                           <p class="notification-item">⏰ Follow up schedule for your employees is scheduled on ' . $scheduledDatetime . '.</p>
                      </a>
                  </div>';
            } elseif ($Role == 'Manager') {
                echo '<div class="alert alert-dismissible notification border-bottom px-2 py-1 bg-primary fade show" role="alert">
                      <a href="followup-view.php?id=' . $NotiData['id'] . '" style="text-decoration: none; color: #fff; width: 100%;"> 
                           <p class="notification-item">⏰ Followup scheduled for '. $NotiData['lead_assign_to'] . ' at ' . $scheduledDatetime . '.</p>
                      </a>
                  </div>';
            } else {
                echo '<div class="alert alert-dismissible notification border-bottom px-2 py-1 bg-primary fade show" role="alert">
                      <a href="followup-edit2.php?id=' . $NotiData['id'] . '" style="text-decoration: none; color: #fff; width: 100%;"> 
                           <p class="notification-item">⏰ Your scheduled followup is on ' . $scheduledDatetime . '</p>
                      </a>
                  </div>';
            }
        }
    } else {
        echo '<div class="no-alerts">No follow-ups due at this time.</div>';
    }
    exit;
}
?>


<!-- <div id="notificationContainer"></div> -->
<!-- <div id="notificationCount" style="font-weight: bold;"></div> -->