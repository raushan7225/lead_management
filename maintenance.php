<?php
include("config/db.php");
include("config/session.php");

################# check maintain mode #####################
if ($ModeStatus == 0) {
    echo "<script>window.location.href='index.php'</script>";
    exit;
}
################# check maintain mode #####################

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Maintenance</title>
    <?php include("head.php"); ?>
    <style>
        .page-area {
            width: 100%;
            height: 90vh;
            padding: 10%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .message {
            max-width: 500px;
            margin-bottom: 2rem;
        }

        .btnContainer {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            background-color: #ff6c2f;
            padding: 5px;
            border-radius: 50px;
        }

        .btndate {
            color: #fff;
            padding: 5px 10px;
            border-radius: 50px;
        }

        .btntime {
            background-color: #fff;
            color: #ff6c2f;
            padding: 5px 10px;
            border-radius: 50px;
        }

        .btnOut {
            color: #fff;
            padding: 5px 12px;
            border-radius: 50px;
        }
    </style>
</head>

<body>

    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-fluid">
                <div class="page-area text-center">
                    <h1 class="h3 text-uppercase fs-md-24 mb-1">Under Maintenance</h1><br>
                    <img src="assets/images/maintenance.png" width="80%" alt=""><br><br>
                    <h2 class="h3 text-uppercase fs-md-24 mb-3">We're just tunning up a few things</h2>
                    <p class="message">We apologize for the inconvenience, Lead Management is currently undergoing planned maintenance. We'll be back online shortly.</p>

                    <div class="btnContainer">
                        <div class="btndate"><?php echo date('d/m/Y'); ?></div>
                        <div class="btntime"><span id="time"></span></div>
                        <a href="logout.php" class="btnOut">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Date and Time
        function updateTime() {
            const currentTime = new Date();
            const hours = currentTime.getHours();
            const minutes = currentTime.getMinutes();
            const seconds = currentTime.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedTime = `${hours % 12 || 12}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} ${ampm}`;
            document.getElementById('time').innerHTML = `${formattedTime}`;
        }
        setInterval(updateTime, 1000); // update every 1000 milliseconds (1 second)


        // Loader
        window.addEventListener("load", function() {
            setTimeout(function() {
                document.getElementById("loader-wrapper").style.display = "none";
                document.getElementById("wrapper").style.display = "block";
            }, 500);
        });
    </script>
</body>

</html>