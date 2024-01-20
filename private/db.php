<?php
$db = mysqli_connect("localhost", "root", "root","facemash");
if ($db == false){
    print("Error: Mysql connent imp... " . mysqli_connect_error());
}


?>
