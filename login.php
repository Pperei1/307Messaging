
<?php
	require 'dbconnect.php';
	require '/vendor/autoload.php';

	$app = new \Slim\Slim();
	$app->post('/login','login');
	
	$app->post('/sendMessage', function(){
		$json = \Slim\Slim::getInstance()->request()->getBody();
		$decode = json_decode($json);
		$sql = "INSERT INTO messages(senderUser,receiUser,message) VALUES (:sender,:receiver,:message)";
		try{
			$db = getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("sender",$decode->{'user'});
			$stmt->bindParam("receiver",$decode->{'friend'});
			$stmt->bindParam("message",$decode->{'message'});
			$stmt->execute();	
			echo "hi";
		}
		catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
	);
	
	$app->post('/updateChat', function(){
		$json = \Slim\Slim::getInstance()->request()->getBody();
		$decode = json_decode($json);
		$sql = "SELECT * FROM (SELECT * FROM `messages` WHERE senderUser = :user AND receiUser = :friend UNION SELECT * FROM `messages` WHERE senderUser = :friend AND receiUser = :user) As `mmm` ORDER BY c";
		try{
			$db = getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("user",$decode->{'user'});
			$stmt->bindParam("friend",$decode->{'friend'});
			$stmt->execute();	
			$counter = 1;
			$messages = '[';
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				if($counter==($stmt->rowCount())){
					$messages .= '{"username":"'. $row['senderUser'] .'","message":"'. $row['message'].'"}';
				}
				else{
					$messages .= '{"username":"'. $row['senderUser'] .'","message":"'. $row['message'].'"},';
				}
				$counter++;
			}
			$messages .= ']';	
			echo $messages;
		}
		catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
	);
	
	$app->post('/loadFriend', function(){
		$json = \Slim\Slim::getInstance()->request()->getBody();
		$decode = json_decode($json);
		$sql = "SELECT friend FROM Friends WHERE user = :username";
		try{
			$db = getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("username",$decode->{'user'});
			$stmt->execute();
			$counter = 1;
			$friends = '[';
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				if($counter==($stmt->rowCount())){
					$friends .= '{"username":"'. $row['friend'] .'"}';
				}
				else{
					$friends .= '{"username":"'. $row['friend'] .'"},';
				}
				$counter++;
			}
			$friends .= ']';	
			echo $friends;
		}
		catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
	);

	$app->post('/signup', function(){
		$json = \Slim\Slim::getInstance()->request()->getBody();
		$decode = json_decode($json);
		$pass = (String)$decode->{'password'};
		$use = (String)$decode->{'username'};
		$key = (int)$decode->{'key'};
		$memID = rand(10,1000)+$key;

		//get current username
		$sql = "SELECT username FROM users WHERE username = :username";
		try{
			$db = getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("username",$decode->{'username'});
			$stmt->execute();

			if($stmt->rowCount()>0){
			//select the users and store based on the username match
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					if($row["userName"]==$use){
						echo $key = -1;
					break;
					}
				}
			}else{
				$sql = "INSERT INTO users(memberID,username,password,sharedKey) VALUES (:memID,:use,:pass,:key)";
				$stmt = $db->prepare($sql);
				$stmt->bindParam("memID",$memID);
				$stmt->bindParam("use",$use);
				$stmt->bindParam("pass",$pass);
				$stmt->bindParam("key",$key);
				$stmt->execute();
				echo $key;
			}
		}
		catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
	);

	$app->post('/add',function(){
		$json = \Slim\Slim::getInstance()->request()->getBody();
		$decode = json_decode($json);
		$sql = "SELECT username from users WHERE username = :user";
		try{
			$db = getDB();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("user",$decode->{'newFriend'});
			$stmt->execute();
			if($stmt->rowCount()==1){
				$sql = "SELECT friend from FRIENDS WHERE user = :user AND friend = :friend";
				$db = getDB();
				$stmt = $db->prepare($sql);
				$stmt->bindParam("user",$decode->{'adder'});
				$stmt->bindParam("friend",$decode->{'newFriend'});
				$stmt->execute();
				if($stmt->rowCount()==1){
					echo '{"success":"False","msg":"That user is already a friend"}';
				}
				else{
					echo '{"success":"True","msg":"Friend added"}';
					$sql = "INSERT INTO FRIENDS VALUES(:adder,:newFriend)";
					$db = getDB();
					$stmt = $db->prepare($sql);
					$stmt->bindParam("adder",$decode->{'adder'});
					$stmt->bindParam("newFriend",$decode->{'newFriend'});
					$stmt->execute();
					$sql = "INSERT INTO FRIENDS VALUES(:adder,:newFriend)";
					$db = getDB();
					$stmt = $db->prepare($sql);
					$stmt->bindParam("adder",$decode->{'newFriend'});
					$stmt->bindParam("newFriend",$decode->{'adder'});
					$stmt->execute();
				}
			}
			else{
				echo '{"success":"False","msg":"Invalid User Name"}';
			}
		}
		catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
	}
	);

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
				<p class ='text-center' >Logged Out. Goodbye $username</p>";
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
				$uname = $decode->{'username'};
				$result = $stmt->fetch(PDO::FETCH_BOTH);
				$arr = array('username' => $uname, 'memberID' => $result[0]);
				$return = json_encode($arr);
				echo $return;
			}
			else{
				$return = '
					<a href="login.html"Login Page</a>
					<p class ="text-center">invalid username or password</p>
					<p>0</p>';
				echo $return;
			}
		}
		else{
			$return = '
				<a href="login.html"Login Page</a>
				<p class ="text-center">invalid username or password</p>
				<p>1</p>';
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
