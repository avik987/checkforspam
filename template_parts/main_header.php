<?php //error_reporting(E_ALL); ini_set('display_errors', 1); ?>
<?php

// include functions
include 'config.php';
include 'functions.php';

// connect to db
db_connect();

// get variables
if (isset($_GET['session_id'])) { $session_id = make_safe($_GET['session_id']); } else { $session_id = NULL; }
if (isset($_GET['user_id'])) { $submitted_user_id = make_safe($_GET['user_id']); } else { $submitted_user_id = NULL; }
if (isset($_GET['domain'])) { $domain = make_safe($_GET['domain']); } else { $domain = NULL; }
if (isset($_GET['action'])) { $action = make_safe($_GET['action']); } else { $action = NULL; }
if (isset($_GET['page'])) { $page = make_safe($_GET['page']); } else { $page = NULL; }

// validate session_id, otherwise redirect to login page
purge_old_sessions();
if (isset($session_id)) {
    $user_id = get_user_id($session_id);
}


if ($user_id == FALSE) {
    echo "You don't have permission to be here";
    exit();
} else {
    $display_logout = TRUE;
    update_session_timestamp($session_id);
}

// check if action has been requested
if ($action == "delete_user" && $submitted_user_id) {
    $result = delete_user($submitted_user_id);
}

// process form submissions
if ($_POST['submit']) {

    // check which page
    if ($page == "" || $page == "dashboard") {


    } elseif ($page == "profile") {
        // validate profile update details
        // get variables
        $firstname = make_safe($_POST['firstname']);
        $lastname = make_safe($_POST['lastname']);
        $email = make_safe($_POST['email']);
        $password = make_safe($_POST['password']);

        // validate variables
        $output = "";
        if (!$firstname) { $output .= "<p>Please enter a first name.</p>"; }
        if (!$lastname) { $output .= "<p>Please enter a last name.</p>"; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $output .= "<p>Please enter a valid email address.</p>"; }
        if (!$password) { $output .= "<p>Please enter a password.</p>"; }

        // check all are valid
        if (!$output) {
            $account_updated = update_profile($firstname, $lastname, $email, $password, $session_id);
        }

        if ($account_updated === TRUE) {
            $result = "Profile updated successfully.";
        } else {
            $result = $account_updated;
        }
    }

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CheckForSPAM - My Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="Easy Admin Panel Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <link href="../styles/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <link href="../styles/style.css" rel='stylesheet' type='text/css' />
    <link href="../styles/jsgrid-theme.min.css" rel='stylesheet' type='text/css' />
    <link href="../styles/jsgrid.min.css" rel='stylesheet' type='text/css' />
    <link href="../styles/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/icon-font.min.css" type='text/css' />
    <link href="../styles/animate.css" rel="stylesheet" type="text/css" media="all">
    <script src="./../js/jquery-1.10.2.min.js"></script>
    <script src="./../js/jsgrid.min.js"></script>
    <script src="../js/wow.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>


    <script>
        new WOW().init();
    </script>
    <link href='//fonts.googleapis.com/css?family=Cabin:400,400italic,500,500italic,600,600italic,700,700italic' rel='stylesheet' type='text/css'>
    <meta id='vp' name="viewport" content="width=device-width, initial-scale=1">
    <script>
        if (screen.width < 500)
        {
            var mvp = document.getElementById('vp');
            mvp.setAttribute('content','width=500');
        }
    </script>
    <!-- Bootstrap Core JavaScript -->
</head>

<body class="sticky-header left-side-collapsed"">
<section>
    <!-- left side start-->
    <div class="left-side sticky-left-side">

        <!--logo and iconic logo start-->
        <div class="logo">
            <h1><a href="#">Easy <span>Admin</span></a></h1>
        </div>
        <div class="logo-icon text-center">
            <a href="#"><i class="lnr lnr-home"></i> </a>
        </div>

        <!--logo and iconic logo end-->
        <div class="left-side-inner">

            <!--sidebar nav start-->
            <ul class="nav nav-pills nav-stacked custom-nav">
                <li class="active"><a href="/admin.php?session_id=<?php echo $session_id; ?>"><i class="lnr lnr-power-switch"></i><span>Dashboard</span></a></li>
                <li><a href="users.php?session_id=<?php echo $session_id;?>"><i class="lnr lnr-users"></i> <span>Users</span></a></li>
                <li><a href="#"><i class="lnr lnr-menu"></i> <span>Web</span></a></li>

            </ul>
            <!--sidebar nav end-->
        </div>
    </div>
    <!-- left side end-->

    <!-- main content start-->
    <div class="main-content">
        <!-- header-starts -->
        <div class="header-section">

            <!--toggle button start-->
            <a class="toggle-btn  menu-collapsed"><i class="fa fa-bars"></i></a>
            <!--toggle button end-->

            <!--notification menu start -->
            <div class="menu-right">
                <div class="user-panel-top">
                    <div class="profile_details">
                        <ul>
                            <li> <a href="/login.php?action=logout&session_id=<?php echo $session_id; ?>"><span class="fa fa-sign-out"></span> Logout</a> </li>
                            <div class="clearfix"> </div>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!--notification menu end -->
        </div>
        <!-- //header-ends -->