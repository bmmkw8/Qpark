<html>
<head>
<title>Log in</title>
</head>
<body>
        <form action='index.php' method='POST'>
                <label id='email'>Email: </label>
                <input type='text' name='email' /> <br />
                <label id='password'>Password: </label>
                <input type='password' name='password' /> <br />
                <input type='submit' value='Submit' name='submit' />
                        </form>
        <a onlick="myFunction()">Click here</a> to register.
</body>
</html>
<script type="text/javascript">
function myFunction() {

    $.ajax({
        url: 'loginController',
        type: 'POST',
        dataType: "json",
        data: {
            email: $('#email').val(),
            password: $('#password').val()
        },
        success: function(data){
            alert(JSON.stringify(data));
    });

}
</script>
<?php

$username = 'Brandon';
$password = 'Guest';
$email = 'brandon@test.com';

// Create login functions
// Login Function

class User {

function Logout(){

        session_start();
        session_destroy();
        $_SESSION = array();
        header("Location: index.php");


}


function RegisterVehicle($make, $model, $year, $plate, $color, $state){

session_start();
if(isset($_POST['submit']))
        {


$link = mysql_connect('localhost', 'root', 'root');
                        if (!$link) {
                        die('Could not connect: ' . mysql_error());
                        }
                                //echo 'Connected successfully';


                        $email =  $_SESSION['email'];


                                $query = sprintf("SELECT user_id from rdtg6.User WHERE user_email='%s'",
                                mysql_real_escape_string($email));





                                $results = mysql_query($query);

                                $row = mysql_fetch_assoc($results);




                                $make = $_POST['make'];
                                $model = $_POST['model'];
                                $year = '1990';
                                $plate = $_POST['plate'];
                                $color = $_POST['color'];
                                $state = $_POST['state'];
                                $user_id = $row['user_id'];


 $veh = mysql_query("Insert into rdtg6.Vehicle VALUES(NULL, '$make', '$model', 1990, '$plate', '$color', '$state','$user_id')");

                                if(!$veh)
                                                {
                                                        header("Location: register.php");
                                                }
                                else
                                                {
                                                        header("Location: home.php");
                                                }


}



}

function Register($email, $password, $firstname, $lastname, $pawprint, ){

session_start();


        if(isset($_POST['submit']))
        {


                if(!$_POST['email'])
                {
                        echo '<br/>';
                        echo 'Error: Some data was invalid. Please try again.';
                        echo '<br/>';
                        exit;
                }
                if(!$_POST['password'])
                {
                        echo '<br/>';
                        echo 'Error: Some data was invalid. Please try again.';
                        echo '<br/>';
                        exit;
                }
                if(!$_POST['confirm-password'])
                {
                        echo '<br/>';
                        echo 'Error: Some data was invalid. Please try again.';
                        echo '<br/>';
                        exit;
                }
                //check if passwords match
                if(strcmp($_POST['password'], $_POST['confirm-password'])==0)
                {

                        $link = mysql_connect('localhost', 'root', 'root');
                        if (!$link) {
                        die('Could not connect: ' . mysql_error());
                        }
                                //echo 'Connected successfully';


                                $query = sprintf("SELECT user_email from rdtg6.User WHERE user_email='%s'",
                                mysql_real_escape_string($_POST['email']));



                                $results = mysql_query($query);

                                $row = mysql_fetch_assoc($results);



                                        //check if username exists
                                          if(strcmp($row, $_POST['email'])==0)
                                        {
                                                echo "Username already taken.";

                                        }
                                        //if not exists insert new name
                                        else
                                        {

                                                $email = $_POST['email'];
                                                $salt = sha1($_POST['email']);
                                                $password = sha1($_POST['password'] . $salt);
                                                $fname = $_POST['firstname'];
                                                $lname = $_POST['lastname'];
                                                $permission = "a";
                                                $pawprint = $_POST['pawprint'];
                                                $isactive = "true";
                                                $qrcode = "qr";
                                                $datetime = '2014-02-12 00:00:00';


                                                mysql_query("Insert INTO rdtg6.User VALUES (NULL, '$email', '$password', '$fname', '$lname', '$permission', '$pawprint', '$isactive', '$qrcode', '$datetime')");


                                        mysql_close($link);


//create session to send username accross pages
                                                                $_SESSION['email'] = $_POST['email'];

                                                if(isset($_SESSION['email']))
                                                {
                                                        header("Location: vehicleregister.php");
                                                }

                                    }



                }

                else
                {
                        echo'<br/>';
                        echo"password does not match confirmation password";
                }





        }


}

	function login($email, $password) {

$link = mysql_connect('localhost', 'root', 'root');
                        if (!$link) {
                        die('Could not connect: ' . mysql_error());
                        }
                                //echo 'Connected successfully';


                                $query = sprintf("SELECT user_email from User WHERE user_email='%s'",
                                mysql_real_escape_string($email));



                                $results = mysql_query($query);

                                $row = mysql_fetch_assoc($results);
      
        mysql_free_result($results);

      //  echo $row['user_email'];
        $email = $row['user_email'];


                //comparing password with confirm password
                if(strcmp($email, $row) == 0)
                {

      //echo "here";

                        //getting password to compare with one they enteres
                        //$query1 = "select password_hash, salt from lab8.authentication where username = $1";

      $query1 = sprintf("SELECT user_password from User WHERE user_email='%s'",
                                mysql_real_escape_string($email));


      $results1 = mysql_query($query1);

      $row1 = mysql_fetch_assoc($results1);

      mysql_free_result($results1);

      mysql_close($link);

      //echo $row1['user_password'];
      

                        //$results1 = pg_prepare($conn, "pass", $query1);
                        //$results1 = pg_execute($conn, "pass", array($_POST['username']));

                        //$row1 = pg_fetch_assoc($results1);

      $salt = sha1($email);
      

                        //comparing password in database with users input
                        if(strcmp(sha1($password . $salt), $row1['user_password']) == 0)
                        {

                                //create username session to be sent accross pages
                                //$_SESSION['email'] = $_POST['email'];

                                if(isset($email))
                                {
                                        header("Location: home.php");
                                }

                        }
                        else
                        {
                                echo '<br/>';
                                echo"Invalid username or password";
                                echo '<br/>';
                        }


                }
                else
                {
                        echo '<br/>';
                        echo "Invalid usernam or password";
                        echo '<br/>';
                }

      }
}

	//function to show any messages
	/*function messages() {
   		$message = '';
   		if($_SESSION['success'] != '') {
       		$message = '<span class="success" id="message">'.$_SESSION['success'].'</span>';
       		$_SESSION['success'] = '';
   		}
   		if($_SESSION['error'] != '') {
       		$message = '<span class="error" id="message">'.$_SESSION['error'].'</span>';
       		$_SESSION['error'] = '';
   		}
   		return $message;
	}

	// function to escape data and strip tags
	function safestrip($string){
       	$string = strip_tags($string);
       	$string = mysql_real_escape_string($string);
       	return $string;
	}
}
*/
?>