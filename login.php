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
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CheckForSPAM - Website Monitoring - Login</title>
	<link href="styles/main.css" rel="stylesheet" type="text/css" />
    <link href="styles/top.css" rel="stylesheet" type="text/css" />
    <link href="styles/feature.css" rel="stylesheet" type="text/css" />
    <link href="styles/myaccount.css" rel="stylesheet" type="text/css" />
	<link href="styles/footer.css" rel="stylesheet" type="text/css" />

        <meta id='vp' name="viewport" content="width=device-width, initial-scale=1">
    <script>
		if (screen.width < 500)
		{
			var mvp = document.getElementById('vp');
			mvp.setAttribute('content','width=500');
		}
		</script>
</head>

<body>

<div id="top_bar">
	<?php include 'template_parts/template_top.php'; ?>
</div>



<div id="content_bar">
	<div class="page_container">
    	
		<p class="page_feature_title">Access your account</p>
        
        <div class="form_result"><?php echo $result; ?></div>
        
        <div class="form_container">
        	<form action="/login.php" method="post">
                <input type="text" name="email" placeholder="E-Mail Address" />
                <input type="password" name="password" placeholder="Password" />
                <input type="submit" name="submit" value="Login" />
            </form>
        
        </div>
        
    </div>
</div>

<div id="footer_bar">
	<?php include 'template_parts/template_footer.php'; ?>
</div>

</body>
</html>