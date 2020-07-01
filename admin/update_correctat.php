<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

$dt = $_POST['ud'];


$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "UPDATE `tbl_fs_categories` SET `correct_at`='$dt' WHERE (`bl_live`='1')";

	$conn->exec($sql);

$conn = null;


$return = date('j M y',strtotime($dt));



die($return);

?>