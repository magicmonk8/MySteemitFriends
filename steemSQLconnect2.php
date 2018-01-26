<?php

$servername = "vip.steemsql.com:1433";

$username = "your_user_name";

$password = "your_password";



try {
	
    $conn = new PDO("dblib:host=$servername;dbname=DBSteem", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
}
    
catch(PDOException $e) {

	echo "Connection failed: " . $e->getMessage();
}


?>

