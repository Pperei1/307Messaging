<<<<<<< HEAD

<?php
	require 'dbconnect.php';
	require 'vendor/autoload.php';

	$app = new \Slim\Slim();
	$app->post('/login','login');
	
	$app->post('/logout',function(){
		$json = \Slim\Slim::getInstance()->request()->getBody();
		$decode = json_decode($json);
		$sql = "SELECT memberID from session WHERE sessionID = :sID";
		try{
			$db = getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("sID",$decode->{'sessionID'});
			$stmt->execute();
			if($stmt->rowCount()==1){
				$result = $stmt->fetch(PDO::FETCH_BOTH);
				$memberID = $result[0];
				$sql = "DELETE FROM session WHERE sessionID = :sID";
				$stmt = $db->prepare($sql);
				$stmt->bindParam("sID",$decode->{'sessionID'});
				$stmt->execute();
				$sql = "SELECT username FROM users WHERE memberID = :mID";
				$stmt = $db->prepare($sql);
				$stmt->bindParam("mID",$memberID);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_BOTH);
				$username = $result[0];
				$returnstr = "<a href='login.html'>Login Page</a>
				<p class ='text-center' >Logged Out. Goodbye username</p>";
				$returnstr = str_replace("username", $username, $returnstr);
				echo $returnstr;
			}
			else{
				echo "<p class ='text-center'>invalid user</p>";
			}
		}
		catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
	);
	
	$app->post('/shared',function(){
		$json = \Slim\Slim::getInstance()->request()->getBody();
		$decode = json_decode($json);
		$sql = "SELECT sharedkey from users WHERE username = :user";
		try{
			$db = getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("user",$decode->{'username'});
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_BOTH);
			$username = $result[0];
			echo $username;
		}
		catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	});
	$app->run();
	
	function login(){
		$json = \Slim\Slim::getInstance()->request()->getBody();
		$decode = json_decode($json);
		$sql = "SELECT memberID,password,sharedKey FROM users WHERE username=:username";
		try{
			$db = getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("username",$decode->{'username'});
			$stmt->execute();
			$db = null;
		}
		catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
		if($stmt->rowCount()== 1){
			$result = $stmt->fetch(PDO::FETCH_BOTH);
			$pass = $result[1];
			$key = $result[2];
			$npass = encode($pass,$key);
			if(strcmp($npass,$decode->{'password'}) == 0){
				$sql = "INSERT INTO session (memberID) VALUES (:mID)";
				try{
					$db = getDB();
					$stmt = $db->prepare($sql);
					$stmt->bindParam("mID",$result[0]);
					$stmt->execute();
					$sql = "SELECT sessionID FROM session WHERE memberID =:mID";
					$stmt = $db->prepare($sql);
					$stmt->bindParam("mID",$result[0]);
					$stmt->execute();
					$db=null;
				}
				catch(PDOException $e) {
					echo '{"error":{"text":'. $e->getMessage() .'}}';
				}
				$result = $stmt->fetch(PDO::FETCH_BOTH);
				$sessionID = $result[0];
				$return = "<p class ='text-center'>Successfully logged</p>
					<form id='logout'>
						<input type = 'hidden' value = 'sessionID' ><br>
					</form>
					<button class = 'btn btn-primary' onclick='logout()'>logout</button>";
				$return = str_replace("sessionID",$sessionID,$return);
				echo $return;
			}
			else{
				$return = "
					<a href='login.html'>Login Page</a>
					<p class ='text-center'>invalid username or password</p>
					<p>0</p>";
				echo $return;
			}
		}
		else{
			$return = "
				<a href='login.html'>Login Page</a>
				<p class ='text-center'>invalid username or password</p>
				<p>1</p>";
			echo $return;
		}
	}
	
	function encode($pass,$key){
		for ($i=0;$i<strlen($pass);$i++) {
				$c = ord($pass[$i]);
					if($c > 64 && $c < 91) { 
						$c = (($c-65+$key)%26)+65; 
					} 
					else if($c > 96){ 
						$c = (($c-97+$key)%26)+97; 
					} 
				$pass[$i] = chr($c);
			}
		return $pass;
	}
?>
=======
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


	$sql = "SELECT memberID,sharedKey,userName,password FROM members";
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
			if(($row["userName"]==$uname)){
				$key = $row["sharedKey"];
				$compPass = $row["password"];
				$memID = $row["memberID"];
				$sessID = $memID+1000;
				break;
			}
		}
	}else{
		echo "0 results<br/>";
	}

	//decrypts the encrypted password
	//TODO: when less than a return back to z
	for($i=0;$i<strlen($encryptPass);$i++){
		$c = ord($encryptPass[$i]);
		$n = intval($encryptPass[$i]);

		//uppercase
		if($c>=65&&$c<=90){
				$dPass[$i] = chr(($c-60-$key)%26+60);
		}  
		//Lowercase
		else if($c>=97&&$c<=122){
				$dPass[$i] = chr(($c-97-$key)%26+97);
		} 
		//numbers
		else if($n>=0&&$n<=9){
			if($n-$key<0){
				$dPass[$i] = (String)($n-$key+10);
			}
			else{
				$dPass[$i] = (String)($n-$key);
			}
		}

		//neither
		else $dPass[$i] = $encryptPass[$i];  // Copy
	}
	$decryptPass = implode("",$dPass);

	
	if(($compPass==$decryptPass)){
		//send a reply confirming logged in
		//with a webpage with a logout button
		//and a hidden field where it states session ID
		//append to session ID user ID and session ID (that I randomly make up)
		$sql = "INSERT INTO session (memberID,sessionID) VALUES ($memID,$sessID)";
		if(mysqli_query($connect,$sql)){
			echo "$uname logged in successfully!!!<br/>";
		}
		else{
			echo "ERROR<br/>";
		}

		echo "
		<!DOCTYPE html>
		<html>
		<head>
		<link rel=stylesheet type=text/css href=style.css>
		<body text=#FFFFFF bgcolor=#800000>
		<header> Login Successful!
		</header>

		<form id=frm1 method=post action=logout.php>
		<input type=hidden name =sessionID value=$sessID></p>
		<button id = logout >Logout</button>
		</form>

		</body>
		</html>";
	}
	else{
		//sends a webpage with a hyperlink to the login page
		//state also that login was unsuccessful
		echo "
		<!DOCTYPE html>
		<html>
		<head>
		<link rel=stylesheet type=text/css href=style.css>
		<body text=#FFFFFF bgcolor=#800000>
		<header> Login Failed!
		</header>
			<a href=Login.html>Login Page</a>

		</body>
		</html>";
	}



});

$app->run();
?>
>>>>>>> origin/master
