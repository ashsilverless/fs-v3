<?php
error_reporting(0);
@session_start();
set_time_limit(0);
function db_connect() {
    static $connection;

	##################      LIVE SERVER     ###########################

	$host = "46.32.229.204";
	$username = "FeatherStoneDashboard";
	$password = "FSD>Login-1";
	$dbname	 = "featherstone_db";
	$charset = 'utf8mb4';

	##################     / LIVE SERVER     ##########################


    if(!isset($connection)) {
        $connection = mysqli_connect($host,$username,$password,$dbname);
    }
    if($connection === false) {
        return mysqli_connect_error();
    }
    return $connection;
}

function getMShow(){

	$host = "46.32.229.204";
	$user = "FeatherStoneDashboard";
	$pass = "FSD>Login-1";
	$db = "featherstone_db";
	$charset = 'utf8mb4';

	try {
	  // Connect and create the PDO object
	  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
	  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

	  $result = $conn->prepare("SELECT * FROM tbl_fs_maintenance WHERE id = 1");
	  $result->execute();

	  // Parse returned data
	  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		  $return = $row['m_show'];
	  }

	  $conn = null;        // Disconnect
	  return $return;

	}
	catch(PDOException $e) {
	  echo $e->getMessage();
	}
}

function db_query($query) {
    $connection = db_connect();
    $result = mysqli_query($connection,$query);
    return $result;
}

function db_error() {
    $connection = db_connect();
    return mysqli_error($connection);
}

$connect = db_connect();

if (!isset($_SESSION['fs_client_token'])) {
    $token = md5(uniqid(rand(), TRUE));
    $_SESSION['fs_client_token'] = $token;
    $_SESSION['fs_client_token_time'] = time();
}else{
    $token = $_SESSION['fs_client_token'];
}

define('ADMINEMAIL', 'tim@silverless.co.uk');

?>
