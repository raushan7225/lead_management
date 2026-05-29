<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

$error = "";  // initiallise error message

################## Publish / Unpublish ######################
if (isset($_POST['alertid']) && isset($_POST['publish'])) {
    $alertId   = $_POST['alertid'];
    $newStatus = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
    $toggleSql = "UPDATE `alertnotes` SET `alert_status` = '$newStatus' WHERE `id` = '$alertId'";
    mysqli_query($conn, $toggleSql);
    header("Location: alert-notes.php");
    exit;
}
################## Publish / Unpublish ######################

################## Delete Alert Note ######################
if (isset($_POST['delete_alertnote'])) {
    $alertId = $_POST['alertid'];
    $deleteSql = "DELETE FROM `alertnotes` WHERE `id` = '$alertId'";
    mysqli_query($conn, $deleteSql);
    header("Location: alert-notes.php");
    exit;
} elseif (isset($_POST["edit_alertnote"])) {
    $alertId = $_POST["alertid"];
    header("Location: alert-note-edit.php?id=$alertId");
    exit;
}
################## Delete Alert Note ######################

################## Add Alert Note ######################
if (isset($_POST['submit_alertnote'])) {
    $alert_id          = '#' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
    $alert_title       = mysqli_real_escape_string($conn, $_POST['alert_title']);
    $alert_message     = mysqli_real_escape_string($conn, $_POST['alert_message']);
    $alert_date        = $_POST['alert_date'];
    $alert_for_company = $Company;
    $alert_createdby   = $username;
    $alert_status      = $_POST['alert_status'];

    ################### Duplicate Title Check ###################
    $dupSql   = "SELECT `alert_title` FROM `alertnotes` WHERE `alert_title` = '$alert_title' LIMIT 1";
    $dupCheck = mysqli_query($conn, $dupSql);

    if (mysqli_num_rows($dupCheck) > 0) {
        $error = "Error: This alert title already exists! Please try another one.";
        ################### Duplicate Title Check ###################
    } else {
        ################### Insert new alertnote ###################
        $insertSql = "INSERT INTO `alertnotes` (`alert_id`,`alert_title`,`alert_message`,`alert_date`,`alert_for_company`,`alert_createdby`,`alert_status`) VALUES ('$alert_id','$alert_title','$alert_message','$alert_date','$alert_for_company','$alert_createdby','$alert_status')";
        if (mysqli_query($conn, $insertSql)) {
            header("Location: alert-notes.php");
            exit;
        } else {
            $error = mysqli_error($conn);
        }
        ################### Insert new alertnote ###################

    }
}
################## Add Alert Note ######################


################### Fetch All Companies ###################
$company_list_sql      = "SELECT * FROM `companies`   ORDER BY `id` DESC";
$company_list_result   = mysqli_query($conn, $company_list_sql);
################### Fetch All Companies ###################


################### Fetch All Alert Notes ###################
$alertnote_list_sql = "SELECT * FROM `alertnotes`  ORDER BY `id` DESC";
$alertnote_list_result = mysqli_query($conn, $alertnote_list_sql);
################### Fetch All Alert Notes ###################

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
                    <!-- Modal -->
                    <div class="modal fade" id="AddAlertModal" tabindex="-1" aria-labelledby="AddAlertModalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <form action="#" method="post" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="AddAlertModalTitle">Alert Notes</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <!-- ======================== Title ======================== -->
                                        <div class="mb-2">
                                            <label for="alert_title" class="form-label">Title</label>
                                            <input type="text" name="alert_title" id="alert_title" class="form-control" placeholder="Alert Title" required>
                                        </div>
                                        <!-- ===========x============ Title =============x========== -->

                                        <!-- ======================== Message ======================== -->
                                        <div class="mb-2">
                                            <label class="form-label">Message</label>
                                            <div id="alert_message_editor" style="height: 120px;"></div>
                                            <textarea name="alert_message" id="alert_message" style="display: none;"></textarea>
                                        </div>
                                        <!-- ===========x============ Message =============x========== -->

                                        <div class="row">
                                            <!-- ======================== Date ======================== -->
                                            <div class="col-lg-3 mb-2">
                                                <label for="alert_date" class="form-label">Date <small class="text-success">(Required)</small></label>
                                                <input type="date" name="alert_date" id="alert_date" class="form-control" placeholder="Alert Date" required>
                                            </div>
                                            <!-- ===========x============ Date =============x========== -->

                                            <!-- ======================== Status ======================== -->
                                            <div class="col-lg-3 mb-2">
                                                <label for="alert_status" class="form-label">Status</label>
                                                <select name="alert_status" id="alert_status" class="form-control" data-choices>
                                                    <option value="1">Publish</option>
                                                    <option value="0">Unpublish</option>
                                                </select>
                                            </div>
                                            <!-- ==========x============ Status =============x========== -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="card-footer border-top border-dashed d-flex justify-content-end">
                                            <button type="submit" name="submit_alertnote" class="btn btn-primary">Submit Now</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


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


                    <!-- =============== Alert List ================ -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center gap-2">
                                <h3 class="card-title">Alert List</h3>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#AddAlertModal">
                                    Add Alert
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="table-alertnote-search"></div>
                            </div>
                        </div>
                    </div>
                    <!-- ========x====== Alert List ========x====== -->
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

    <!-- gridjs -->
    <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

    <script>
        // Date picker
        document.getElementById("alert_date").flatpickr();

        // Alert List
        const AlertData = [
            <?php
            $counterValue = 1;
            if ($counterValue <= mysqli_num_rows($alertnote_list_result)) {
                while ($note = mysqli_fetch_assoc($alertnote_list_result)) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`),
                        gridjs.html(`<?php echo addslashes($note['alert_title']); ?>`),
                        gridjs.html(`<?php echo date('d-m-Y', strtotime($note['alert_date'])); ?>`),
                        gridjs.html(`<?php echo addslashes($note['alert_createdby']); ?>`),

                        gridjs.html(`
                            <form action="#" method="post">
                                <input type="hidden" name="alertid" value="<?php echo $note['id']; ?>">
                                <input type="hidden" name="publish" value="0">
                                <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                    <input class="form-check-input" name="publish" type="checkbox" role="switch" id="switch<?php echo $note['id']; ?>" <?php echo ($note['alert_status'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                </div>
                            </form>`),

                        gridjs.html(`
                            <div class="row gap-1">
                                <div class="col-5">
                                    <form action="#" method="post">
                                        <input type="hidden" name="alertid" value="<?php echo $note['id']; ?>">
                                        <button type="submit" name="edit_alertnote" value="<?php echo $note['id']; ?>" class="btn btn-soft-info btn-sm">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                                <div class="col-5">
                                    <form action="#" method="post" onsubmit="return confirm('Are you sure you want to delete this alert?');">
                                        <input type="hidden" name="alertid" value="<?php echo $note['id']; ?>">
                                        <button type="submit" class="btn btn-soft-danger btn-sm">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </div>`)
                    ],
            <?php }
            } ?>
        ];

        if (document.getElementById("table-alertnote-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No",
                        width: "50px"
                    },
                    "Alert",
                    {
                        name: "Date",
                        width: "150px"
                    },
                    {
                        name: "Created By",
                        width: "120px"
                    },
                    {
                        name: "Status",
                        width: "50px"
                    },
                    {
                        name: "Actions",
                        width: "130px"
                    }
                ],
                data: AlertData,
                pagination: {
                    limit: 10
                },
                search: true,
                style: {
                    table: {
                        'min-width': '1200px',
                        'font-size': '15px',
                        'text-align': 'center'
                    },
                    th: {
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    }
                }
            }).render(document.getElementById("table-alertnote-search"));
        }
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