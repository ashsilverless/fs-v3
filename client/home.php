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
                <h1 class="heading heading__1">Valuation</h1>
                <p class="mb3">Data accurate as at <?= date('j M y',strtotime($last_date));?></p>
            </div>

            <div class="data-section tables">

                <!--<h2 class="heading heading__2">Accounts for <?=$user_name;?></h2>-->
                <div class="data-table">
                    <div class="data-table__head">
                        <div>
                            <h3 class="heading heading__4">Account Name</h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">Invested</h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">Value</h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">Gain(£)</h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">Gain(%)</h3>
                        </div>
                    </div>

			            <div class="calcs"></div>

                </div>
                <h3 class="heading heading__4 heading__h-line"><span>View Charts</span>
                <div class="chart-close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.82 21.82"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.71,19.39a.71.71,0,0,0-.54-.22H4.91c-1.57,0-2.26-.69-2.26-2.26V14.65a.67.67,0,0,0-.23-.53L.83,12.5a2,2,0,0,1,0-3.19l1.59-1.6a.72.72,0,0,0,.23-.54V4.92c0-1.59.69-2.27,2.26-2.27H7.17a.73.73,0,0,0,.54-.22L9.31.83a1.94,1.94,0,0,1,3.19,0l1.61,1.6a.71.71,0,0,0,.54.22h2.26c1.57,0,2.26.69,2.26,2.27V7.17a.72.72,0,0,0,.23.54L21,9.31a2,2,0,0,1,0,3.19L19.4,14.12a.67.67,0,0,0-.23.53v2.26c0,1.57-.69,2.26-2.26,2.26H14.65a.71.71,0,0,0-.54.22L12.5,21a1.94,1.94,0,0,1-3.18,0Zm4,.76,1.87-1.88a.89.89,0,0,1,.7-.29h2.67c.89,0,1.07-.17,1.07-1.07V14.23a1,1,0,0,1,.28-.69l1.89-1.87c.63-.64.63-.87,0-1.52L18.26,8.28a.94.94,0,0,1-.28-.7V4.92c0-.9-.18-1.08-1.07-1.08H14.24a.89.89,0,0,1-.7-.29L11.67,1.67C11,1,10.79,1,10.15,1.67L8.28,3.55a.89.89,0,0,1-.7.29H4.91C4,3.84,3.84,4,3.84,4.92V7.58a.94.94,0,0,1-.28.7L1.67,10.15c-.63.65-.63.88,0,1.52l1.89,1.87a1,1,0,0,1,.28.69v2.68c0,.9.17,1.07,1.07,1.07H7.58a.89.89,0,0,1,.7.29l1.87,1.88C10.79,20.79,11,20.79,11.67,20.15ZM6.89,14.38a.55.55,0,0,1,.18-.44l3-3-3-3a.54.54,0,0,1-.18-.44A.6.6,0,0,1,7.5,7a.54.54,0,0,1,.43.19l3,3,3-3A.57.57,0,0,1,14.32,7a.6.6,0,0,1,.61.6.58.58,0,0,1-.18.43l-3,3,3,3a.64.64,0,0,1,.19.45.61.61,0,0,1-.61.61.58.58,0,0,1-.45-.2l-3-3L8,14.79a.57.57,0,0,1-.45.2A.61.61,0,0,1,6.89,14.38Z"/></g></g></svg>Close Charts</div>
                </h3>
                <div class="data-section chart">
                	<!--<?php foreach ($accounts as $account): ?>
                        <a href="#?ac_id=<?=$account['id'];?>" class="accountchart">
                            <div class="button button__raised button__inline chart-select">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29.96 25.73"><defs><style>.cls-1{fill:#97ceb5;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M29.31,25.73H0V.65a.65.65,0,0,1,1.3,0V24.43h28a.65.65,0,0,1,0,1.3ZM10.3,22V14.69A.65.65,0,0,0,9.65,14H4.27a.65.65,0,0,0-.65.65V22a.65.65,0,0,0,.65.65H9.65A.65.65,0,0,0,10.3,22ZM4.92,15.34H9v6H4.92ZM18.69,22V2.46A.65.65,0,0,0,18,1.81H12.65a.65.65,0,0,0-.65.65V22a.65.65,0,0,0,.65.65H18A.65.65,0,0,0,18.69,22ZM13.3,3.11h4.09V21.35H13.3ZM27.07,22V10.65a.65.65,0,0,0-.65-.65H21a.65.65,0,0,0-.65.65V22a.65.65,0,0,0,.65.65h5.38A.65.65,0,0,0,27.07,22ZM21.69,11.3h4.08V21.35H21.69Z"/></g></g></svg>
                            <?=$account['ac_display_name'];?></div></a>
                	<?php endforeach; ?>-->
                    <div class="chartcontainer"></div>
                </div>
            </div>

            <!--<div class="data-section tables">
                <h2 class="heading heading__2">Accounts Linked To <?=$user_name;?></h2>
                <div class="data-table">
                    <div class="data-table__head">
                        <div>
                            <h3 class="heading heading__4">Account Name</h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">Invested</h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">Value</h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">Gain(£)</h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">Gain(%)</h3>
                        </div>
                    </div>

			<div class="linked_calcs"></div>

        	</div>
    </div>-->

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
?>

   <script>



	 $(document).ready(function() {

		 <?php if(getField('tbl_fs_maintenance','m_show','id','1')==1){ echo ("$('#maintenance').modal('show');"); }; ?>

		  $(".calcs").load("__calcs2.php?ca_lnk=0");
		  $(".linked_calcs").load("__calcs2.php?ca_lnk=1");

		$(document).on('click', '.accountchart', function(e) {
            e.preventDefault();
			$(".chartcontainer").html('<p><i class="fas fa-spinner"></i> Compiling Chart Data</p>');
            var ac_id = getParameterByName('ac_id',$(this).attr('href'));
            $(".chartcontainer").load("chart.php?ac_id="+ac_id);
        });

		$(document).on('click', '.graphtime', function(e) {
            e.preventDefault();
			$(".chartcontainer").html('<p><i class="fas fa-spinner"></i> Compiling Chart Data</p>');
			var ac_id = getParameterByName('ac_id',$(this).attr('href'));
            var t = getParameterByName('t',$(this).attr('href'));
            $(".chartcontainer").load("chart.php?ac_id="+ac_id+"&t="+t);
        });


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


    $(document).on('click', '.toggle', function(e) {
              $(this).closest( '.data-table__account-wrapper' ).addClass('active');
              $(this).addClass('active-button');
              $(this).children('.data-toggle-button').text('Close Detailed Breakdown');
        });

        $(document).on('click', '.toggle.active-button', function(e) {
                  $(this).closest( '.data-table__account-wrapper' ).removeClass('active');
                  $(this).children('.data-toggle-button').text('View Detailed Breakdown');
                  $(this).removeClass('active-button');
            });

        $(document).on('click', '.chart-close', function(e) {
            if ($('.chartcontainer').hasClass('close')) {
                $('.chartcontainer').removeClass('close');
            } else {
                $('.chartcontainer').addClass('close');
            }
        });
        $(document).on('click', '.chart-select', function(e) {
            $(this).removeClass('active');
            $('.chartcontainer').removeClass('close');
        });
    </script>
  </body>
</html>
