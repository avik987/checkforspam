<?php error_reporting(E_ALL); ini_set('display_errors', 1); ?><?php

include "config.php";
include "functions.php";

// connect to db
db_connect();

// 1. retreive all domains
// 2. create array of unique domains
// 3. convert domains to IPs
// 4. get list of unique IPs
// 5. retrieve spam response for each IP and each spam_db


// create array
$domains_only = array();

// get list of domains/ips
$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".websites ORDER BY domain ASC";
$result = $GLOBALS['db_connection'] -> query($query);
	
if ($result -> num_rows > 0) {
	while($row = $result -> fetch_assoc()) {
		$domains_full[] = array('domain' => $row['domain'], 'user_id' => $row['user_id'], 'site_id' => $row['site_id'], 'spam_dbs' => $row['spam_dbs'], 'responses' => $row['responses'], 'downtime' => $row['downtime'], 'alerts' => $row['alerts']);
		$domains_only[] = $row['domain'];
	}
}


// create array of unique domains/ips (in case multi users are checking same site)
$domains_only = array_keys(array_flip($domains_only)); // use instad of array_unique as is faster

// go through each domain and identify requested spam_dbs
$domain_spam_dbs = array();
foreach ($domains_only as $domain_only) {
	
	$spam_dbs_array = array();
	// for multiple instances of the same domain, get a combined set of spam_dbs to scandir
	foreach ($domains_full as $domain_full) {

		if ($domain_full['domain'] == $domain_only) {
			$spam_dbs_array = array_merge($spam_dbs_array, explode(",", $domain_full['spam_dbs']));
		}
	}
	// take array of requested spam_dbs and remove duplicated values
	$spam_dbs_array = array_filter(array_keys(array_flip($spam_dbs_array))); // use instead of array_unique as is faster
	
	// create new array of domain and array of spam_dbs
	$domain_spam_dbs[$domain_only] = $spam_dbs_array;
}

// clear variable
$spam_dbs_array;
unset($spam_dbs_array);

// match domains with an ip address and create list of ip addresses
$ip_addresses = array(); // array('ip address' => array(spam_dbs))
$domain_ip_match = array();
foreach ($domains_only as $domain) {
	
	// get ip address for domain
	$ip = get_ip_address_from_url($domain);

	// if ip has already been retrieved, then merge requested spam_dbs and make unique
	if (array_key_exists($ip, $ip_addresses)) {
		// add spam dbs to end of existing spam db array
		// make unique
		$ip_addresses[$ip] = array_merge($ip_addresses[$ip], $domain_spam_dbs[$domain]);
		$ip_addresses[$ip] = array_filter(array_keys(array_flip($ip_addresses[$ip]))); // use instead of array_unique as is faster
	} else {
		// add ip address to array for checking
		$ip_addresses[$ip] = $domain_spam_dbs[$domain];
	}
	
	$domain_ip_match[$domain] = $ip;
}

$output = array();

// for each IP address, get spam list status
foreach ($ip_addresses as $ip_address => $spam_dbs) {
	
	foreach ($spam_dbs as $spam_db) {
		$spam_url = $GLOBALS['spam_blacklist_servers'][$spam_db];
		
		$reverse_ip = implode(".", array_reverse(explode(".", $ip_address)));
		$access_url = $reverse_ip . "." . $spam_url;
		$start_time = microtime(true);
		$record = dns_get_record($access_url);
		$end_time = microtime(true);
							
		if (count($record) > 0) {
			$output[$ip_address][$spam_db] = array('status' => 'listed', 'time' => round(($end_time - $start_time),2));
		} else {
			$output[$ip_address][$spam_db] = array('status' => 'unlisted', 'time' => round(($end_time - $start_time),2));
		}
	}
	
}


// get current timestamp
$current_time = time();
// update list of domains
foreach ($domains_full as $domain) {
	
	$responses = array(); // status, time
	$site_id = $domain['site_id'];
	$domain_ip = $domain_ip_match[$domain['domain']];
	
	// check each spam_db if requested
	if ($domain['spam_dbs']) {
		
		// get requested spam_dbs
		$requested_spam_dbs = explode(",", $domain['spam_dbs']);
		
		// foreach requested spam_db, add to corresponding sub array
		foreach ($requested_spam_dbs as $requested_spam_db) {
			$responses[$requested_spam_db] = $output[$domain_ip][$requested_spam_db];
		}
		
		
		// calculate downtime
		// downtime array is of format array(spam_db => array(array(start, finish), array(start,finish)), ...)
		$downtime_data = json_decode($domain['downtime'], true);
		foreach ($requested_spam_dbs as $requested_spam_db) {
			
			if (@$domain['responses'][$requested_spam_db]['status'] == 'listed') {
				// if domain is previously listed on spam_db
				// check if still listed, or now been cleared
				if ($responses[$requested_spam_db]['status'] == 'listed') {
					// if previously listed, then still listed
					// make no changes
				} else {
					// retrieve last element of downtime array
					$last_downtime_value = array_pop($downtime_data[$requested_spam_db]); // array(start, finish [or null])
					$last_downtime_value['finish'] = $current_time; // update element
					$downtime_data[$requested_spam_db][] = $last_downtime_value;
				}
			} else {
				// domain is previously unlisted
				// check if new response is listed or unlisted
				if ($responses[$requested_spam_db]['status'] == 'listed') {
					// if last check is 'unlisted' and new check is 'listed' add new array with current start time and set finish to current
					$downtime_data[$requested_spam_db][] = array('start' => $current_time, 'finish' => 'current');
				} else {
					// was unlisted, still unlisted, do nothing
				}
			}
			
		}
		
		// remove downtime data that is older than 1 year
		foreach ($requested_spam_dbs as $requested_spam_db) {
			// if there is any downtime data, then check for old
			if (isset($downtime_data[$requested_spam_db])) {
				foreach ($downtime_data[$requested_spam_db] as $key => $downtime) {
					if ($downtime['finish'] <> "current") {
						if ($downtime['finish'] < ($current_time - (24*3600*365))) {
							unset($downtime_data[$requested_spam_db][$key]);
						}
					}
				}
				// reindex the port subarrays to start from 0
				$downtime_data[$requested_spam_db] = array_values(array_filter($downtime_data[$requested_spam_db]));
			}
		}

		// reencode downtime
		$downtime_data = json_encode($downtime_data);
		// reencode responses
		$responses_query = json_encode($responses);
				
		// build query and update database
		$query = "UPDATE " . $GLOBALS['db_name'] . ".websites SET downtime = '$downtime_data', responses = '$responses_query' WHERE site_id = '$site_id' LIMIT 1";
	//	echo $query . "<br>";
		if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
		} else {
			display_sql_error();
			exit();
		}
		
						
		// check if e-mail alerts are enabled for this domain
		$alert_message = "";
		if ($domain['alerts'] == 'enabled') {
			// send e-mail alerts if domain gets listed on spam_db
			
			foreach ($requested_spam_dbs as $requested_spam_db) {
				// check if requested spam_db is down
				if ($responses[$requested_spam_db]['status'] == 'listed') {
					$alert_message .= $domain['domain'] . " is currently listed on " . $requested_spam_db . ".\n";
				}
			}
			
			if ($alert_message) {
				// if message body has been created, then submit to user

				// get user data
				$user_data = get_user_data_by_user_id($domain['user_id']);
			//	var_dump($user_data);die;
				$subject = "CheckForSPAM Alert for " . $domain['domain'];
				$alert_message .= "Dear " . $user_data['firstname'] . " " . $user_data['lastname'] . ", \n\n";
				$alert_message .= "\n\n" . "Regards, \n" . "CheckForSPAM.com";
				$headers = 'From: webmaster@example.com' . "\r\n" .
					'Reply-To: webmaster@example.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
				
				var_dump(mail($user_data['email'], $subject, $alert_message, $headers));
			}
			
			
		} else {

		}
	}
}
	

// 
?>