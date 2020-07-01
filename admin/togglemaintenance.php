<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db


$name = $_SESSION['fs_admin_name'];


$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8
  	$query = "SELECT * FROM `tbl_fs_maintenance` where id = 1 ;";
  	$result = $conn->prepare($query);
  	$result->execute();

	  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		  $m_show = $row['m_show'];
	  }

	$m_show == 0 ? $m_show = 1 : $m_show = 0;

	$sql = "UPDATE `tbl_fs_maintenance` SET m_show = $m_show , confirmed_by = '$name', confirmed_date = '$str_date' WHERE id = '1' ;";
	$conn->exec($sql);

	

  $conn = null;        // Disconnect

$return = date('j M y',strtotime($dt));

$m_show == 0 ? $return = '<span style="color:blue; cursor:pointer">Enable Emergency Maintenance</span>' : $return = '<span style="color:red; cursor:pointer">Disable Emergency Maintenance</span>'; 

die($return);

?>