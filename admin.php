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
	<link href="styles/main.css" rel="stylesheet" type="text/css" />
    <link href="styles/top.css" rel="stylesheet" type="text/css" />
    <link href="styles/feature.css" rel="stylesheet" type="text/css" />
    <link href="styles/content.css" rel="stylesheet" type="text/css" />
    <link href="styles/myaccount.css" rel="stylesheet" type="text/css" />
    <link href="styles/onoffswitch.css" rel="stylesheet" type="text/css" />
    
    
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

<div id="account_menu_bar">
	<div class="page_container">
        <ul>
            <li><a href="/admin.php?session_id=<?php echo $session_id; ?>">Dashboard</a></li>
            <li><a href="/admin.php?session_id=<?php echo $session_id; ?>&page=profile">Account Settings</a></li>
        </ul>
    </div>
</div>

<div class="content_bar">

	<div class="page_container">

		<?php
        
            if ($page == "" || $page == "dashboard") {
                
                echo "<p class='page_feature_title'>Dashboard (ADMIN)</p>";
				echo "<div class='form_result'>" . $result . "</div>";
				                
                // retrieve and display list of users
                
                $users_list = get_users_list();
								
				foreach ($users_list as $user_entry) {
					if ($user_entry['user_id'] <> 'admin') {
						echo "<div class='user_list_entry'>Name: " . $user_entry['lastname'] . ", " . $user_entry['firstname'] . " | E-Mail: " . $user_entry['email'] . " | <a onclick=\"if (!confirm('Are you sure you wish to delete this user?')) return false;\" href='/admin.php?session_id=" . $session_id . "&page=dashboard&action=delete_user&user_id=" . $user_entry['user_id'] . "'><img class='trash_icon' src='/images/trash.png' /></a></div>";
					}
				}
                
                
            } elseif ($page == "profile") {
                            
                // display profile information that can be updated
                $user_data = get_user_data($session_id);
                
                if (is_array($user_data)) {
                    
                    // display current profile that can be edited
                    ?>
                    
                    <p class="page_feature_title">Edit your profile (ADMIN)</p>
            
                    <div class="form_result"><?php echo $result; ?></div>
                    <div class="form_output"><?php echo $output; ?></div>
                    
                    <div class="form_container">
                        <form id="update_profile" action="/myaccount.php?session_id=<?php echo $session_id; ?>&page=profile" method="post">
                            <div>First Name: <input type="text" name="firstname" value="<?php echo $user_data['firstname']; ?>" /></div>
                            <div>Last Name: <input type="text" name="lastname" value="<?php echo $user_data['lastname']; ?>" /></div>
                            <div>E-Mail: <input type="text" name="email" value="<?php echo $user_data['email']; ?>" /></div>
                            <div>Password: <input type="password" name="password" value="<?php echo $user_data['password']; ?>" /></div>
                            <input type="submit" name="submit" value="Update Profile" />
                        </form>
                    </div>
                    
                    <?php
                    
                    
                    
                    
                } else {
                    // if not user data, then shouldn't be logged in!
                    // redirect to login.php
                }
                
            } else {
                
            }
            
        
        ?>
	</div>
</div>

<div id="footer_bar">
	<?php include 'template_parts/template_footer.php'; ?>
</div>





</body>
</html>