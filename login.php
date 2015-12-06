
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
