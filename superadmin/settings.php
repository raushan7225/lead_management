<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


################# Settings #################
$Settings = "SELECT * FROM `settings` ORDER BY `id` DESC LIMIT 1";
$SettingsResult = mysqli_query($conn, $Settings);
if (mysqli_num_rows($SettingsResult) > 0) {
    while ($row = mysqli_fetch_assoc($SettingsResult)) {
        $setting_id = $row['id'];
        $setting_status = $row['setting_status'];
    }
}
################# Settings #################


############### Publish / Unpublish #################
if (isset($_POST['publish'])) {
    $settingId = $_POST['settingId'];
    $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
    $updateStatusQuery = "UPDATE `settings` SET `setting_status` = '$new_status' WHERE `id` = '$settingId'";
    $PublishSetting = mysqli_query($conn, $updateStatusQuery);

    if ($PublishSetting) {
        echo "<script>window.location.href='settings.php'</script>";
    } else {
        $error_message = mysqli_error($conn);
        echo "<script>alert('Error: $error_message')</script>";
    }
}
############### Publish / Unpublish #################
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Settings</title>
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

        <!-- Start right Content here -->
        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-xxl">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Maintenance Mode</h4>
                            </div>

                            <div class="card-body d-flex flex-row justify-content-between align-items-center">
                                <label for="mentanance_mode" class="form-label text-dark"> Status </label>
                                <form action="#" method="post">
                                    <input type="hidden" name="settingId" value="<?php echo $setting_id; ?>">
                                    <input type="hidden" name="publish" value="0">
                                    <div class="form-check form-switch flex-box justify-content-center align-items-center">
                                        <input class="form-check-input" name="publish" type="checkbox" role="switch" id="flexSwitchCheckChecked<?php echo $setting_id; ?>" <?php echo ($setting_status == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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

</body>

</html>