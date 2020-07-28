<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
/*
ini_set ("display_errors", "1");	error_reporting(E_ALL);
    */


$user_id = $_SESSION['fs_client_user_id'];
$client_code = $_SESSION['fs_client_featherstone_cc'];

$last_date = getLastDate('tbl_fs_transactions','confirmed_date','confirmed_date','bl_live = "1"');

$lastlogin = date('g:ia \o\n D jS M y',strtotime(getLastDate('tbl_fsusers','last_logged_in','last_logged_in','id = "'.$_SESSION['fs_client_user_id'].'"')));

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
require_once('modalchart.php');
?>

   <script>



	 $(document).ready(function() {

		 <?php if(getField('tbl_fs_maintenance','m_show','id','1')==1){ echo ("$('#maintenance').modal('show');"); }; ?>

		  $(".calcs").load("__calcs2.php?ca_lnk=0");
		  $(".linked_calcs").load("__calcs2.php?ca_lnk=1");

		$(document).on('click', '.accountchart', function(e) {
            e.preventDefault();
			$('#chart').modal('show');
			$('.chart-data').html('<p style="color:white;"><i class="fas fa-spinner"></i> Compiling Chart Data</p>');
			var ac_id = getParameterByName('ac_id',$(this).attr('href'));
			$.ajax({
				type : 'get',
				url : 'chart.php',
				data :  'ac_id='+ ac_id, 
				success : function(data){
					$('.chart-data').html(data);
				}
			});
			
        });

		$(document).on('click', '.graphtime', function(e) {
            e.preventDefault();
			$('#chart').modal('show');
			$('.chart-data').html('<p style="color:white;"><i class="fas fa-spinner"></i> Compiling Chart Data</p>');
			var ac_id = getParameterByName('ac_id',$(this).attr('href'));
            var t = getParameterByName('t',$(this).attr('href'));
			$.ajax({
				type : 'get',
				url : 'chart.php',
				data :  'ac_id='+ ac_id +'&t='+t, 
				success : function(data){
					$('.chart-data').html(data);
				}
			});
        });
		 
		 ////////////////////////      Multi Account Chart   //////////////////////
		 
		 $(document).on('click', '.multichart', function(e) {
            e.preventDefault();
			$('#chart').modal('show');
			$('.chart-data').html('<p style="color:white;"><i class="fas fa-spinner"></i> Compiling Chart Data<br><br>Please be patient as this may take up to 20 seconds to compile</p>');
			var ac_id = getParameterByName('ac_id',$(this).attr('href'));
			$.ajax({
				type : 'get',
				url : 'multi_chart.php',
				success : function(data){
					$('.chart-data').html(data);
				}
			});
			
        });

		$(document).on('click', '.multigraphtime', function(e) {
            e.preventDefault();
			$('#chart').modal('show');
			$('.chart-data').html('<p style="color:white;"><i class="fas fa-spinner"></i> Compiling Chart Data<br><br>Please be patient as this may take up to 30 seconds to compile</p>');
            var t = getParameterByName('t',$(this).attr('href'));
			$.ajax({
				type : 'get',
				url : 'multi_chart.php',
				data :  't='+t, 
				success : function(data){
					$('.chart-data').html(data);
				}
			});
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
              $(this).children('.data-toggle-button').text('Close');
        });

        $(document).on('click', '.toggle.active-button', function(e) {
                  $(this).closest( '.data-table__account-wrapper' ).removeClass('active');
                  $(this).children('.data-toggle-button').text('Detail');
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
