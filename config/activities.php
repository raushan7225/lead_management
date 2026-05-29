<?php
include_once __DIR__ . '/db.php';
include_once __DIR__ . '/session.php';

################# Get activities ################
if ($Role == 'Super Admin') {
    $GetAllActions = "SELECT * FROM `activities` ORDER BY `id` DESC LIMIT 200";
} elseif ($Role == 'Admin') {
    $GetAllActions = "SELECT * FROM `activities` WHERE `activity_company` = '$Company' ORDER BY `id` DESC LIMIT 150";
} elseif ($Role == 'Manager') {
    $GetAllActions = "SELECT * FROM `activities` WHERE `activity_company` = '$Company' AND `activity_branch` = '$Branch' ORDER BY `id` DESC LIMIT 100";
} else {
    $GetAllActions = "SELECT * FROM `activities` WHERE `activity_company` = '$Company' AND `activity_branch` = '$Branch' AND `activity_by` = '$username' ORDER BY `id` DESC LIMIT 100";
}

$AllActionsQuery = $conn->query($GetAllActions);
$AllActivitiesCount = $AllActionsQuery ? $AllActionsQuery->num_rows : 0;
$AllActionsList = [];
if ($AllActivitiesCount > 0) {
    while ($AllActions = $AllActionsQuery->fetch_assoc()) {
        $AllActionsList[] = $AllActions;
    }
}
################# Get activities ################

########### Genrate Unique ID #############
$guid = '#' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
########### Genrate Unique ID #############

################ Get Alert Note #################
$ActionDate = date('Y-m-d');
$GetAlertNote = "SELECT * FROM `alertnotes` WHERE `alert_for_company` = '$Company' AND `alert_date` = '$ActionDate' AND `alert_status` = '1' ORDER BY `id` DESC LIMIT 1";
$AlertNoteQuery = $conn->query($GetAlertNote);
$AlertNote = $AlertNoteQuery->fetch_assoc();
$AlertNoteTitle = $AlertNote["alert_title"];
$AlertNoteMessage = $AlertNote["alert_message"];
$AlertNoteDate = $AlertNote["alert_date"];
################ Get Alert Note #################
?>