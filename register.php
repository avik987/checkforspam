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
    <title>CheckForSPAM | Registration</title>
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
    	
		<p class="page_feature_title">Create an account</p>
        
        <div class="form_result"><?php echo $result; ?></div>
        <div class="form_output"><?php echo $output; ?></div>
        
        <div class="form_container">
        	<form action="/register.php" method="post">
                <input type="text" name="firstname" placeholder="First Name" />
                <input type="text" name="lastname" placeholder="Last Name" />
                <input type="text" name="email" placeholder="E-Mail Address" />
                <input type="password" name="password" placeholder="Password" />
                <input type="submit" name="submit" value="Create Account" />
            </form>
        
        </div>
        
    </div>
</div>


<div id="footer_bar">
	<?php include 'template_parts/template_footer.php'; ?>
</div>



</body>
</html>