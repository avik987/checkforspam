<?php

// $responses_array = json_decode($website_data['responses'], true);
					// array of db->(response, time),db->(response, time)
					
					

//$array = array('Spamhaus'=>array('status'=>'unlisted', 'time' => '78'), 'DroneBL'=>array('status'=>'listed', 'time' => '23'));

$array = array('DroneBL' => array( array('start' => 1470684441, 'finish' => 'current')));

echo json_encode($array);

?>