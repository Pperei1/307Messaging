<?php

function getDB() {
$dbhost="localhost";
$dbuser="root";
$dbpass="";
$dbname="fpro";
$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); 
<<<<<<< HEAD

=======
>>>>>>> origin/master
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
return $db;
}

?> 
