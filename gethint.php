<?php
//TODO make the names link to an "add friend" script
//Author: Taha Ghassemi (with help from W3Schools)
 // get the q parameter from URL
$q = $_REQUEST["q"];

$hint = "";
        $con=mysqli_connect("localhost","root","","users");
        if(mysqli_connect_errno())
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            //the database has a "users" table with a "username" column
        $sql="SELECT * FROM `users`";
        $result=$con->query($sql);

        if($result->num_rows>0){
            echo "Select the user below whom you would like to add as a contact. <br>";
            $q = strtolower($q);
    $len=strlen($q);
    while($row=$result->fetch_assoc()){
        $name=$row["USERNAME"];
        if (stristr($q, substr((String) $name, 0, $len))) {
            if ($hint === "") {
                $hint = $name;
            } else {
                $hint .= ", $name";
            }
         }
    }
// Output "no suggestion" if no hint was found or output correct values 
echo $hint === "" ? "no suggestion" : $hint;
?> 