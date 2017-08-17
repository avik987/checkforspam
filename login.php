<?php

	// include functions
	include 'config.php';
	include 'functions.php';
	
	// connect to db
	db_connect();
	
	// get variables
	$session_id = make_safe($_GET['session_id']);
	$action = make_safe($_GET['action']);
	
	if ($action == 'logout') {
		if ($session_id) {
			$result = kill_session($session_id);
		} else {
			"No session to logout from";
		}
	}
	
	// check if login form is submitted
	if ($_POST['submit']) {

		// get variables
		$email = make_safe($_POST['email']);
		$password = make_safe($_POST['password']);
		
		// validate and create session_id
		$session_id = create_session($email, $password);
		$user_id = get_user_id($session_id);
				
		if ($session_id) {
			if ($user_id == "admin") {
				header('Location: '.$site_base_url.'admin.php?session_id=' . $session_id);
			} else {
				header('Location: '.$site_base_url.'myaccount.php?session_id=' . $session_id);
			}
		} else {
			$result = "Invalid login details";
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>CheckForSPAM - Website Monitoring - Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="Easy Admin Panel Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
	<script type="" src="js/jquery-1.10.2.min.js"></script>

	<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<link href="styles/bootstrap.min.css" rel='stylesheet' type='text/css' />
	<link href="styles/style.css" rel='stylesheet' type='text/css' />
	<link href="styles/font-awesome.css" rel="stylesheet">
	<link rel="stylesheet" href="./styles/icon-font.min.css" type='text/css' />
	<script src="js/Chart.js"></script>
	<link href="styles/animate.css" rel="stylesheet" type="text/css" media="all">
	<script src="js/wow.min.js"></script>
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
</head>
<body class="sign-in-up">
<section>
	<div id="page-wrapper" class="sign-in-wrapper">
		<div class="graphs">
			<div class="sign-in-form">
				<div class="sign-in-form-top">
					<p><span>Sign In to</span> <a href="index.html">Admin</a></p>
				</div>
				<div class="signin">
					<div class="signin-rit">
								<span class="checkbox1">
									 <label class="checkbox"><input type="checkbox" name="checkbox" checked="">Forgot Password ?</label>
								</span>
						<p><a href="#">Click Here</a> </p>
						<div class="clearfix"> </div>
					</div>
					<div class="form_result"><?php echo $result; ?></div>
					<form action="/login.php" method="post">
						<div class="log-input">
							<div class="log-input-left">
								<input type="text" class="user" name="email" value="Yourmail" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Email address:';}"/>
							</div>
								<span class="checkbox2">
									 <label class="checkbox"><input type="checkbox" name="checkbox" checked=""><i> </i></label>
								</span>
							<div class="clearfix"> </div>
						</div>
						<div class="log-input">
							<div class="log-input-left">
								<input type="password" name="password" class="lock" value="password" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Email address:';}"/>
							</div>
								<span class="checkbox2">
									 <label class="checkbox"><input type="checkbox" name="checkbox" checked=""><i> </i></label>
								</span>
							<div class="clearfix"> </div>
						</div>
						<input type="submit" name="submit" value="Login to your account">
					</form>
				</div>
				<div class="new_people">
					<h4>For New People</h4>
					<p>Having hands on experience in creating innovative designs,I do offer design
						solutions which harness.</p>
					<a href="register.php">Register Now!</a>
				</div>
			</div>
		</div>
	</div>
	<!--footer section start-->
	<footer>
		<p>        	&copy;2017 CheckForSPAM.com
		</p>
	</footer>
	<!--footer section end-->
</section>

<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>

