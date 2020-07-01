<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
require_once "simplexlsx.class.php";



$fileName = $_GET['fn'];

/*ini_set ("display_errors", "1");            error_reporting(E_ALL);*/

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ( $xlsx = SimpleXLSX::parse("dataupload/".$fileName) ) {
	foreach ( $xlsx->rows() as $r => $arr ) {
		
		$t_date = explode('/',$arr[0]);
		strlen($t_date[2]) == 2 ? $tyear = '20'.$t_date[2] : $tyear = $t_date[2];
		
		$transaction_date = $tyear."-".$t_date[1]."-".$t_date[0];
		
		$transaction_date = $arr[0];
		
		$d_ref = $arr[1];     $d_type = $arr[2];     $isin_code = $arr[3];     
		$sedol = $arr[4];     $fund_name = $arr[5];     $curr_code = $arr[6];     $ac_units = $arr[7];     
		$c_type_desc = $arr[8];     $client_code = str_replace('0000','',$arr[9]);     $client_name = $arr[10];     $designation = $arr[11];     
		$p_type = $arr[12];     $shares = $arr[13];     $t_price = $arr[14];     $in_ammount = $arr[15]; 

		if($r !== 0){
			$sql = "INSERT INTO `tbl_fs_transactions` (`fs_transaction_date`, `fs_deal_ref`, `fs_deal_type`, `fs_isin_code`, `fs_fund_sedol`, `fs_fund_name`, `fs_currency_code`, `fs_aui`, `fs_client_desc`, `fs_client_code`, `fs_client_name`, `fs_designation`, `fs_product_type`, `fs_shares`, `fs_t_price`, `fs_iam`, `bl_live`, `fs_file_name`) VALUES ('$transaction_date', '$d_ref', '$d_type', '$isin_code', '$sedol', '$fund_name', '$curr_code', '$ac_units', '$c_type_desc', '$client_code', '$client_name', '$designation', '$p_type', '$shares', '$t_price', '$in_ammount', '2','$fileName')";
			
			$conn->exec($sql);
			
			echo '<p>'.$sql.'</p>';
		}

		echo '<br/>';
	}
} else {
	echo SimpleXLSX::parseError();
}
			
$conn = null; 


?>