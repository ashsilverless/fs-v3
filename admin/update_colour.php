<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

$colid = $_POST['colid'];
$colour = $_POST['colour'];


$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "UPDATE `tbl_fs_categories` SET `cat_colour`='$colour' WHERE (`id`='$colid')";

	$conn->exec($sql);

$conn = null;


$return = date('j M y',strtotime($dt));



die($return);

?>