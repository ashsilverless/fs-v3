<?php include 'inc/dbcon.php';     # $host  -  $user  -  $pass  -  $db

ini_set ("display_errors", "1");	error_reporting(E_ALL);


cleanArray($_POST);
$email	= sanSlash($_POST['username']);


$conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
$conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

$result = $conn->prepare("select * from tbl_fsusers where email_address like '".$email."' ;"); 
$result->execute();
$number_of_rows = $result->rowCount(); 

if ($number_of_rows == 1) {
	$newpass = randomPassword();
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "update tbl_fsusers set password='$newpass' where email_address LIKE '".$email."';";
    $conn->exec($sql);
	$strloc = "forgotten_confirmation.php";
	$email = doEmail($email,$newpass);
}else{
	$strloc = "forgotten_password.php?e=t";
}

header('Location:'.$strloc);
?>