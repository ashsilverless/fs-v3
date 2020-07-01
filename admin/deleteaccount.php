<?PHP
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

$ac_id = $_GET['id'];


$name = $_SESSION['fs_admin_name'];

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "UPDATE `tbl_accounts` SET bl_live = 0, created_by = '$name', created_date = '$str_date' WHERE id = '$ac_id' ;";

    $conn->exec($sql);

$conn = null;

header("location:accounts.php");
?>