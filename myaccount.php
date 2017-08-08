<?php //error_reporting(E_ALL); ini_set('display_errors', 1); ?>
<?php

	// include functions
	include 'config.php';
	include 'functions.php';
	
	// connect to db
	db_connect();

	// get variables
	if (isset($_GET['page'])) { $page = make_safe($_GET['page']); } else { $page = NULL; }
	if (isset($_GET['session_id'])) { $session_id = make_safe($_GET['session_id']); } else { $session_id = NULL; }
	if (isset($_GET['domain'])) { $domain = make_safe($_GET['domain']); } else { $domain = NULL; }
	if (isset($_POST['form_name'])) { $form_name = make_safe($_POST['form_name']); } else { $form_name = NULL; }
	if (isset($_GET['action'])) { $action = make_safe($_GET['action']); } else { $action = NULL; }
	
	// clear any old sessions
	purge_old_sessions();
	
	// validate session_id, otherwise redirect to login page
	if (isset($session_id)) {
		$user_id = get_user_id($session_id);
	}
	if ($user_id == FALSE) {
		$header_content = "Location: " . $GLOBALS['site_base_url'];
		header($header_content);
	} else {
		$display_logout = TRUE;
		update_session_timestamp($session_id);
	}
	
	
	// check if an 'action' has been requested
	if ($action == "delete") {
		$result = remove_domain_from_user($domain, $session_id);
	}
	
	// process form submissions
	if ($_POST['submit']) {
		
		// check which page
		if ($page == "" || $page == "dashboard") {
			// add new domain to websites table
			$domain = make_safe($_POST['domain']);
			$result = add_domain_to_user($domain, $session_id);
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
		} elseif ($page == "help") {
			
			// no variables needed, just display the help images
			
		} elseif ($page == "summary") {
			// check whether alerts or databases form is submitted
			if ($form_name == "alerts") {
				
				// alerts will either be enabled or disabled
				// cron will then e-mail if domain is listed
				
				$alerts_status = make_safe($_POST['alert_status']);
				echo $alerts_status;

				// table = alerts
				// update website db entry to send alerts according to submission
				$alerts_updated = update_website_alerts($session_id, $domain, $alerts_status);
				
				if ($alerts_updated == TRUE) {
					$alerts_result = "Alerts updated successfully";
				} else {
					$alerts_result = $alerts_updated;
				}
			} elseif ($form_name == "databases") {
				// databases form
				// create array of databases and insert into websites table
				$databases_array = array_keys($GLOBALS['spam_blacklist_servers']);
				$databases_submitted = array();
				foreach ($databases_array as $database_name) {
					$database_status = make_safe($_POST[$database_name]);
					if ($database_status == TRUE) {
						$databases_submitted[] = $database_name;
					}
				}

				$databases_updated = update_website_databases($session_id, $domain, $databases_submitted);
				
				if ($databases_updated == TRUE) {
					$databases_result = "Databases updated successfully";
				} else {
					$databases_result = $databases_updated;
				}
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
            <li><a href="/myaccount.php?session_id=<?php echo $session_id; ?>">Dashboard</a></li>
            <li><a href="/myaccount.php?session_id=<?php echo $session_id; ?>&page=profile">Account Settings</a></li>
			<li><a href="/myaccount.php?session_id=<?php echo $session_id; ?>&page=help">Help</a></li>
        </ul>
    </div>
</div>

<div class="content_bar">

	<div class="page_container">

		<?php
        
            if ($page == "" || $page == "dashboard") {
                
                echo "<p class='page_feature_title'>Dashboard</p>";
				echo "<div class='form_result'>" . $result . "</div>";
				                
                // retrieve and display list of websites in account
                // display textbox to add new website to be monitored
                
                $websites_array = get_list_of_websites($session_id);
                
                if (is_array($websites_array)) {
                    // display list of websites
                    
                    echo "<ul class='websites_array'>";
					
                    foreach ($websites_array as $website) {
						// retreive data for website
						$website_data = get_website_data($session_id, $website['domain']);
						
						// determine website status
						$responses_array = json_decode($website_data['responses'], true); // database -> array(status, response_time)
						$current_status = get_current_status_by_response($responses_array);
										
                        echo "<li>";
							echo "<div class='dashboard_website_title_container'>";
								echo "<a class='dashboard_title_link' href='/myaccount.php?session_id=" . $session_id . "&page=summary&domain=" . $website['domain'] . "'>" . $website['domain'] . "</a>";
							echo "</div>";
							echo "<div class='dashboard_website_data_container'>";
								echo "<a href='/myaccount.php?session_id=" . $session_id . "&page=summary&domain=" . $website['domain'] . "'>";
									echo "<div class='" . $current_status . "'>Status: " . $current_status . "</div>";
								echo "</a>";								
							echo "</div>";
						echo "</li>";
                    }
                    
                    echo "</ul>";
					
                } elseif ($websites_array == "Invalid Session") {
                    // redirect to login page
                }
                
                // display form to add new website
                ?>
                <div class="dashboard_add_website_container">
                	<div class="status_title">Add new site</div>
                    <div class="dashboard_add_website_form_container">
                        <form id="add_new_site" action="/myaccount.php?session_id=<?php echo $session_id; ?>&page=dashboard" method="post">
                            <input type="text" name="domain" />
                            <input type="submit" name="submit" value="Add site"/>
                        </form>
                    </div>
                </div>
                <?php
                
            }
            elseif ($page == "summary") {
				

                echo "<p class='page_feature_title'>Summary: " . $domain . " <a onclick=\"if (!confirm('Are you sure you wish to delete this site?')) return false;\" href='/myaccount.php?session_id=" . $session_id . "&page=dashboard&action=delete&domain=" . $domain . "'><img class='trash_icon' src='/images/trash.png' /></a></p>";
                
                // display single web page details
                $website_data = get_website_data($session_id, $domain);
                
                if (is_array($website_data)) {
                    
					// code to process data from db
					
					// $responses_array = json_decode($website_data['responses'], true);
					// array of db->(response, time),db->(response, time)
					// response either 'timeout', 'listed', 'unlisted'
					
					// display statistics row
						// - overall status (ok, not ok), uptime %
					// display full list of results
					// display box stating which databases to user
					// display alerts box enabling/disabling e-mail
					?>
					
					<div id="data_summary_container">
                        <div id="data_summary_left">
							<div class="status_title">Website Statistics</div>
							<div class="statistics_container">
								<div class="data_output">
                                    <div class="data_output_title">Overall Status</div>
                                    <?php
                                        $responses_array = json_decode($website_data['responses'], true); // database -> array(status, response_time)
										$current_status = get_current_status_by_response($responses_array);
										
                                        echo "<div class='" . $current_status . "'>Status: " . $current_status . "</div>";
										
										if ($current_status == 'listed') {
											echo "<div class=''>See below to identify source.</div>";
										}
										
                                    ?>
                                
                                </div>
								<div class="data_output">
                                    <div class="data_output_title">Uptime</div>
                                    <?php
										
                                        $current_time = time();
                                        $year_ago = $current_time - (365*24*3600);
                                        $creation_time = $website_data['creation_time'];
                                        if ($creation_time > $year_ago) {
                                            $since = round(($current_time - $creation_time)/(3600*24), 1) . " days";
                                        } else {
                                            $since = "year";
                                        }
                                        
                                        $downtime_array = get_downtime_by_website_data($website_data); 
										
                                        foreach ($website_data['spam_dbs'] as $database) {
											if ($responses_array[$database] <> 'No data') {
                                            	echo "<div>" . $database . ": " . (100 - round($downtime_array[$database], 2)) . "% (over last " . $since . ")</div>";
											}
                                        }
										
                                    ?>
                                
                                </div>
								<div class="data_output">
                                    <div class="data_output_title">Responses</div>
                                    <?php
										// responses_array = array(database -> array(status, response_time))
                                        foreach ($website_data['spam_dbs'] as $database) {
											if ($responses_array[$database] <> 'No data') {
                                            	
												// if entry is listed
												if ($responses_array[$database]['status'] == 'listed') {
													?>
													
													<div class="spam_result_row_fail">
														<div class="spam_result_icon">
															<img src="images/incorrect.png" />
														</div>
														<div class="spam_result_blacklist">
															<?php echo $database; ?>
														</div>
														<div class="spam_result_details">
															Listed
														</div>
														<div class="spam_result_time">
															<?php echo $responses_array[$database]['time']; ?>s
														</div>
													</div>
													
													<?php
												} else {
													?>
													
													<div class="spam_result_row_success">
														<div class="spam_result_icon">
															<img src="images/correct.png" />
														</div>
														<div class="spam_result_blacklist">
															<?php echo $database; ?>
														</div>
														<div class="spam_result_details">
															Not listed
														</div>
														<div class="spam_result_time">
															<?php echo $responses_array[$database]['time']; ?>s
														</div>
													</div>
													
													<?php
												}
											}
                                        }
										
                                    ?>
                                
                                </div>
							</div>
						</div>
						<div id="data_summary_right">
							<div id="alert_status_container">
								<div class="status_title">E-Mail Alerts</div>
								<div class="status_form_container">
									<?php if ($alerts_result) {?><div class="form_result"><?php echo $alerts_result; ?></div><?php } ?>
                                    <form id="alerts_status_form" class="status_form" action="myaccount.php?session_id=<?php echo $session_id; ?>&page=summary&domain=<?php echo $domain; ?>" method="post">
                                        <ul>
											<select name='alert_status'>
												<option value='enabled' <?php if ($website_data['alerts'] == 'enabled') { echo "selected"; } ?>>Enabled</option>
												<option value='disabled' <?php if ($website_data['alerts'] == 'disabled') { echo "selected"; } ?>>Disabled</option>
        									</select>
                                        </ul>
                                        <input type="hidden" name="form_name" value="alerts" />
                                        <input type="submit" name="submit" value="Update Alerts">
                                    </form>
								</div>
							</div>
							
							<div id="monitoring_status_container">
                                <div class="status_title">Databases to Check</div>
                                <div class="status_form_container">
                                	<?php if ($databases_result) {?><div class="form_result"><?php echo $databases_result; ?></div><?php } ?>
                                    <form id="databases_status_form" class="status_form" action="myaccount.php?session_id=<?php echo $session_id; ?>&page=summary&domain=<?php echo $domain; ?>" method="post">
                                        
                                        
                                        <ul>
											<?php
												$databases = array_keys($GLOBALS['spam_blacklist_servers']);
												foreach ($databases as $database_name) {
													// check if database is already requested
													if (in_array($database_name, $website_data['spam_dbs'])) {
														$checked = "checked";
													} else {
														$checked = "";
													}
													
													// output switches
													echo "<li>" . $database_name;
													echo "<div class='onoffswitch'>";
													echo "<input class='onoffswitch-checkbox' " . $checked . " id='databases_" . $database_name . "' type='checkbox' name='" . $database_name . "' value='TRUE' />";
													echo "<label class='onoffswitch-label' for='databases_" . $database_name . "'>
														<span class='onoffswitch-inner'></span>
														<span class='onoffswitch-switch'></span>
													</label>";
													echo "</div>";
													echo "</li>";
												}
											?>
                                        </ul>
                                        
                                        <input type="hidden" name="form_name" value="databases" />
                                        

                                        
                                        <input type="submit" name="submit" value="Update DBs">
                                    </form>
                            	</div>
                            </div>
						</div>
					</div>
						
						
						
					<?php
                } else {
                    // redirect to dashboard
                }
                
            }
            elseif ($page == "profile") {
                            
                // display profile information that can be updated
                $user_data = get_user_data($session_id);
                
                if (is_array($user_data)) {
                    
                    // display current profile that can be edited
                    ?>
                    
                    <p class="page_feature_title">Edit your profile</p>
            
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
                
            }
            elseif ($page == "help") {
				
				// display help images to user
				?>
				
				<p class="page_feature_title">Help</p>
                
                <div class="help_screenshot_item">
                    <div class="status_title">Dashboard Help</div>
                    <div class="status_form_container">
                    	<img src="images/help_dashboard.jpg" />
                    </div>
                </div>
                
                <div class="help_screenshot_item">
                    <div class="status_title">Site/Server Summary Help</div>
                    <div class="status_form_container">
                    	<img src="images/help_summary.jpg" />
                    </div>
                </div>
                
                <?php
				
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