<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="styles/main.css" rel="stylesheet" type="text/css" />
<link href="styles/top.css" rel="stylesheet" type="text/css" />
<link href="styles/feature.css" rel="stylesheet" type="text/css" />
<link href="styles/content.css" rel="stylesheet" type="text/css" />
<link href="styles/footer.css" rel="stylesheet" type="text/css" />
<link href="styles/front.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="verifyownership" content="2066e88900f8aa6a7737d4b9279f0a45" />
<title>CheckforSPAM.com</title>

<?php

// Includes
include "config.php";
include "functions.php";

/*****************************/

// Connect to DB (needed to make_safe to work)
db_connect();

/* CHECK FOR FORM SUBMISSION */

// clear variables
$ip_address = NULL;

// check for form submission
if (isset($_GET['query'])) { 
	// remove any nasties
	$query = make_safe($_GET['query']); 
	
	// validate query
	// responses = url, ip, blank, invalid
	$valid_query = check_input_type($query);
	
	// if valid URL, then get ip address for it
	if ($valid_query == "url") {
		$ip_address = get_ip_address_from_url($query);
	} elseif ($valid_query == "ip") {
		$ip_address = $query;
	} else {
		$result_title = "Please enter a valid query!";
	}
	
	if ($ip_address) {
		
		// incorporate jquery
		?>
		<script type="text/javascript" src="includes/jquery-1.9.1.min.js"></script>
		<script type="text/javascript">                                         
			$(document).ready(function(){
				<?php
					foreach ($GLOBALS['spam_blacklist_servers'] as $name => $url) {
						?> 
						$('#<?php echo strtolower($name) . "_container"; ?>').load("checker.php?q=<?php echo $ip_address; ?>&remote=<?php echo strtolower($name); ?>");
						<?php
					}
				?>
			});
		</script>
		<?php
		
	}
}
/*****************************/

?>



</head>

<body>


<div id="top_bar">
	<?php include "template_parts/template_top.php"; ?>
</div>

<div id="feature_bar">
	<div class="page_container">
    	<div id="feature_container">
        	<div id="feature_exclamation">
            	<a href="/register.php" ><img src="/images/spam_exclamation.png" /></a>
            </div>
            <div id="feature_headings_container">
            	<div id="header_line1">
                	<div id="l1_left">STOP!</div>
                    <div id="l1_right">Is your e-mail being blocked?</div>
                </div>
                <div id="header_line2">Free SPAM Blacklist Monitoring</div>
            </div>
            <div id="feature_red_button">
            	<a href="/register.php" ><img src="/images/big_red_button.png" /></a>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
</div>

<div id="content_bar">
	<div class="page_container">
    	
    	<div id="check_container">
            	<p>Check the SPAM blacklists now:</p>
            	<div id="front_form_outer">
                    <div id="front_form_inner">
                        <form action="/" method="get">
                            <p>Please enter a valid domain or IPv4 address:</p>
                            <input type="text" name="query" value="<?php echo $query; ?>">
                            <input type="submit" value="Search ->">
                            <p class="footer_text">e.g. checkforspam.com or 139.130.4.5</p>
                        </form>
                    </div>
                </div>
                
                <?php
                if ($ip_address) {
                    ?>
                    <div id="results_container">
                        <div class="results_table_row">
                            <div class="spam_result_icon">
                            </div>
                            <div class="spam_result_blacklist">
                                <strong>Blacklist</strong>
                            </div>
                            <div class="spam_result_details">
                                <strong>Results</strong>
                            </div>
                            <div class="spam_result_time">
                                <strong>Response Time</strong>
                            </div>
                        </div>
                        <?php
                        foreach ($GLOBALS['spam_blacklist_servers'] as $name => $url) {
                            ?>
                                <div class="results_table_row" id="<?php echo strtolower($name) . "_container"; ?>"><img src="images/loading.gif" /><?php echo $name; ?></div>
                            <?php
                        }
                        ?>
                    </div>    
                        <?php
                }
                ?>
 
          </div>  
          
          <div id="call_to_action_container">
          	<div id="call_to_action_left">
            	<a href="/register.php" >Sign up now for FREE blacklist monitoring alerts straight to your e-mail inbox.<br />
                <br />
                Click here to begin...</a>
            </div>
            <div id="call_to_action_right">
            	<a href="/register.php" ><img src="/images/website_screenshot_400.jpg" /></a>
            </div>
          </div>
    </div>
</div>


<div id="footer_bar">
	<?php include "template_parts/template_footer.php"; ?>
</div>




</body>
</html>