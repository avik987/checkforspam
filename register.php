<?php

	// include functions
	include 'config.php';
	include 'functions.php';
	
	// connect to db
	db_connect();
	
	// check if registration form has been submitted
	if ($_POST['submit']) {

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
			$account_created = create_account($firstname, $lastname, $email, $password);
		}
		
		if ($account_created === TRUE) {
			$result = "Account created successfully.";
		} else {
			$result = $account_created;
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="Easy Admin Panel Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
	<title>CheckForSPAM | Registration</title>
	<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<!-- Bootstrap Core CSS -->
	<link href="./styles/bootstrap.min.css" rel='stylesheet' type='text/css' />
	<!-- Custom CSS -->
	<link href="./styles/style.css" rel='stylesheet' type='text/css' />
	<!-- Graph CSS -->
	<link href="./styles/font-awesome.css" rel="stylesheet">
	<!-- jQuery -->
	<!-- lined-icons -->
	<link rel="stylesheet" href="./styles/icon-font.min.css" type='text/css' />
	<!-- //lined-icons -->
	<!-- chart -->
	<script src="./js/Chart.js"></script>
	<!-- //chart -->
	<!--animate-->
	<link href="./styles/animate.css" rel="stylesheet" type="text/css" media="all">
	<script src="./js/wow.min.js"></script>
	<script>
		new WOW().init();
	</script>
	<!--//end-animate-->
	<!----webfonts--->
	<link href='//fonts.googleapis.com/css?family=Cabin:400,400italic,500,500italic,600,600italic,700,700italic' rel='stylesheet' type='text/css'>
	<!---//webfonts--->
	<!-- Meters graphs -->
	<script src="./js/jquery-1.10.2.min.js"></script>
	<!-- Placed js at the end of the document so the pages load faster -->

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
			<div class="sign-up">
				<h3>Register Here</h3>
				<h5>Personal Information</h5>
				<div class="form_result"><?php echo $result; ?></div>
				<div class="form_output"><?php echo $output; ?></div>
				<form action="/register.php" method="post">

					<div class="sign-u">
						<div class="sign-up1">
							<h4>First Name* :</h4>
						</div>
						<div class="sign-up2">
							<input type="text"  name="firstname" placeholder="First Name " />
						</div>
						<div class="clearfix"> </div>
					</div>
					<div class="sign-u">
						<div class="sign-up1">
							<h4>Last Name* :</h4>
						</div>
						<div class="sign-up2">
							<input type="text" name="lastname" placeholder="Last Name" />
						</div>
						<div class="clearfix"> </div>
					</div>
					<div class="sign-u">
						<div class="sign-up1">
							<h4>Email Address* :</h4>
						</div>
						<div class="sign-up2">
							<input type="text" name="email" placeholder="E-Mail Address"/>
						</div>
						<div class="clearfix"> </div>
					</div>
					<h6>Login Information</h6>
					<div class="sign-u">
						<div class="sign-up1">
							<h4>Password* :</h4>
						</div>
						<div class="sign-up2">
							<input type="password" name="password" placeholder="Password"/>

						</div>
						<div class="clearfix"> </div>
					</div>
					<div class="sub_home">
						<div class="sub_home_left">
							<input type="submit" name="submit" value="Create Account">
						</div>
						<div class="sub_home_right">
							<p>Go Back to <a href="index.php">Home</a></p>
						</div>
						<div class="clearfix"> </div>
					</div>
				</form>
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

<script src="./js/jquery.nicescroll.js"></script>
<script src="./js/scripts.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="./js/bootstrap.min.js"></script>
</body>
</html>