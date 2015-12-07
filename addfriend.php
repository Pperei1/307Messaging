<?php
        $con=mysqli_connect("localhost","root","","users");
        if(mysqli_connect_errno())
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        $user=$_POST["user"];
        $friend=$_POST["friend"];
        $sql="INSERT INTO users($user,$friend) VALUES ($friend,$user)";
        mysqli_query($con,$sql);
?>