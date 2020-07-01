<?PHP
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

$id = $_GET['id'];

$ac_client_code = $_POST['ac_client_code'];
$ac_product_type = $_POST['ac_product_type'];
$ac_designation = sanSlash($_POST['ac_designation']);
$ac_display_name = sanSlash($_POST['ac_display_name']);

$name = $_SESSION['fs_admin_name'];

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "UPDATE `tbl_accounts` SET `ac_client_code`='$ac_client_code', `ac_designation`='$ac_designation', `ac_product_type`='$ac_product_type', `ac_display_name`='$ac_display_name' WHERE (`id`='$id')";

    $conn->exec($sql);

$conn = null;

header("location:accounts.php");
?>
