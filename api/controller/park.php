<?php

header("Access-Control-Allow-Origin: *");
$link = mysql_connect('dbhost-mysql.cs.missouri.edu', 'cs4970s14grp1', 'VetMh^J=B1');
mysql_select_db("cs4970s14grp1");

if (!$link) {
    die('Could not connect: ' . mysql_error());
}


if(!empty($_GET)){
	$vars = $_GET;
}elseif(!empty($_POST)){
	$vars = $_POST;
}else{
	$vars = null;
}

if(isset($vars['action']) && $vars['action'] != ''){
<<<<<<< HEAD
	if($vars['action'] == 'getParkValidity'){
		getParkValidity($vars['employeeGarage'], $vars['userId']);
	}
=======
	if($vars['action'] == 'returnScanInfo'){
        getParkValidity($vars['employeeGarage'], $vars['qrId']);
	    getVehicleInfo($vars['employeeGarage'], $vars['qrId']);
    }
>>>>>>> bc7145e06b424de02e27b7e3cafd3dceae6383e7
}


function getParkValidity($employeeGarage, $qrId){

    $garageValidity = 0;

	date_default_timezone_set('America/Chicago');
	$currTime = date('m/d/Y h:i:s');

    $db_result = mysql_query("SELECT user_id FROM User WHERE user_qrcodeid = $qrId");
    $row = mysql_fetch_array($db_result, MYSQL_NUM);
    $userId = $row[0];

	$db_result = mysql_query("SELECT vehicle_id FROM Vehicle WHERE vehicle_userid = $userId");
	$row = mysql_fetch_array($db_result, MYSQL_NUM);
	$vehicleId = $row[0];

	$db_result = mysql_query("SELECT park_status FROM Park WHERE park_vehicleid = $vehicleId ORDER BY park_time ASC");
	$row = mysql_fetch_array($db_result, MYSQL_NUM);
	$timeValidity = $row[0];


<<<<<<< HEAD
    return timevalidity;
    
=======
    //echo $timeValidity;
>>>>>>> bc7145e06b424de02e27b7e3cafd3dceae6383e7

	$db_result = mysql_query("SELECT park_garage FROM Park WHERE park_vehicleid = $vehicleId ORDER BY park_time ASC");
    $row = mysql_fetch_array($db_result, MYSQL_NUM);
    $parkGarage = $row[0];

    if ($employeeGarage == $parkGarage){
        $garageValidity = 1;
    }

<<<<<<< HEAD
    return garageValidity;
=======
    $garageValidity = json_encode($garageValidity);


    //echo $garageValidity;
>>>>>>> bc7145e06b424de02e27b7e3cafd3dceae6383e7

    //$query = "UPDATE Park SET park_status=0 WHERE (park_time + park_duration) > NOW() AND park_status NOT LIKE 0";


    //"UPDATE Park SET park_status = 'false' WHERE (park_time + park_duration) <= NOW()"; //


    if ($timeValidity == 1 && $garageValidity == 1) {
        $violationCode = 0;
        $violationMessage = 'No Violations';
    }
    else if ($timeValidity == 1 && $garageValidity == 0){
        $violationCode = 1;
        $violationMessage = 'Invalid Garage';
    }
    else if ($timeValidity = 0){
        $violationCode = 2;
        $violationMessage = 'Invalid Time';
    }

    $violationCode = json_encode($violationCode);
    echo $violationCode;

    $violationMessage = json_encode($violationMessage);
    echo $violationMessage;
  

}


?>