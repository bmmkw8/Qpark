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
	if($vars['action'] == 'login'){
		login($vars['email'], $vars['password']);
	}
	if($vars['action'] == 'register'){
		register($vars['email'], $vars['password'],$vars['fname'],$vars['lname'],$vars['pawprint'],$vars['make'],$vars['model'], $vars['year'], $vars['plate'], $vars['color'], $vars['state']);
	}
	if($vars['action'] == 'update'){
		updateaacountinfo($vars['fname'],$vars['lname'], $vars['password'], $vars['email']);
	}
	if($vars['action'] == 'payment'){
		payment($vars['email'],$vars['garage'], $vars['duration'], $vars['price']);
	}
	if($vars['action'] == 'logout'){
		logout();
	}
	if($vars['action'] == 'viewticket'){
		viewticket($vars['email']);
	}
    if($vars['action'] == 'addtime'){
        addtime($vars['email'], $vars['duration']);
    }
    if($vars['action'] == 'readaccountinfo'){
        readaccountinfo($vars['email']);
    }
    if($vars['action'] == 'readvehicleinformation'){
        readvehicleinformation($vars['email']);
    }
} else{
	echo "no action selected";
}


function login($email, $password) {
//echo $password;

$query = sprintf("SELECT user_email from User WHERE user_email='%s';",
mysql_real_escape_string($email));
$results = mysql_query($query);
$row = mysql_fetch_assoc($results);
mysql_free_result($results);
$email = $row['user_email'];

if(strcmp($email, $row) == 0)
{
$query1 = sprintf("SELECT user_password, user_permission, user_isactive from User WHERE user_email='%s'",
mysql_real_escape_string($email));
$results1 = mysql_query($query1);
$row1 = mysql_fetch_assoc($results1);
mysql_free_result($results1);
mysql_close($link);
$salt = sha1($email);
$saltedPass = sha1($password . $salt);


if(strcmp($row1['user_isactive'], "true") == 0)
{

    //comparing password in database with users input
        if(strcmp($saltedPass, $row1['user_password']) == 0)
        {

            if(strcmp($row1['user_permission'], "a") == 0 || strcmp($row1['user_permission'], "c") == 0)
            {
            
                if($row1['user_permission'] == 'a')
                {

                    if(isset($email))
                    {
                        $validation = array('user' => $email,'user_permission' => $row1['user_permission'], 'login_success' => true);
                        echo json_encode($validation);
                    } else {
                        $validation = array('user' => $email,'user_permission' => $row1['user_permission'], 'login_success' => false);
                        echo json_encode($validation);
                    }

                }
                elseif($row1['user_permission'] == 'c')
                {
            
                    if(isset($email))
                    {
                        $validation = array('user' => $email, 'user_permission' => $row1['user_permission'], 'login_success' => true);
                        echo json_encode($validation);
                    } else {
                        $validation = array('user' => $email,'user_permission' => $row1['user_permission'], 'login_success' => false);
                        echo json_encode($validation);
                    }

                }
		 
            }
            else
            {
                echo '<br/>';
                echo"Incorrect permission";
                echo '<br/>';
            }
	   }
        else
        {
            echo '<br/>';
            echo"Invalid password";
            echo '<br/>';
        }
    }
    else
    {
        echo '<br/>';
        echo"Banned Account";
        echo '<br/>';
    }
}
else
{
        echo '<br/>';
        echo "Invalid username";
        echo '<br/>';
}
}

function Register($emailParam, $passwordParam, $fnameParam, $lnameParam, $pawprintParam, $makeParam, $modelParam, $yearParam, $licensePlate, $colorParam, $stateParam){

//session_start();

$query = sprintf("SELECT user_email from User WHERE user_email='%s'",
mysql_real_escape_string($emailParam));



$results = mysql_query($query);

$row = mysql_fetch_assoc($results);



//check if username exists
  if(strcmp($row, $emailParam)==0)
{
        echo "Username already taken.";

}
//if not exists insert new name
else
{
	//Set and Insert User Info
    $email = $emailParam;
    $salt = sha1($emailParam);
    $password = sha1($passwordParam . $salt);
    $fname = $fnameParam;
    $lname = $lnameParam;
    $permission = "a";
    $pawprint = $pawprintParam;
    $user_notification_time = 15;
    $user_notification_status = 1;
    $isactive = "true";
    $url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=";
    $qrcode = $url . $salt;
    $datetime = date('Y/m/d') . " " .date('g:i:s');

	mysql_query("Insert INTO User VALUES (NULL, '$email', '$password', '$salt', '$fname', '$lname', '$permission', '$pawprint', '$isactive', '$qrcode', '$user_notification_status', '$user_notification_time', '$datetime')");


	// Query for user_id for user that was just created
	$query = sprintf("SELECT user_id from User WHERE user_email='%s'",
	mysql_real_escape_string($emailParam));

	$results = mysql_query($query);

	$row = mysql_fetch_assoc($results);

	// Set and Insert User Vehicle Info
	$make = $makeParam;
	$model = $modelParam;
	$year = $yearParam;
	$plate = $licensePlate;
	$color = $colorParam;
	$state = $stateParam;
	$user_id = $row['user_id'];

	$veh = mysql_query("Insert into Vehicle VALUES(NULL, '$make', '$model', '$year', '$plate', '$color', '$state','$user_id')");

	//echo $veh;

	if(!$veh)
	{
	       echo "failed";;
	}
	else
	{
	        echo "success";
	}

	}

}
function updateaacountinfo($fname, $lname, $passwordParam, $emailParam){

$email = $emailParam;
$salt = sha1($emailParam);
$password = sha1($passwordParam . $salt);


    $query = sprintf("UPDATE User SET user_firstname = '%s', user_lastname = '%s', user_password = '%s' WHERE user_email = '%s'",mysql_real_escape_string($fname), 
        mysql_real_escape_string($lname), mysql_real_escape_string($password), mysql_real_escape_string($email));

$results = mysql_query($query);

mysql_free_result($results);

mysql_close($link);


}

function payment($emailParam, $garageParam, $parkDurationParam, $priceParam){

$query = sprintf("SELECT user_id, user_notification_time from User WHERE user_email='%s'",
mysql_real_escape_string($emailParam));

$results = mysql_query($query);

$row = mysql_fetch_assoc($results);

//echo $row['user_id'];
//echo $row['user_notification_time'];

$vehicleUserID = $row['user_id'];
//$notification_time = $row['user_notification_time'];

$query1 = sprintf("SELECT vehicle_id from Vehicle WHERE vehicle_userid ='%s'", mysql_real_escape_string($vehicleUserID));

$results1 = mysql_query($query1);

$row1 = mysql_fetch_assoc($results1);

//echo $row1['vehicle_id'];

 $vehicleId = $row1['vehicle_id'];
 $garage = $garageParam;
 $datetime = date('Y/m/d') . " " .date('g:i:s');
 $isactive = "true";
 $price = $priceParam;
 $duration = $parkDurationParam;
 $isNotified = 0;

echo $datetime;
echo $garage;
echo $isNotified;
echo $vehicleId;
echo $isactive;
echo $duration;
echo $price;


if($notification_time == 15)
{
    $notificationTime = 15;
}
elseif($notification_time == 30)
{
    $notificationTime = 30;
}
elseif($notification_time == null)
{
    $notificationTime = 15;
}

echo $notificationTime;

 $query2 = mysql_query("Insert into Park VALUES(NULL, '$datetime', '$duration', '$garage', NULL, '$price', NULL, '$isactive', '$notificationTime', '$isNotified', '$vehicleId')");
                $results2 = mysql_query($query2);


                                if($query2)
                                {
                                        //header("Location: home.php");
                                	echo "Payment Success";
                                }
mysql_free_result($results);
mysql_free_result($results1);
mysql_free_result($results2);

mysql_close($link);                              
}



function viewticket($emailParam){

$query = sprintf("Select user_id from User where user_email='%s'", mysql_real_escape_string($emailParam));

$result = mysql_query($query);

$row = mysql_fetch_assoc($result);

$query3 = sprintf("Select * from Ticket where ticket_userid ='%s' AND ticket_isactive = 'true'", mysql_real_escape_string($row['user_id']));

$result3 = mysql_query($query3);

$allTickets = is_array();
$i = 0;

while($row3 = mysql_fetch_assoc($result3)) {

    $ticketInfo = array('ticket_date' => $row3['ticket_date'], 'ticket_time' => $row3['ticket_time'], 'ticket_price' => $row3['ticket_price'], 
<<<<<<< HEAD
    'ticket_violation' => $row3['ticket_violation'], 'ticket_notes' => $row3['ticket_notes'], 'ticket_employee_id' => $row3['ticket_employee_id']);  
    $allTickets[$i] = json_encode($ticketInfo);
=======
    'ticket_violation' => $row3['ticket_violation'], 'ticket_notes' => $row3['ticket_notes'], 'ticket_employee_id' => $row3['ticket_employee_id']);
    
    $allTickets[$i] = $ticketInfo;
>>>>>>> ad22e6ccfd009c5bcbd5c54c6cc117265df7c166
    $i++;
}

echo json_encode($allTickets);
mysql_free_result($result);
mysql_close($link);
   
}

function Logout(){

        session_start();
        session_destroy();
        $_SESSION = array();
        //header("Location: index.php");
        echo "Logout";
}

function addtime($emailParam, $timeToAdd, $priceParam)
{

    $query = sprintf("SELECT user_id from User WHERE user_email='%s'",
    mysql_real_escape_string($emailParam));
    $results = mysql_query($query);
    $row = mysql_fetch_assoc($results);
    $vehicleUserID = $row['user_id'];

    $query1 = sprintf("SELECT vehicle_id from Vehicle where vehicle_userid='%s'", 
    mysql_real_escape_string($vehicleUserID));
    $result1 = mysql_query($query1);
    $row1 = mysql_fetch_assoc($result1);
    $parkvehicleid = $row1['vehicle_id'];
      
    $query5 = sprintf("UPDATE Park SET park_addtime = '%s' WHERE park_vehicleid = '%s'",
    mysql_real_escape_string($timeToAdd), mysql_real_escape_string($parkvehicleid));
    $result5 = mysql_query($query5);

    //$email = $emailParam;
    //$duration = $parkduration;

    echo $parkvehicleid;
    echo '<br/>';
    
    $duration = New datetime();

    $query3 = sprintf("SELECT park_duration from Park where park_vehicleid= '%s'", 
    mysql_real_escape_string($parkvehicleid));
    $result2 = mysql_query($query2);

     
    //$query4 = sprintf("UPDATE Park SET park_price = (park_price + park_addprice) WHERE park_vehicleid = '%s'",
    //mysql_real_escape_string($parkvehicleid));
    //$result4 = mysql_query($query4);

    

    $duration->modify("+' . $timeToAdd . '");
 


    /*echo $parkdurationold;
    echo '<br/>';
    echo $parkduration;
    echo '<br/>';*/

    

    /*echo '<br/>';
    $duration = $parkdurationold + $parkduration;
    
    $sumtime = date("H:i:s", $duration);
    echo $sumtime;*/

    $query2 = sprintf("UPDATE Park SET park_duration = '%s' WHERE park_vehicleid = '%s'",
    mysql_real_escape_string($duration), mysql_real_escape_string($parkvehicleid));
    $result2 = mysql_query($query2);

    if($result2)
    {
        echo "successful";
    }

    mysql_free_result($results);
    mysql_free_result($result1);
    mysql_free_result($result2);
    mysql_free_result($result5);
    mysql_free_result($result4);

    mysql_close($link);

}

function readaccountinfo($emailParam){

$query = sprintf("Select user_email, user_firstname, user_lastname, user_pawprint, user_notification_time, user_qrcode from User where user_email= '%s'",
                                mysql_real_escape_string($emailParam));
$result = mysql_query($query);
$row = mysql_fetch_assoc($result);

$accountInfo = array('user_email' => $row['user_email'], 'user_firstname' => $row['user_firstname'], 'user_lastname' => $row['user_lastname'], 
    'user_pawprint' => $row['user_pawprint'], 'user_notification_time' => $row['user_notification_time'], 'user_qrcode' => $row['user_qrcode']);
        echo json_encode($accountInfo);


mysql_free_result($result);
mysql_close($link);

}

function readvehicleinformation($emailParam){

$query = sprintf("Select user_id from User where user_email= '%s'", mysql_real_escape_string($emailParam));
$result = mysql_query($query);
$row = mysql_fetch_assoc($result);
$vehicle_userid = $row['user_id'];

$query1 = sprintf("Select * from Vehicle where vehicle_userid = '%s'", mysql_real_escape_string($vehicle_userid));
$result1 = mysql_query($query1);
$row1 = mysql_fetch_assoc($result1);

$vehicleInfo = array('vehicle_make' => $row1['vehicle_make'], 'vehicle_model' => $row1['vehicle_model'], 'vehicle_year' => $row1['vehicle_year'], 
    'vehicle_color' => $row1['vehicle_color'], 'vehicle_state' => $row1['vehicle_state'], 'vehicle_plate' => $row1['vehicle_plate']);
        echo json_encode($vehicleInfo);


mysql_free_result($result);
mysql_free_result($result1);
mysql_close($link);

}

?>