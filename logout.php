<?php
require 'C://xampp/vendor/autoload.php';
$app = new \Slim\Slim();


$app->post('/', function() use ($app){
	
	//connection to mysql
	$servername = "localhost";
	$username = "root";
	$password = "";
	$db="q2db";

	$connect = new mysqli($servername, $username, $password, $db);

	if(!$connect){
		die("connection failed: " . mysqli_connect_error());
	}


	$sql = "SELECT memberID,sessionID FROM session";
	$result = mysqli_query($connect,$sql);

	//todo: authenticate user from post variables
	$req = $app->request();
	$loggedSessID = $req->post('sessionID');
	$memID = 0;
	//compares data to post data from html
	if(mysqli_num_rows($result)>0){
		//select the users and store based on the username match
		while($row = mysqli_fetch_assoc($result)){
			if(($row["sessionID"]==$loggedSessID)){
				$memID = $row["memberID"];
				echo "login confirmed<br/>";
				echo "deleting<br/>";

				$sql="DELETE FROM session WHERe memberID = $memID";
				if(mysqli_query($connect,$sql)){
					echo "Deleted<br/>";
				}
				else{
					echo "delete failed!<br/>";
				}

				//find username based on the member ID
				$sql = "SELECT memberID,sharedKey,userName,password FROM members";
				$result = mysqli_query($connect,$sql);
				if(mysqli_num_rows($result)>0){
				//select the users and store based on the username match
					while($row = mysqli_fetch_assoc($result)){
						if(($row["memberID"]==$memID)){
							$uname = $row["userName"];
				//prints username
							echo "$uname logged out!";

				//prints login 
							echo "
							<!DOCTYPE html>
							<html>
							<head>
							<link rel=stylesheet type=text/css href=style.css>
							<body text=#FFFFFF bgcolor=#800000>
							<header> Logout successful!
							</header>
							<a href=Login.html>Back to Login Page</a>

							</body>
							</html>";
							break;
						}
					}
				}else{
					echo "0 results<br/>";
				}


				break;
			}
		}
	}else{
		echo "No one Logged in!!<br/>";
	}

	if($memID==0){
		echo "not a valid logout request!!";
		echo "
				<!DOCTYPE html>
				<html>
				<head>
				<link rel=stylesheet type=text/css href=style.css>
				<body text=#FFFFFF bgcolor=#800000>
				<header> Nothing to logout!!!
				</header>
				<a href=Login.html>Back to Login Page</a>

				</body>
				</html>";
	}
	
});

$app->run();
?>