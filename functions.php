<?php // error_reporting(E_ALL); ini_set('display_errors', 1); ?><?php

// SPAMKing Functions File

function db_connect() {
	// connect to the mysql database
	
	global $db_connection;

	// Create connection
	$db_connection = new mysqli($GLOBALS['db_servername'], $GLOBALS['db_username'], $GLOBALS['db_password']);

	// Check connection
	if ($db_connection -> connect_error) {
		die("Connection failed: " . $db_connection -> connect_error);
	}
}

function make_safe($text) {
	// cleans user inputted text for use with mysql
	// make_safe MUST be called AFTER db_connect()
	// database must be connected to use escape string function
	
	$text = stripslashes($text);
	$text = htmlentities($text);
	$text = strip_tags($text);
	$text = $GLOBALS['db_connection'] -> real_escape_string($text);
	return $text;
}

function valid_ipv4($var) {
	// check if submission is valid IPv4 address
	return filter_var($var, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
}

function valid_url($var){	
	// check if submission is valid URL
	$valid = filter_var($var, FILTER_VALIDATE_URL);
	
	if (!$valid === FALSE) {
		return TRUE;
	} else {
		// if invalid, try adding http:// in front of it
		$var = "http://" . $var;
		$valid = filter_var($var, FILTER_VALIDATE_URL);
		
		if (!$valid === FALSE) {
			return TRUE;
		} else {
		
			return FALSE;
		}
	}
}

function visitor_ip() { 
	// return ip address of visitor
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$theip=$_SERVER['HTTP_X_FORWARDED_FOR']; 
	} else {
		$theip=$_SERVER['REMOTE_ADDR']; 
	}

	return trim($theip); 
} 

function check_input_type($query) {
	// determine entry type
	if (valid_ipv4($query)) {
		return "ip";
	} elseif (valid_url($query)) {
		return "url";
	} elseif ($query == "") {
		return "blank";
	} else {
		return "invalid";
	}
}

function get_ip_address_from_url($url) {
			
	// get list of ip addresses based on domain name
	$ipaddress = gethostbyname($url);
		
	if (!$ipaddress === FALSE) {
		return $ipaddress;
	} else {
		return 'invalid';
	}
}

function check_spam_blacklists($ip) {
	// check a given IP address against the list of spam blacklists
		
	// reverse the ip address octets
	$reverse_ip = implode(".", array_reverse(explode(".", $ip)));
	
	$results = array();
	
	foreach ($GLOBALS['spam_blacklist_servers'] as $name => $url) {
		$access_url = $reverse_ip . "." . $url; 
		$record = dns_get_record($access_url);
				
		if (count($record) > 0) {
			$results[$name] = TRUE;
		} else {
			$results[$name] = FALSE;
		}
	}
	return $results;
}

function create_session($email, $password) {
	// check if email address and password are valid
	$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".users WHERE email = '$email' LIMIT 1";
	$result = $GLOBALS['db_connection'] -> query($query);
	
	if ($result -> num_rows > 0) {
		while($row = $result -> fetch_assoc()) {
			$db_pass = $row['password'];
			$user_id = $row['user_id'];
		}
		
		if ($db_pass <> $password) {
			return FALSE; 
		} else {
			// create session_id
			$unique_id = uniqid();
			// get current timestamp (unix epoch)
			$current_time = time();
						
			$query = "INSERT INTO " . $GLOBALS['db_name'] . ".sessions (session_id, user_id, session_time) VALUES ('$unique_id', '$user_id', '$current_time')";
			if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
				return $unique_id;
			} else {
				display_sql_error();
			}
		}
	} else {
		return FALSE;
	}
}

function create_account($firstname, $lastname, $email, $password) {
	
	// check if email address is already in database
	$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".users WHERE email = '$email' LIMIT 1";
	$result = $GLOBALS['db_connection'] -> query($query);
	
	if ($result -> num_rows > 0) {
		return "E-Mail address already registered";
	}
	
	// create account
	$unique_id = uniqid();
	$query = "INSERT INTO " . $GLOBALS['db_name'] . ".users (firstname, lastname, email, password, user_id) VALUES ('$firstname', '$lastname', '$email', '$password', '$unique_id')";
		
	if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
		return TRUE;
	} else {
		display_sql_error();
	}
}

function get_user_id($session_id) {
	// get the user_id based on the session_id
	
	
	$session_id = make_safe($session_id);
		
	$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".sessions WHERE session_id = '$session_id' LIMIT 1";
	$result = $GLOBALS['db_connection'] -> query($query);
	
	if ($result -> num_rows > 0) {
		while($row = $result -> fetch_assoc()) {
			$user_id = $row['user_id'];
		}
		return $user_id;
	} else {
		return FALSE;
	}

}

function remove_domain_from_user($domain, $session_id) {
	// removes domain from user
	
	// get the user_id (and check validity of session) based on session_id
	$user_id = get_user_id(make_safe($session_id));
	$domain = make_safe($domain);
	
	if ($user_id) {
		if ($domain) {
			$query = "DELETE FROM " . $GLOBALS['db_name'] . ".websites WHERE user_id='$user_id' AND domain='$domain'";
			if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
				return "Domain deleted successfully";
			} else {
				display_sql_error();
			}
		} else {
			return "Invalid domain";
		}
	} else {
		return "Invalid Session";
	}
}

function add_domain_to_user($submission, $session_id) {
	// add domain or IP address to user_id based on session_id
	
	// get the user_id (and check validity of session) based on session_id
	$user_id = get_user_id(make_safe($session_id));
	
	// check entry type
	if (check_input_type($submission) == "ip") {
		$entry = $submission;
	} elseif (check_input_type($submission) == "url") {
		$entry = get_domain_only($submission);
	}
	
	$current_time = time();
		
	// if domain is blank, try adding http:// in front
	if ($entry == "" ) {
		$entry = get_domain_only("http://" . $submission);
		// if still blank then exit
		if ($entry == "") {
			return "Error: invalid entry";
		}
	}
	
	
	// check if domain already exists for this user
	// if so, return error
	$website_list = get_list_of_websites($session_id); //array( array('site_id', 'domain'), ...)
	foreach ($website_list as $website) {
		if ($website['domain'] == $entry) {
			return "Error: Site already exists for user";
		}
	}
	
	if ($user_id) {
		
		if ($entry) {
		
			$unique_id = uniqid();
			$query = "INSERT INTO " . $GLOBALS['db_name'] . ".websites (site_id, user_id, domain, alerts, creation_time) VALUES ('$unique_id', '$user_id', '$entry', 'disabled', '$current_time')";
			if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
				return "Site added successfully";
			} else {
				display_sql_error();
			}
		} else {
			return "Invalid address";
		}
		
	} else {
		return "Invalid Session";
	}
	
}

function update_profile($firstname, $lastname, $email, $password, $session_id) {
	
	// retrieve user_id and validate session_id
	$user_id = get_user_id($session_id);
	
	if ($user_id) {
		$query = "UPDATE " . $GLOBALS['db_name'] . ".users SET firstname='$firstname', lastname='$lastname', password='$password', email='$email' WHERE user_id='$user_id' LIMIT 1";
		//$result = $GLOBALS['db_connection'] -> query($query);
		
		if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
			return TRUE;
		} else {
			display_sql_error();
		}
		
	} else {
		return "Error: User not found.";
	}
}

function get_list_of_websites($session_id) {
	// based on session id, retrieve list of websites that account holder has
	
	// get the user_id (and check validity of session) based on session_id
	$user_id = get_user_id(make_safe($session_id));
	

	
	if ($user_id) {
		
		$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".websites WHERE user_id = '$user_id' ORDER BY domain ASC";
		$result = $GLOBALS['db_connection'] -> query($query);
		
		if ($result -> num_rows > 0) {
			
			$sites = array();
			
			while($row = $result -> fetch_assoc()) {
				$sites[] = array('site_id' => $row['site_id'], 'domain' => $row['domain']);
			}
			
			return $sites;
			
		} else {
			return FALSE;
		}
		
	} else {
		return "Invalid Session";
	}
}



function get_website_data($session_id, $domain) {
	// retrieve website based on its domain and session_id
	
	// get the user_id (and check validity of session) based on session_id
	$user_id = get_user_id(make_safe($session_id));
	
	$domain = make_safe($domain);
	
	if ($user_id) {
		
		$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".websites WHERE domain = '$domain' AND user_id = '$user_id' LIMIT 1";
		$result = $GLOBALS['db_connection'] -> query($query);
		
		if ($result -> num_rows > 0) {
						
			while($row = $result -> fetch_assoc()) {
				$site_data = array('responses' => $row['responses'], 'site_id' => $row['site_id'], 'downtime' => $row['downtime'], 'creation_time' => $row['creation_time'], 'alerts' => $row['alerts'], 'spam_dbs' => explode(",", $row['spam_dbs']));
			}
			
			return $site_data;
			
		} else {
			return FALSE;
		}
		
	} else {
		return "Invalid Session";
	}
}

function get_last_responses($session_id, $domain) {
	// retrieve last response data for a domain
	
	// get the user_id (and check validity of session) based on session_id
	$user_id = get_user_id(make_safe($session_id));
	
	$domain = make_safe($domain);
	
	if ($user_id) {
		
		$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".websites WHERE domain = '$domain' AND user_id = '$user_id' LIMIT 1";
		$result = $GLOBALS['db_connection'] -> query($query);
		
		if ($result -> num_rows > 0) {
						
			while($row = $result -> fetch_assoc()) {
				$site_data = array( 'responses' => json_decode($row['responses'], true), 'spam_dbs' => explode(",", $row['spam_dbs']));
			}
			
			// if any ports are being monitored
			if (count($site_data['spam_dbs'] > 0)) {
				$databases = array();
				// for each database that is recorded for this domain, get last status
				foreach ($site_data['spam_dbs'] as $database) {
					if ($site_data['responses'][$database] == "") {
						$databases[$database] = 'No data';
					} else {
						$databases[$database] = $site_data[$database]; // array(status, response time)					
					}
				}
								
				return $pings;
			} else {
				return FALSE;
			}
			
		} else {
			return FALSE;
		}
		
	} else {
		return "Invalid Session";
	}
}

function update_website_alerts($session_id, $domain, $alerts_status) {
	// set domain alert status either to enabled or disabled
	$user_id = get_user_id(make_safe($session_id));
	
	$alerts_status = make_safe($alerts_status);
	if ($alerts_status <> 'enabled' && $alerts_status <> 'disabled') {
		return "Error";
	}
	
	
	if ($user_id) {
		
		$query = "UPDATE " . $GLOBALS['db_name'] . ".websites SET alerts='$alerts_status' WHERE user_id='$user_id' AND domain='$domain' LIMIT 1";
		$result = $GLOBALS['db_connection'] -> query($query);
		
		if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
			return TRUE;
		} else {
			display_sql_error();
		}
		
	} else {
		return "Invalid Session";
	}
}

function update_website_databases($session_id, $domain, $databases) {
	// updates domain record with databases that user requires to be monitored
	
	// get the user_id (and check validity of session) based on session_id
	$user_id = get_user_id(make_safe($session_id));
	
	// $ports is a string of csv
	$databases_list = implode(",", $databases);
		
	if ($user_id) {
		
		$query = "UPDATE " . $GLOBALS['db_name'] . ".websites SET spam_dbs='$databases_list' WHERE user_id='$user_id' AND domain='$domain' LIMIT 1";

		if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
			return TRUE;
		} else {
			display_sql_error();
		}
		
	} else {
		return "Invalid Session";
	}
}

function get_current_status_by_response($responses = NULL) {
	// the current overall status of a domain
	
	// go through each response and return
	// 'listed' if any are found
	foreach ($responses as $response) {
		if ($response['status'] == 'listed') {
			return 'listed';
		}
	}
	// check if unlisted is in response, otherwise return 'no data'
	foreach ($responses as $response) {
		if ($response['status'] == 'unlisted') {
			return 'unlisted';
		}
	}
	return "No Data";
}


function get_downtime_by_website_data($website_data) {
	
	$requested_databases = $website_data['spam_dbs'];
	$downtimes = json_decode($website_data['downtime'], true);
	$creation_time = $website_data['creation_time'];
	
	

	// array(database => array( array(start, finish), array(start, finish)), database => array( array(start, finish), array(start, finish)))
	
	// if ports are being monitored
	if (count($requested_databases > 0)) {
		
		$total_downtime = array();
		$current_time = time(); // time now in seconds since epoch
		$old_time = $current_time - (3600 * 24 * 365); // time 1 year ago in seconds since epoch
		
		
	//	print_r($requested_ports);
	//	print_r($downtimes);
		
		// go through each requested port and calculate the difference between start and finish times
		foreach ($requested_databases as $database) {
			$total_downtime[$database] = 0;
			foreach ($downtimes[$database] as $downtime) {
				// check both start and finish time is within last year
				// this will also flush out if recently down and finish time is still null
				//echo "[" . $downtime['start'] . "|" . $downtime['finish'] . "]";
				if ($downtime['finish'] = 'current') { $downtime['finish'] = $current_time; }
				if (($downtime['start'] > $old_time) && ($downtime['finish'] > $old_time)) {
					$total_downtime[$database] += $downtime['finish'] - $downtime['start'];
				}
			}
		}
		
	} else {
		return false;
	}
	
	
	// if creation time is less than a year, then downtime percentage should be based on creation time
	$dt_pc = array();

	foreach ($requested_databases as $database) {
		if ($creation_time > $old_time) {
			$existed_time = $current_time - $creation_time;
			$dt_pc[$database] = ($total_downtime[$database] / $existed_time) *100;
		} else {
			$dt_pc[$database] = ($total_downtime[$database] / (3600*24*365)) *100;
		}
	}

	// return array(downtime % over 1 year, downtime s for 1 year)
	return ($dt_pc);
}


function purge_old_sessions() {
	// go through sessions table and remove any sessions where timestamp is more than 15 minutes old
	$old_time = time() - (15*60); // time 15 minutes ago
	
	$query = "DELETE FROM " . $GLOBALS['db_name'] . ".sessions WHERE session_time < '$old_time'";

	if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
		return TRUE;
	} else {
	 	display_sql_error();
	}
}

function get_user_data($session_id) {
	
	// get the user_id (and check validity of session) based on session_id
	$user_id = get_user_id(make_safe($session_id));
	
	if ($user_id) {
		
		$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".users WHERE user_id = '$user_id' LIMIT 1";
		$result = $GLOBALS['db_connection'] -> query($query);
		
		if ($result -> num_rows > 0) {
			
			$sites = array();
			
			while($row = $result -> fetch_assoc()) {
				$data = array('firstname' => $row['firstname'], 'lastname' => $row['lastname'], 'email' => $row['email'], 'password' => $row['password']);
			}
			
			return $data;
			
		} else {
			return FALSE;
		}
		
	} else {
		return "Invalid Session";
	}
}

function get_user_data_by_user_id($user_id) {
	
	$user_id = make_safe($user_id);
	
	if ($user_id) {
		
		$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".users WHERE user_id = '$user_id' LIMIT 1";
		$result = $GLOBALS['db_connection'] -> query($query);
		
		if ($result -> num_rows > 0) {
			
			$sites = array();
			
			while($row = $result -> fetch_assoc()) {
				$data = array('firstname' => $row['firstname'], 'lastname' => $row['lastname'], 'email' => $row['email']);
			}
			
			return $data;
			
		} else {
			return FALSE;
		}
		
	} else {
		return "Invalid user_id";
	}
}

function kill_session($session_id) {
	// get the user_id (and check validity of session) based on session_id
	$user_id = get_user_id(make_safe($session_id));
	
	if ($user_id) {
		$query = "DELETE FROM " . $GLOBALS['db_name'] . ".sessions WHERE session_id = '$session_id'";
		if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
			return "You have logged out";
		} else {
			display_sql_error();
		}
	} else {
		return "Invalid session";
	}
}

function update_session_timestamp($session_id) {
	// update the timestamp attached to a session_id
	// timestamp is number of seconds since unix epoch
	$session_id = make_safe($session_id);
	$current_time = time();
	$query = "UPDATE " . $GLOBALS['db_name'] . ".sessions SET session_time='$current_time' WHERE session_id='$session_id' LIMIT 1";
	if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
		return TRUE;
	} else {
		display_sql_error();
	}
}

function display_sql_error() {
	echo "Error: " . $GLOBALS['db_connection'] -> error;
	exit();
}

function get_domain_only($url) {

	// function takes url of format 'scheme://username:password@sub1.sub2.hostname.sld.tld/path?arg=value#anchor' and returns only host + sld + tld
	
	$parsed_link = parse_url(strtolower($url));
	$domain = NULL;
	$found = FALSE;

	// cycle through to see if valid sld is used
	foreach ($GLOBALS['slds'] as $sld) {
		if ($sld == substr($parsed_link['host'], -(strlen($sld)))) {
			$domain = substr($parsed_link['host'], 0, (strlen($parsed_link['host']) - strlen($sld))); // remove sld from host
			$domain = explode(".", $domain);
			$domain = end($domain) . $sld; // remove subdomains
			$found = TRUE; // set found to true to prevent further cycling by slds
			break; // skip rest of slds
		}
	}
	
	// if no valid sld, then check valid tlds	
	if ($found == FALSE) {
		foreach ($GLOBALS['tlds'] as $tld) {
			if (($tld == substr($parsed_link['host'], -(strlen($tld))))  && (substr($parsed_link['host'], -(strlen($tld)+1),1) == '.')) {
				$domain = substr($parsed_link['host'], 0, (strlen($parsed_link['host']) - strlen($tld) - 1)); // remove tld from host and extra -1 is to remove last '.'
				$domain = explode(".", $domain);
				$domain = end($domain) . "." . $tld; // remove subdomains	
				break; // skip rest of tlds
			}
		}
	}

	return $domain;

}

function get_users_list() {
	$session_id = make_safe($session_id);
		
	$query = "SELECT * FROM " . $GLOBALS['db_name'] . ".users ORDER BY user_id ASC";
	$result = $GLOBALS['db_connection'] -> query($query);
	
	$users_list = array();
	
	if ($result -> num_rows > 0) {
		while($row = $result -> fetch_assoc()) {
			$users_list[] = array('user_id' => $row['user_id'], 'firstname' => $row['firstname'], 'lastname' => $row['lastname'], 'email' => $row['email']);
		}
		return $users_list;
	} else {
		return FALSE;
	}
}

function delete_user($user_id) {
	// delete all websites related to that user_id
	if ($user_id) {
		$query = "DELETE FROM " . $GLOBALS['db_name'] . ".websites WHERE user_id = '$user_id'";
		if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
			$query = "DELETE FROM " . $GLOBALS['db_name'] . ".users WHERE user_id = '$user_id'";
			if ($GLOBALS['db_connection'] -> query($query) === TRUE) {
				return "User deleted";
			} else {
				display_sql_error();
			}
		} else {
			display_sql_error();
		}
	} else {
		return "Invalid session";
	}
	// delete user from users table
}

global $slds;
global $tlds;

$slds = array('.com.ac', '.net.ac', '.gov.ac', '.org.ac', '.mil.ac', '.co.ae', '.net.ae', '.gov.ae', '.ac.ae', '.sch.ae', '.org.ae', '.mil.ae', '.pro.ae', '.name.ae', '.com.af', '.edu.af', '.gov.af', '.net.af', '.org.af', '.com.al', '.edu.al', '.gov.al', '.mil.al', '.net.al', '.org.al', '.ed.ao', '.gv.ao', '.og.ao', '.co.ao', '.pb.ao', '.it.ao', '.com.ar', '.edu.ar', '.gob.ar', '.gov.ar', '.int.ar', '.mil.ar', '.net.ar', '.org.ar', '.tur.ar', '.gv.at', '.ac.at', '.co.at', '.or.at', '.com.au', '.net.au', '.org.au', '.edu.au', '.gov.au', '.csiro.au', '.asn.au', '.id.au', '.org.ba', '.net.ba', '.edu.ba', '.gov.ba', '.mil.ba', '.unsa.ba', '.untz.ba', '.unmo.ba', '.unbi.ba', '.unze.ba', '.co.ba', '.com.ba', '.rs.ba', '.co.bb', '.com.bb', '.net.bb', '.org.bb', '.gov.bb', '.edu.bb', '.info.bb', '.store.bb', '.tv.bb', '.biz.bb', '.com.bh', '.info.bh', '.cc.bh', '.edu.bh', '.biz.bh', '.net.bh', '.org.bh', '.gov.bh', '.com.bn', '.edu.bn', '.gov.bn', '.net.bn', '.org.bn', '.com.bo', '.net.bo', '.org.bo', '.tv.bo', '.mil.bo', '.int.bo', '.gob.bo', '.gov.bo', '.edu.bo', '.adm.br', '.adv.br', '.agr.br', '.am.br', '.arq.br', '.art.br', '.ato.br', '.b.br', '.bio.br', '.blog.br', '.bmd.br', '.cim.br', '.cng.br', '.cnt.br', '.com.br', '.coop.br', '.ecn.br', '.edu.br', '.eng.br', '.esp.br', '.etc.br', '.eti.br', '.far.br', '.flog.br', '.fm.br', '.fnd.br', '.fot.br', '.fst.br', '.g12.br', '.ggf.br', '.gov.br', '.imb.br', '.ind.br', '.inf.br', '.jor.br', '.jus.br', '.lel.br', '.mat.br', '.med.br', '.mil.br', '.mus.br', '.net.br', '.nom.br', '.not.br', '.ntr.br', '.odo.br', '.org.br', '.ppg.br', '.pro.br', '.psc.br', '.psi.br', '.qsl.br', '.rec.br', '.slg.br', '.srv.br', '.tmp.br', '.trd.br', '.tur.br', '.tv.br', '.vet.br', '.vlog.br', '.wiki.br', '.zlg.br', '.com.bs', '.net.bs', '.org.bs', '.edu.bs', '.gov.bs', 'com.bz', 'edu.bz', 'gov.bz', 'net.bz', 'org.bz', '.ab.ca', '.bc.ca', '.mb.ca', '.nb.ca', '.nf.ca', '.nl.ca', '.ns.ca', '.nt.ca', '.nu.ca', '.on.ca', '.pe.ca', '.qc.ca', '.sk.ca', '.yk.ca', '.co.ck', '.org.ck', '.edu.ck', '.gov.ck', '.net.ck', '.gen.ck', '.biz.ck', '.info.ck', '.ac.cn', '.com.cn', '.edu.cn', '.gov.cn', '.mil.cn', '.net.cn', '.org.cn', '.ah.cn', '.bj.cn', '.cq.cn', '.fj.cn', '.gd.cn', '.gs.cn', '.gz.cn', '.gx.cn', '.ha.cn', '.hb.cn', '.he.cn', '.hi.cn', '.hl.cn', '.hn.cn', '.jl.cn', '.js.cn', '.jx.cn', '.ln.cn', '.nm.cn', '.nx.cn', '.qh.cn', '.sc.cn', '.sd.cn', '.sh.cn', '.sn.cn', '.sx.cn', '.tj.cn', '.tw.cn', '.xj.cn', '.xz.cn', '.yn.cn', '.zj.cn', '.com.co', '.org.co', '.edu.co', '.gov.co', '.net.co', '.mil.co', '.nom.co', '.ac.cr', '.co.cr', '.ed.cr', '.fi.cr', '.go.cr', '.or.cr', '.sa.cr', '.cr', '.ac.cy', '.net.cy', '.gov.cy', '.org.cy', '.pro.cy', '.name.cy', '.ekloges.cy', '.tm.cy', '.ltd.cy', '.biz.cy', '.press.cy', '.parliament.cy', '.com.cy', '.edu.do', '.gob.do', '.gov.do', '.com.do', '.sld.do', '.org.do', '.net.do', '.web.do', '.mil.do', '.art.do', '.com.dz', '.org.dz', '.net.dz', '.gov.dz', '.edu.dz', '.asso.dz', '.pol.dz', '.art.dz', '.com.ec', '.info.ec', '.net.ec', '.fin.ec', '.med.ec', '.pro.ec', '.org.ec', '.edu.ec', '.gov.ec', '.mil.ec', '.com.eg', '.edu.eg', '.eun.eg', '.gov.eg', '.mil.eg', '.name.eg', '.net.eg', '.org.eg', '.sci.eg', '.com.er', '.edu.er', '.gov.er', '.mil.er', '.net.er', '.org.er', '.ind.er', '.rochest.er', '.w.er', '.com.es', '.nom.es', '.org.es', '.gob.es', '.edu.es', '.com.et', '.gov.et', '.org.et', '.edu.et', '.net.et', '.biz.et', '.name.et', '.info.et', '.ac.fj', '.biz.fj', '.com.fj', '.info.fj', '.mil.fj', '.name.fj', '.net.fj', '.org.fj', '.pro.fj', '.co.fk', '.org.fk', '.gov.fk', '.ac.fk', '.nom.fk', '.net.fk', '.fr', '.tm.fr', '.asso.fr', '.nom.fr', '.prd.fr', '.presse.fr', '.com.fr', '.gouv.fr', '.co.gg', '.net.gg', '.org.gg', '.com.gh', '.edu.gh', '.gov.gh', '.org.gh', '.mil.gh', '.com.gn', '.ac.gn', '.gov.gn', '.org.gn', '.net.gn', '.com.gr', '.edu.gr', '.net.gr', '.org.gr', '.gov.gr', '.mil.gr', '.com.gt', '.edu.gt', '.net.gt', '.gob.gt', '.org.gt', '.mil.gt', '.ind.gt', '.com.gu', '.net.gu', '.gov.gu', '.org.gu', '.edu.gu', '.com.hk', '.edu.hk', '.gov.hk', '.idv.hk', '.net.hk', '.org.hk', '.ac.id', '.co.id', '.net.id', '.or.id', '.web.id', '.sch.id', '.mil.id', '.go.id', '.war.net.id', '.ac.il', '.co.il', '.org.il', '.net.il', '.k12.il', '.gov.il', '.muni.il', '.idf.il', '.in', '.co.in', '.firm.in', '.net.in', '.org.in', '.gen.in', '.ind.in', '.ac.in', '.edu.in', '.res.in', '.ernet.in', '.gov.in', '.mil.in', '.nic.in', '.iq', '.gov.iq', '.edu.iq', '.com.iq', '.mil.iq', '.org.iq', '.net.iq', '.ir', '.ac.ir', '.co.ir', '.gov.ir', '.id.ir', '.net.ir', '.org.ir', '.sch.ir', '.dnssec.ir', '.gov.it', '.edu.it', '.co.je', '.net.je', '.org.je', '.com.jo', '.net.jo', '.gov.jo', '.edu.jo', '.org.jo', '.mil.jo', '.name.jo', '.sch.jo', '.ac.jp', '.ad.jp', '.co.jp', '.ed.jp', '.go.jp', '.gr.jp', '.lg.jp', '.ne.jp', '.or.jp', '.co.ke', '.or.ke', '.ne.ke', '.go.ke', '.ac.ke', '.sc.ke', '.me.ke', '.mobi.ke', '.info.ke', '.per.kh', '.com.kh', '.edu.kh', '.gov.kh', '.mil.kh', '.net.kh', '.org.kh', '.com.ki', '.biz.ki', '.de.ki', '.net.ki', '.info.ki', '.org.ki', '.gov.ki', '.edu.ki', '.mob.ki', '.tel.ki', '.km', '.com.km', '.coop.km', '.asso.km', '.nom.km', '.presse.km', '.tm.km', '.medecin.km', '.notaires.km', '.pharmaciens.km', '.veterinaire.km', '.edu.km', '.gouv.km', '.mil.km', '.net.kn', '.org.kn', '.edu.kn', '.gov.kn', '.kr', '.co.kr', '.ne.kr', '.or.kr', '.re.kr', '.pe.kr', '.go.kr', '.mil.kr', '.ac.kr', '.hs.kr', '.ms.kr', '.es.kr', '.sc.kr', '.kg.kr', '.seoul.kr', '.busan.kr', '.daegu.kr', '.incheon.kr', '.gwangju.kr', '.daejeon.kr', '.ulsan.kr', '.gyeonggi.kr', '.gangwon.kr', '.chungbuk.kr', '.chungnam.kr', '.jeonbuk.kr', '.jeonnam.kr', '.gyeongbuk.kr', '.gyeongnam.kr', '.jeju.kr', '.edu.kw', '.com.kw', '.net.kw', '.org.kw', '.gov.kw', '.com.ky', '.org.ky', '.net.ky', '.edu.ky', '.gov.ky', '.com.kz', '.edu.kz', '.gov.kz', '.mil.kz', '.net.kz', '.org.kz', '.com.lb', '.edu.lb', '.gov.lb', '.net.lb', '.org.lb', '.gov.lk', '.sch.lk', '.net.lk', '.int.lk', '.com.lk', '.org.lk', '.edu.lk', '.ngo.lk', '.soc.lk', '.web.lk', '.ltd.lk', '.assn.lk', '.grp.lk', '.hotel.lk', '.com.lr', '.edu.lr', '.gov.lr', '.org.lr', '.net.lr', '.com.lv', '.edu.lv', '.gov.lv', '.org.lv', '.mil.lv', '.id.lv', '.net.lv', '.asn.lv', '.conf.lv', '.com.ly', '.net.ly', '.gov.ly', '.plc.ly', '.edu.ly', '.sch.ly', '.med.ly', '.org.ly', '.id.ly', '.ma', '.net.ma', '.ac.ma', '.org.ma', '.gov.ma', '.press.ma', '.co.ma', '.tm.mc', '.asso.mc', '.co.me', '.net.me', '.org.me', '.edu.me', '.ac.me', '.gov.me', '.its.me', '.priv.me', '.org.mg', '.nom.mg', '.gov.mg', '.prd.mg', '.tm.mg', '.edu.mg', '.mil.mg', '.com.mg', '.com.mk', '.org.mk', '.net.mk', '.edu.mk', '.gov.mk', '.inf.mk', '.name.mk', '.pro.mk', '.com.ml', '.net.ml', '.org.ml', '.edu.ml', '.gov.ml', '.presse.ml', '.gov.mn', '.edu.mn', '.org.mn', '.com.mo', '.edu.mo', '.gov.mo', '.net.mo', '.org.mo', '.com.mt', '.org.mt', '.net.mt', '.edu.mt', '.gov.mt', '.aero.mv', '.biz.mv', '.com.mv', '.coop.mv', '.edu.mv', '.gov.mv', '.info.mv', '.int.mv', '.mil.mv', '.museum.mv', '.name.mv', '.net.mv', '.org.mv', '.pro.mv', '.ac.mw', '.co.mw', '.com.mw', '.coop.mw', '.edu.mw', '.gov.mw', '.int.mw', '.museum.mw', '.net.mw', '.org.mw', '.com.mx', '.net.mx', '.org.mx', '.edu.mx', '.gob.mx', '.com.my', '.net.my', '.org.my', '.gov.my', '.edu.my', '.sch.my', '.mil.my', '.name.my', '.com.nf', '.net.nf', '.arts.nf', '.store.nf', '.web.nf', '.firm.nf', '.info.nf', '.other.nf', '.per.nf', '.rec.nf', '.com.ng', '.org.ng', '.gov.ng', '.edu.ng', '.net.ng', '.sch.ng', '.name.ng', '.mobi.ng', '.biz.ng', '.mil.ng', '.gob.ni', '.co.ni', '.com.ni', '.ac.ni', '.edu.ni', '.org.ni', '.nom.ni', '.net.ni', '.mil.ni', '.com.np', '.edu.np', '.gov.np', '.org.np', '.mil.np', '.net.np', '.edu.nr', '.gov.nr', '.biz.nr', '.info.nr', '.net.nr', '.org.nr', '.com.nr', '.com.om', '.co.om', '.edu.om', '.ac.om', '.sch.om', '.gov.om', '.net.om', '.org.om', '.mil.om', '.museum.om', '.biz.om', '.pro.om', '.med.om', '.edu.pe', '.gob.pe', '.nom.pe', '.mil.pe', '.sld.pe', '.org.pe', '.com.pe', '.net.pe', '.com.ph', '.net.ph', '.org.ph', '.mil.ph', '.ngo.ph', '.i.ph', '.gov.ph', '.edu.ph', '.com.pk', '.net.pk', '.edu.pk', '.org.pk', '.fam.pk', '.biz.pk', '.web.pk', '.gov.pk', '.gob.pk', '.gok.pk', '.gon.pk', '.gop.pk', '.gos.pk', '.pwr.pl', '.com.pl', '.biz.pl', '.net.pl', '.art.pl', '.edu.pl', '.org.pl', '.ngo.pl', '.gov.pl', '.info.pl', '.mil.pl', '.waw.pl', '.warszawa.pl', '.wroc.pl', '.wroclaw.pl', '.krakow.pl', '.katowice.pl', '.poznan.pl', '.lodz.pl', '.gda.pl', '.gdansk.pl', '.slupsk.pl', '.radom.pl', '.szczecin.pl', '.lublin.pl', '.bialystok.pl', '.olsztyn.pl', '.torun.pl', '.gorzow.pl', '.zgora.pl', '.biz.pr', '.com.pr', '.edu.pr', '.gov.pr', '.info.pr', '.isla.pr', '.name.pr', '.net.pr', '.org.pr', '.pro.pr', '.est.pr', '.prof.pr', '.ac.pr', '.com.ps', '.net.ps', '.org.ps', '.edu.ps', '.gov.ps', '.plo.ps', '.sec.ps', '.co.pw', '.ne.pw', '.or.pw', '.ed.pw', '.go.pw', '.belau.pw', '.arts.ro', '.com.ro', '.firm.ro', '.info.ro', '.nom.ro', '.nt.ro', '.org.ro', '.rec.ro', '.store.ro', '.tm.ro', '.www.ro', '.co.rs', '.org.rs', '.edu.rs', '.ac.rs', '.gov.rs', '.in.rs', '.com.sb', '.net.sb', '.edu.sb', '.org.sb', '.gov.sb', '.com.sc', '.net.sc', '.edu.sc', '.gov.sc', '.org.sc', '.co.sh', '.com.sh', '.org.sh', '.gov.sh', '.edu.sh', '.net.sh', '.nom.sh', '.com.sl', '.net.sl', '.org.sl', '.edu.sl', '.gov.sl', '.gov.st', '.saotome.st', '.principe.st', '.consulado.st', '.embaixada.st', '.org.st', '.edu.st', '.net.st', '.com.st', '.store.st', '.mil.st', '.co.st', '.edu.sv', '.gob.sv', '.com.sv', '.org.sv', '.red.sv', '.co.sz', '.ac.sz', '.org.sz', '.com.tr', '.gen.tr', '.org.tr', '.biz.tr', '.info.tr', '.av.tr', '.dr.tr', '.pol.tr', '.bel.tr', '.tsk.tr', '.bbs.tr', '.k12.tr', '.edu.tr', '.name.tr', '.net.tr', '.gov.tr', '.web.tr', '.tel.tr', '.tv.tr', '.co.tt', '.com.tt', '.org.tt', '.net.tt', '.biz.tt', '.info.tt', '.pro.tt', '.int.tt', '.coop.tt', '.jobs.tt', '.mobi.tt', '.travel.tt', '.museum.tt', '.aero.tt', '.cat.tt', '.tel.tt', '.name.tt', '.mil.tt', '.edu.tt', '.gov.tt', '.edu.tw', '.gov.tw', '.mil.tw', '.com.tw', '.net.tw', '.org.tw', '.idv.tw', '.game.tw', '.ebiz.tw', '.club.tw', '.com.mu', '.gov.mu', '.net.mu', '.org.mu', '.ac.mu', '.co.mu', '.or.mu', '.ac.mz', '.co.mz', '.edu.mz', '.org.mz', '.gov.mz', '.com.na', '.co.na', '.ac.nz', '.co.nz', '.cri.nz', '.geek.nz', '.gen.nz', '.govt.nz', '.health.nz', '.iwi.nz', '.maori.nz', '.mil.nz', '.net.nz', '.org.nz', '.parliament.nz', '.school.nz', '.abo.pa', '.ac.pa', '.com.pa', '.edu.pa', '.gob.pa', '.ing.pa', '.med.pa', '.net.pa', '.nom.pa', '.org.pa', '.sld.pa', '.com.pt', '.edu.pt', '.gov.pt', '.int.pt', '.net.pt', '.nome.pt', '.org.pt', '.publ.pt', '.com.py', '.edu.py', '.gov.py', '.mil.py', '.net.py', '.org.py', '.com.qa', '.edu.qa', '.gov.qa', '.mil.qa', '.net.qa', '.org.qa', '.asso.re', '.com.re', '.nom.re', '.ac.ru', '.adygeya.ru', '.altai.ru', '.amur.ru', '.arkhangelsk.ru', '.astrakhan.ru', '.bashkiria.ru', '.belgorod.ru', '.bir.ru', '.bryansk.ru', '.buryatia.ru', '.cbg.ru', '.chel.ru', '.chelyabinsk.ru', '.chita.ru', '.chukotka.ru', '.chuvashia.ru', '.com.ru', '.dagestan.ru', '.e-burg.ru', '.edu.ru', '.gov.ru', '.grozny.ru', '.int.ru', '.irkutsk.ru', '.ivanovo.ru', '.izhevsk.ru', '.jar.ru', '.joshkar-ola.ru', '.kalmykia.ru', '.kaluga.ru', '.kamchatka.ru', '.karelia.ru', '.kazan.ru', '.kchr.ru', '.kemerovo.ru', '.khabarovsk.ru', '.khakassia.ru', '.khv.ru', '.kirov.ru', '.koenig.ru', '.komi.ru', '.kostroma.ru', '.kranoyarsk.ru', '.kuban.ru', '.kurgan.ru', '.kursk.ru', '.lipetsk.ru', '.magadan.ru', '.mari.ru', '.mari-el.ru', '.marine.ru', '.mil.ru', '.mordovia.ru', '.mosreg.ru', '.msk.ru', '.murmansk.ru', '.nalchik.ru', '.net.ru', '.nnov.ru', '.nov.ru', '.novosibirsk.ru', '.nsk.ru', '.omsk.ru', '.orenburg.ru', '.org.ru', '.oryol.ru', '.penza.ru', '.perm.ru', '.pp.ru', '.pskov.ru', '.ptz.ru', '.rnd.ru', '.ryazan.ru', '.sakhalin.ru', '.samara.ru', '.saratov.ru', '.simbirsk.ru', '.smolensk.ru', '.spb.ru', '.stavropol.ru', '.stv.ru', '.surgut.ru', '.tambov.ru', '.tatarstan.ru', '.tom.ru', '.tomsk.ru', '.tsaritsyn.ru', '.tsk.ru', '.tula.ru', '.tuva.ru', '.tver.ru', '.tyumen.ru', '.udm.ru', '.udmurtia.ru', '.ulan-ude.ru', '.vladikavkaz.ru', '.vladimir.ru', '.vladivostok.ru', '.volgograd.ru', '.vologda.ru', '.voronezh.ru', '.vrn.ru', '.vyatka.ru', '.yakutia.ru', '.yamal.ru', '.yekaterinburg.ru', '.yuzhno-sakhalinsk.ru', '.ac.rw', '.co.rw', '.com.rw', '.edu.rw', '.gouv.rw', '.gov.rw', '.int.rw', '.mil.rw', '.net.rw', '.com.sa', '.edu.sa', '.gov.sa', '.med.sa', '.net.sa', '.org.sa', '.pub.sa', '.sch.sa', '.com.sd', '.edu.sd', '.gov.sd', '.info.sd', '.med.sd', '.net.sd', '.org.sd', '.tv.sd', '.a.se', '.ac.se', '.b.se', '.bd.se', '.c.se', '.d.se', '.e.se', '.f.se', '.g.se', '.h.se', '.i.se', '.k.se', '.l.se', '.m.se', '.n.se', '.o.se', '.org.se', '.p.se', '.parti.se', '.pp.se', '.press.se', '.r.se', '.s.se', '.t.se', '.tm.se', '.u.se', '.w.se', '.x.se', '.y.se', '.z.se', '.com.sg', '.edu.sg', '.gov.sg', '.idn.sg', '.net.sg', '.org.sg', '.per.sg', '.art.sn', '.com.sn', '.edu.sn', '.gouv.sn', '.org.sn', '.perso.sn', '.univ.sn', '.com.sy', '.edu.sy', '.gov.sy', '.mil.sy', '.net.sy', '.news.sy', '.org.sy', '.ac.th', '.co.th', '.go.th', '.in.th', '.mi.th', '.net.th', '.or.th', '.ac.tj', '.biz.tj', '.co.tj', '.com.tj', '.edu.tj', '.go.tj', '.gov.tj', '.info.tj', '.int.tj', '.mil.tj', '.name.tj', '.net.tj', '.nic.tj', '.org.tj', '.test.tj', '.web.tj', '.agrinet.tn', '.com.tn', '.defense.tn', '.edunet.tn', '.ens.tn', '.fin.tn', '.gov.tn', '.ind.tn', '.info.tn', '.intl.tn', '.mincom.tn', '.nat.tn', '.net.tn', '.org.tn', '.perso.tn', '.rnrt.tn', '.rns.tn', '.rnu.tn', '.tourism.tn', '.ac.tz', '.co.tz', '.go.tz', '.ne.tz', '.or.tz', '.biz.ua', '.cherkassy.ua', '.chernigov.ua', '.chernovtsy.ua', '.ck.ua', '.cn.ua', '.co.ua', '.com.ua', '.crimea.ua', '.cv.ua', '.dn.ua', '.dnepropetrovsk.ua', '.donetsk.ua', '.dp.ua', '.edu.ua', '.gov.ua', '.if.ua', '.in.ua', '.ivano-frankivsk.ua', '.kh.ua', '.kharkov.ua', '.kherson.ua', '.khmelnitskiy.ua', '.kiev.ua', '.kirovograd.ua', '.km.ua', '.kr.ua', '.ks.ua', '.kv.ua', '.lg.ua', '.lugansk.ua', '.lutsk.ua', '.lviv.ua', '.me.ua', '.mk.ua', '.net.ua', '.nikolaev.ua', '.od.ua', '.odessa.ua', '.org.ua', '.pl.ua', '.poltava.ua', '.pp.ua', '.rovno.ua', '.rv.ua', '.sebastopol.ua', '.sumy.ua', '.te.ua', '.ternopil.ua', '.uzhgorod.ua', '.vinnica.ua', '.vn.ua', '.zaporizhzhe.ua', '.zhitomir.ua', '.zp.ua', '.zt.ua', '.ac.ug', '.co.ug', '.go.ug', '.ne.ug', '.or.ug', '.org.ug', '.sc.ug', '.ac.uk', '.bl.uk', '.british-library.uk', '.co.uk', '.cym.uk', '.gov.uk', '.govt.uk', '.icnet.uk', '.jet.uk', '.lea.uk', '.ltd.uk', '.me.uk', '.mil.uk', '.mod.uk', '.national-library-scotland.uk', '.nel.uk', '.net.uk', '.nhs.uk', '.nic.uk', '.nls.uk', '.org.uk', '.orgn.uk', '.parliament.uk', '.plc.uk', '.police.uk', '.sch.uk', '.scot.uk', '.soc.uk', '.dni.us', '.fed.us', '.isa.us', '.kids.us', '.nsn.us', '.com.uy', '.edu.uy', '.gub.uy', '.mil.uy', '.net.uy', '.org.uy', '.co.ve', '.com.ve', '.edu.ve', '.gob.ve', '.info.ve', '.mil.ve', '.net.ve', '.org.ve', '.web.ve', '.co.vi', '.com.vi', '.k12.vi', '.net.vi', '.org.vi', '.ac.vn', '.biz.vn', '.com.vn', '.edu.vn', '.gov.vn', '.health.vn', '.info.vn', '.int.vn', '.name.vn', '.net.vn', '.org.vn', '.pro.vn', '.co.ye', '.com.ye', '.gov.ye', '.ltd.ye', '.me.ye', '.net.ye', '.org.ye', '.plc.ye', '.ac.yu', '.co.yu', '.edu.yu', '.gov.yu', '.org.yu', '.ac.za', '.agric.za', '.alt.za', '.bourse.za', '.city.za', '.co.za', '.cybernet.za', '.db.za', '.edu.za', '.gov.za', '.grondar.za', '.iaccess.za', '.imt.za', '.inca.za', '.landesign.za', '.law.za', '.mil.za', '.net.za', '.ngo.za', '.nis.za', '.nom.za', '.olivetti.za', '.org.za', '.pix.za', '.school.za', '.tm.za', '.web.za', '.ac.zm', '.co.zm', '.com.zm', '.edu.zm', '.gov.zm', '.net.zm', '.org.zm', '.sch.zm');

$tlds = array('ac', 'ad', 'ae', 'aero', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'arpa', 'as', 'asia', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'biz', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cat', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'com', 'coop', 'cr', 'cu', 'cv', 'cw', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'edu', 'ee', 'eg', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'info', 'int', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jobs', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'name', 'nc', 'ne', 'net', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'org', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt', 'pw', 'py', 'qa', 're', 'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'su', 'sv', 'sx', 'sy', 'sz', 'tc', 'td', 'tel', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'travel', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'xxx', 'ye', 'yt', 'za', 'zm', 'zw');



?>