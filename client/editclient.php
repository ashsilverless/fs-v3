<?PHP
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

$user_prefix = sanSlash($_POST['user_prefix']);
$first_name = sanSlash($_POST['first_name']);
$last_name = sanSlash($_POST['last_name']);
$email_address = sanSlash($_POST['email_address']);
//$telephone = sanSlash($_POST['telephone']);
$originalpassword = sanSlash($_POST['password']);
$newpassword = sanSlash($_POST['newpassword']);
$confirmpassword = sanSlash($_POST['confirmpassword']);
$hashToStoreInDb = password_hash($confirmpassword, PASSWORD_DEFAULT);

$client_code =$_POST['client_code'];

//    Get the original password
$dbhash = getField('tbl_fsusers','password_hash','id',$client_code);
  

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if($confirmpassword!=""){
		if(password_verify($originalpassword,$dbhash)){
			
			$nxtYear = date('Y-m-d', strtotime('+1 year'));
			$_SESSION['fs_client_newregister'] == 1 ? $destruct_date = ", `destruct_date`='2099-01-01'" : $destruct_date = '';
			
			$msg='updated';
			$sql = "UPDATE `tbl_fsusers` SET `user_prefix`='$user_prefix', `first_name`='$first_name', `last_name`='$last_name', `email_address`='$email_address', `password_hash`='$hashToStoreInDb'  $destruct_date  WHERE (`id`='$client_code')";

			
		}else{
			$msg='badpass';
		}
	}else{
		$sql = "UPDATE `tbl_fsusers` SET `user_prefix`='$user_prefix', `first_name`='$first_name', `last_name`='$last_name', `email_address`='$email_address' WHERE (`id`='$client_code')";
		$msg='updated';
	}

    $conn->exec($sql);

$conn = null;

$_SESSION['fs_client_name'] = $first_name;
$_SESSION['fs_client_username'] = $user_name;

header("location:settings.php?msg=".$msg);
?>
