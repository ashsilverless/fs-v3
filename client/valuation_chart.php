<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
/*
ini_set ("display_errors", "1");	error_reporting(E_ALL);
    */


$user_id = $_SESSION['fs_client_user_id'];
$client_code = $_SESSION['fs_client_featherstone_cc'];

$last_date = getLastDate('tbl_fs_transactions','fs_transaction_date','fs_transaction_date','fs_isin_code = "GB0009346486"');

$lastlogin = date('g:ia \o\n D jS M y',strtotime(getLastDate('tbl_fsusers','last_logged_in','last_logged_in','id = "'.$_SESSION['fs_client_user_id'].'"')));
$testVar = 'test';
try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8


     //    Get the user data for Client   ///


  $query = "SELECT * FROM tbl_fsusers where id LIKE '$user_id' AND bl_live = 1;";


  $result = $conn->prepare($query);
  $result->execute();

  // Parse returned data
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
	  $user_name = $row['user_name'];

  }

  //    Get the Client Accounts   ///

  $query = "SELECT * FROM `tbl_fs_client_accounts` where fs_client_id = '$user_id' AND ca_linked = '0' AND bl_live = 1 ORDER by ca_order_by DESC;;";

  $result = $conn->prepare($query);
  $result->execute();

  // Parse returned data
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $client_accounts[] = $row;
  }


	foreach ($client_accounts as $ca):

		  $query = "SELECT * FROM `tbl_accounts` where id = ".$ca['ac_account_id']." AND bl_live = 1;";

		  $result = $conn->prepare($query);
		  $result->execute();

		  // Parse returned data
		  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			  $accounts[] = $row;
		  }

	endforeach;


  $conn = null;        // Disconnect

}

catch(PDOException $e) {
  echo $e->getMessage();
}

?>

<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/header.php');
require_once(__ROOT__.'/page-sections/header-elements.php');
require_once(__ROOT__.'/page-sections/sidebar-elements.php');
?>

    <div class="col-md-9
        <?php $_SESSION['fs_admin_name'] != '' ? $class='admin-logged-in':$class= 'client-logged-in';
        echo $class;?>">
        <div class="border-box main-content daily-data">
            <div class="main-content__head">
                <h1 class="heading heading__1">Valuation Chart</h1>
                <p class="mb3">Data accurate as at <?= date('j M y',strtotime($last_date));?></p>
            </div>

                <div class="data-section chart">
                    <div class="chartcontainer row h-100 justify-content-center align-items-center" style="min-height:565px;"><p><i class="fas fa-spinner"></i> Compiling Chart Data</p></div>
                </div>


            
</div>

</div>

</div>

</div>
<?php
require_once(__ROOT__.'/global-scripts.php');
require_once('../page-sections/footer-elements.php');
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/modals/logout.php');
require_once(__ROOT__.'/modals/password.php');
require_once(__ROOT__.'/modals/time-out.php');
require_once(__ROOT__.'/modals/maintenance.php');
require_once(__ROOT__.'/modals/chart.php');
?>

   <script>



	 $(document).ready(function() {

		 <?php if(getField('tbl_fs_maintenance','m_show','id','1')==1){ echo ("$('#maintenance').modal('show');"); }; ?>

		  $(".chartcontainer").load("multi_chart.php");
		 
		 /* ################################################################################## */
		 /* ################################################################################## */
		 /* ################################################################################## */
		 
		 	 $(document).on('click', '.multigraphtime', function(e) {
				e.preventDefault();
				$(".chartcontainer").html('<p><i class="fas fa-spinner"></i> Compiling Chart Data</p>');
				var t = getParameterByName('t',$(this).attr('href'));
				$(".chartcontainer").load("multi_chart.php?&t="+t);
			});
		 /* ################################################################################## */
		 /* ################################################################################## */
		 /* ################################################################################## */



	});

	function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    </script>
  </body>
</html>
