<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################### GET USER DATA FROM DATABASE ###################
$UsersList = [];
$CollectUsersSQL = "SELECT `employee_username`, `employee_name`, `profile_pic` FROM `employees` WHERE `employee_of_company` = '$Company' AND `employee_username` != '$username' AND `employee_user_role` != 'Super Admin' AND `employee_status` = '1' ORDER BY `employee_name` ASC";
$UsersResult = $conn->query($CollectUsersSQL);
if ($UsersResult->num_rows > 0) {
    while ($rowUser = $UsersResult->fetch_assoc()) {
        $UsersList[] = $rowUser;
    }
}
################### GET USER DATA FROM DATABASE ###################


################## GET MESSAGES FROM DATABASE ##################
$MessagesList = [];
$MessagesSQL = "SELECT * FROM `messages` WHERE `message_to` = '$username' OR `message_by` = '$username' ORDER BY `message_aton` ASC";
$MessagesResult = $conn->query($MessagesSQL);
if ($MessagesResult->num_rows > 0) {
    while ($msg = $MessagesResult->fetch_assoc()) {
        $MessagesList[] = $msg;
    }
}
################## GET MESSAGES FROM DATABASE ##################
?>


<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Messages</title>
    <?php include("head.php"); ?>
</head>

<body>
    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- START Wrapper -->
    <div class="wrapper">

        <?php include("topnav.php"); ?>
        <?php include("sidenav.php"); ?>

        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center gap">
                                <h4 class="card-title mb-0">Messages</h4>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#MassegeModal">
                                    Compose
                                </button>
                            </div>

                            <!-- ====================== Start Compose Message Modal ====================== -->
                            <div class="modal fade" id="MassegeModal" tabindex="-1" aria-labelledby="MassegeModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <form action="" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="MassegeModalTitle">Compose Message</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="recipient-name" class="form-label">Recipient:</label>
                                                    <select name="recipient-name" id="recipient-name" class="form-select" data-choices required>
                                                        <option value="">Select Recipient</option>
                                                        <?php foreach ($UsersList as $recipient) { ?>
                                                            <option value="<?php echo $recipient["employee_username"]; ?>">
                                                                <img class="rounded-circle me-2" width="32" height="32" src="<?php echo !empty($recipient["profile_pic"]) ? $recipient["profile_pic"] : '../assets/images/users/dummy-avatar.jpg'; ?>" alt="User Avatar">
                                                                <?php echo $recipient["employee_name"]; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="message-text" class="col-form-label">Message:</label>
                                                    <textarea class="form-control" id="message-text" name="message-content" rows="6" placeholder="Message" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="send_message" class="btn btn-primary">Send</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- ====================== End Compose Message Modal ====================== -->

                            <div class="card-body mb-0" style="height: 60vh; overflow-y: auto;">
                                <div class="h-100 ">
                                    <?php foreach ($MessagesList as $message) {
                                        $isSentByUser = $message["message_by"] === $username;
                                        $otherUser = $isSentByUser ? $message["message_to"] : $message["message_by"];

                                        $userInfoSQL = "SELECT `employee_name`, `profile_pic` FROM `employees` WHERE `employee_username` = '$otherUser' ORDER BY `id` ASC LIMIT 1";
                                        $userInfoResult = $conn->query($userInfoSQL);
                                        $userInfo = $userInfoResult->fetch_assoc();

                                        $userName = $userInfo["employee_name"];
                                        $userPic = !empty($userInfo["profile_pic"]) ? $userInfo["profile_pic"] : "../assets/images/users/dummy-avatar.jpg";
                                    ?>
                                        <div class="d-flex mb-3 <?php echo $isSentByUser ? 'justify-content-end text-end' : 'justify-content-start text-start'; ?>">
                                            <?php if (!$isSentByUser) { ?>
                                                <img class="rounded-circle me-2" width="32" height="32" src="<?php echo $userPic; ?>" alt="User Avatar">
                                            <?php } ?>
                                            <div class="" style="max-width: 75%;">
                                                <div class="bg-light p-1 px-2 rounded shadow-sm text-muted fs-14 p-1"><?php echo $message["messages"]; ?></div>
                                                <div class="dropdown-divider my-1"></div>
                                                <div class="fw-semibold fs-11"><?php echo $userName; ?></div>
                                                <small class="text-muted fs-8"><?php echo $message["message_aton"]; ?></small>
                                            </div>
                                            <?php if ($isSentByUser) { ?>
                                                <img class="rounded-circle ms-2" width="32" height="32" src="<?php echo !empty($profilepic) ? $profilepic : '../assets/images/users/dummy-avatar.jpg'; ?>" alt="My Avatar">
                                            <?php } ?>
                                        </div>
                                    <?php } ?><br>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <?php include("../footer.php"); ?>
        </div>

    </div>

    <script src="../assets/js/vendor.js"></script>
    <script src="../assets/js/app.js"></script>
</body>

</html>


<?php
################## Sent Messages ##################
if (isset($_POST["send_message"])) {
    function generateGUID()
    {
        return bin2hex(random_bytes(16));
    }

    $message_id = generateGUID();
    $message_to = $_POST["recipient-name"];
    $message_content = $_POST["message-content"];
    $message_by = $username;
    $message_aton = date("Y-m-d H:i:s");

    $InsertMessageSQL = "INSERT INTO `messages` 
        (`message_id`, `messages`, `message_to`, `message_by`, `message_aton`, `message_event`) 
        VALUES 
        ('$message_id', '$message_content', '$message_to', '$message_by', '$message_aton', 'unseen')";

    $conn->query($InsertMessageSQL);
    echo "<script>window.location.href=window.location.href;</script>";
}
################## Sent Messages ##################
?>