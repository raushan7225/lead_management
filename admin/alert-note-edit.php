<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    ######################### Get Alert Note Data #######################
    $GetAlertNotes = "SELECT * FROM `alertnotes` WHERE `alert_for_company` = '$Company' AND `id` = '$id' LIMIT 1";
    $AlertNotesQuery = $conn->query($GetAlertNote);
    if (mysqli_num_rows($AlertNotesQuery) > 0) {
        while ($AlertNotes = mysqli_fetch_assoc($AlertNotesQuery)) {
            $AlertNotesTitle = $AlertNotes["alert_title"];
            $AlertNotesMessage = $AlertNotes["alert_message"];
            $AlertNotesDate = $AlertNotes["alert_date"];
            $AlertNotesStatus = $AlertNotes["alert_status"];
        }
    }
    ######################### Get Alert Note Data #######################

    $error = "";  // initiallise error message
    ################## Add Alert Note ######################
    if (isset($_POST['update_alertnote'])) {
        $alert_title       = trim(mysqli_real_escape_string($conn, $_POST['alert_title']));
        $alert_message     = trim(mysqli_real_escape_string($conn, $_POST['alert_message']));
        $alert_date        = $_POST['alert_date'];
        $alert_for_company = $Company;
        $alert_createdby   = $username;
        $alert_status      = $_POST['alert_status'];

        ################### Duplicate Title Check ###################
        $UpdateAlert   = "UPDATE `alertnotes` SET 
        `alert_title`='$alert_title',
        `alert_message`='$alert_message',
        `alert_date`='$alert_date',
        `alert_for_company`='$alert_for_company',
        `alert_createdby`='$alert_createdby',
        `alert_status`='$alert_status' WHERE `id`='$id'";
        $UpdateAlertQuery = mysqli_query($conn, $UpdateAlert);

        if ($UpdateAlertQuery) {
            echo "<script>window.location = 'alert-notes.php';</script>";
            exit;
        } else {
            $error = "Error: This alert title already exists! Please try another one.";
        }
    }
    ################## Add Alert Note ######################
} else {
    echo "<script>window.location = 'alert-notes.php';</script>";
    exit;
}

?>


<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Alert Notes</title>
    <?php include("head.php"); ?>
</head>

<body>
    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- WRAPPER -->
    <div class="wrapper">
        <?php include("topnav.php"); ?>
        <?php include("sidenav.php"); ?>

        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <!-- ============== Error Aler =============== -->
                    <div class="col-lg-12">
                        <?php if ($error) : ?>
                            <div class="alert alert-danger alert-dismissible mb-2 fade show rounded-1 overflow-hidden" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- ======x======= Error Aler ========x====== -->

                    <!-- ============== Alert Notes =============== -->
                    <div class="col-lg-12">
                        <form action="#" method="post" enctype="multipart/form-data">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Alert Notes</h4>
                                </div>

                                <div class="card-body">
                                    <!-- ======================== Title ======================== -->
                                    <div class="mb-2">
                                        <label for="alert_title" class="form-label">Title</label>
                                        <input type="text" name="alert_title" id="alert_title" class="form-control" placeholder="Alert Title" value="<?php echo $AlertNotesTitle; ?>" required>
                                    </div>
                                    <!-- ===========x============ Title =============x========== -->

                                    <!-- ======================== Message ======================== -->
                                    <div class="mb-2">
                                        <label class="form-label">Message</label>
                                        <div id="alert_message_editor" style="height: 240px;">
                                            <?php echo $AlertNotesMessage; ?>
                                        </div>
                                        <textarea name="alert_message" id="alert_message" style="display: none;">
                                            <?php echo $AlertNotesMessage; ?>
                                        </textarea>
                                    </div>
                                    <!-- ===========x============ Message =============x========== -->

                                    <div class="row">
                                        <!-- ======================== Date ======================== -->
                                        <div class="col-lg-3 mb-2">
                                            <label for="alert_date" class="form-label">Date <small class="text-success">(Required)</small></label>
                                            <input type="date" name="alert_date" id="alert_date" class="form-control" placeholder="Alert Date" value="<?php echo $AlertNotesDate; ?>" required>
                                        </div>
                                        <!-- ===========x============ Date =============x========== -->

                                        <!-- ======================== Status ======================== -->
                                        <div class="col-lg-3 mb-2">
                                            <label for="alert_status" class="form-label">Status</label>
                                            <select name="alert_status" id="alert_status" class="form-control" data-choices>
                                                <option value="1" <?php if ($AlertNotesStatus == 1) echo "selected"; ?>>Publish</option>
                                                <option value="0" <?php if ($AlertNotesStatus == 0) echo "selected"; ?>>Unpublish</option>
                                            </select>
                                        </div>
                                        <!-- ==========x============ Status =============x========== -->
                                    </div>
                                </div>

                                <div class="card-footer border-top border-dashed d-flex justify-content-end">
                                    <!-- Submit -->
                                    <button type="submit" name="update_alertnote" class="btn btn-primary">Update Alert</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- ======x======= Alert Notes ========x====== -->

                </div>
                <!-- row -->
            </div>
            <!-- container-xxl -->
        </div>
        <!-- page-content -->

        <!-- Footer -->
        <?php include("../footer.php"); ?>

    </div>
    <!-- wrapper -->

    <!-- Vendor Javascript -->
    <script src="../assets/js/vendor.js"></script>

    <!-- app js -->
    <script src="../assets/js/app.js"></script>

    <!-- Flatpickr -->
    <script>
        document.getElementById("alert_date").flatpickr();
    </script>

    <!-- Quill Editor js -->
    <script>
        var quill = new Quill('#alert_message_editor', {
            theme: 'snow',
            modules: {
                'toolbar': [
                    [{
                        'header': [false, 2, 3, 4, 5, 6]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    ['blockquote', 'code-block'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }, {
                        'indent': '-1'
                    }, {
                        'indent': '+1'
                    }],
                    ['direction', {
                        'align': []
                    }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            },
        });

        quill.on('text-change', function(delta, oldDelta, source) {
            var textContent = quill.root.innerHTML.trim();
            document.getElementById('alert_message').value = textContent;
        });
    </script>
</body>

</html>