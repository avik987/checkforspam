<?php

include "config.php";
include "functions.php";

// connect to db
db_connect();

// get ip address
$ip_address = make_safe($_GET['q']);
$remote_server = make_safe($_GET['remote']);

// check that valid ip address is submitted
$ip_check = check_input_type($ip_address);
if ($ip_check <> "ip") { echo "No script kiddies please"; exit(); }

$valid_remote_server = FALSE;
foreach ($GLOBALS['spam_blacklist_servers'] as $name => $url) {
	// check remote_server is valid
	if (strtolower($name) == $remote_server) {
		$valid_remote_server = TRUE;
		
		$reverse_ip = implode(".", array_reverse(explode(".", $ip_address)));
		$access_url = $reverse_ip . "." . $url;
		$start_time = microtime(true);
		$record = dns_get_record($access_url);
		$end_time = microtime(true);
						
		if (count($record) > 0) {
			?>
            	<div class="spam_result_row_fail">
                	<div class="spam_result_icon">
                    	<img src="images/incorrect.png" />
                    </div>
                    <div class="spam_result_blacklist">
                    	<?php echo $name; ?>
                    </div>
                    <div class="spam_result_details">
                    	Listed
                    </div>
                    <div class="spam_result_time">
                    	<?php echo ($end_time - $start_time); ?>s
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
                    	<?php echo $name; ?>
                    </div>
                    <div class="spam_result_details">
                    	Not listed
                    </div>
                    <div class="spam_result_time">
                    	<?php echo round(($end_time - $start_time),2); ?>s
                    </div>
                </div>
            <?php
		}
	}
}
if ($valid_remote_server == FALSE) {
	echo "Invalid remote server";
}

?>