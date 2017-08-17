<?php

include "config.php";
if(isset($_POST['session_id']) && !empty($_POST['session_id'])){

          // Create connection
//    $_connection = new mysqli($GLOBALS['db_servername'], $GLOBALS['db_username'], $GLOBALS['db_password']);
//        // Check connection
//        if ($_connection -> connect_error) {
//            die("Connection failed: " . $_connection -> connect_error);
//        }

//    $result = $_POST['session_id'];
    //$result = get_users_list($_POST['session_id']);
    echo $query = "SELECT * FROM `" . $GLOBALS['db_name'] . ".users`";
    $result = $GLOBALS['db_connection']->query($query);die;

    echo $result;die;
}
