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


	$sql = "SELECT userName FROM members";
	$result = mysqli_query($connect,$sql);

	//todo: authenticate user from post variables
	$req = $app->request();
	$jsonString = $req->post('jsonString');
	$jsonDecode = json_decode($jsonString);
	$encryptPass = (String)$jsonDecode->{'password'};
	$uname = (String)$jsonDecode->{'username'};

	//compares data to post data from html
	if(mysqli_num_rows($result)>0){
		//select the users and store based on the username match
		while($row = mysqli_fetch_assoc($result)){
			if(($row["userName"]==$uname))
                echo "The username you selected already exists. Please choose a different username.";
                break;
		}
	}else{
		mysqli_query($connect,"INSERT INTO members(userName,password) VALUES ($uname,$encryptPass)");
	}

    ?>