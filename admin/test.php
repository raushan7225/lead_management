<?php
if (isset($_POST['notify_update'])) {
    $seen_id = $_POST['seen_id'];
    $updateNotify = "UPDATE `notifications` SET `notify_action` = 'Seen', `notify_veiwer` = '$username' WHERE `id` =  '$seen_id'";
    if ($conn->query($updateNotify)) {
      echo 'Notification updated successfully';
    } else {
      echo 'Error updating notification';
    }
  }
?>
