<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

$client_id = $_GET['id'];
 
$v_pages = array('home.php','assets.php','valuation_chart.php','print.php','settings.php');     #   'current_investment.php','peer_groups.php'


//    Get the user details
try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8


	$query = "SELECT *  FROM `tbl_fsusers` where id = $client_id;";

    $result = $conn->prepare($query);
    $result->execute();

	while($row = $result->fetch(PDO::FETCH_ASSOC)) {

		$fs_client_code = $row['fs_client_code'];
		$user_prefix = $row['user_prefix'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$user_name = $row['user_name'];
		$destruct_date = $row['destruct_date'];
		$email_address = $row['email_address'];
		$telephone = $row['telephone'];
		$strategy = $row['strategy'];
		$desc = $row['fs_client_desc'];
		$googlecode = $row['googlecode'];
        $confirmed_by = $row['confirmed_by'];
        $confirmed_date = $row['confirmed_date']= date('d M Y');
	}
	
	// ########################    Create Website Hit Data   ###################
			$timestamp = time();
			$date_time_array = getdate($timestamp);

			$day=$date_time_array['mday'];
			if($day<10){ $day="0".$day; };
			$month=$date_time_array['mon'];
			if($month<10){ $month="0".$month; };
			$date_inc = 30;

			while($date_inc> -1){
			
				$timestamp1 = mktime(0,0,0,$month,$day-$date_inc,$year);
				$date_time_array1 = getdate($timestamp1);
			
				$newday=$date_time_array1['mday'];
				if($newday<10){ $newday="0".$newday; };
				$newmonth=$date_time_array1['mon'];
				if($newmonth<10){ $newmonth="0".$newmonth; };
			
				$the_date = $date_time_array['year']."-".$newmonth."-".$newday;
				$showDate = $newday."/".$newmonth;
			
				$query = "SELECT count(*) FROM `tbl_fshits` where int_user_id = $client_id AND dt_date LIKE '".$the_date."%';";

				$result = $conn->prepare($query);
				$result->execute();
				$num_rows = $result->fetchColumn();
				
				$thelabeldate = date("d-m-Y", strtotime($the_date));
				
				$labels .= "'".$thelabeldate."',";
				$data_count .= $num_rows.",";

				$date_inc -= 1;
				
			}

			$data_count = substr($data_count, 0, -1);
			$labels = substr($labels, 0, -1);
	
	
	##############   Now do the pie chart data    #################
	foreach($v_pages as $page):
		
		$query = "SELECT count(*) FROM `tbl_fshits` where int_user_id = $client_id AND str_page LIKE '%".$page."%';";

		$result = $conn->prepare($query);
		$result->execute();
		$num_rows = $result->fetchColumn();
	
		$pie_count .= $num_rows.",";
		$pie_labels .= "'".$page."',";
	
	endforeach;
	
	$pie_count = substr($pie_count, 0, -1);
	$pie_labels = substr($pie_labels, 0, -1);
	
	
  $conn = null;        // Disconnect

}

catch(PDOException $e) {
  echo $e->getMessage();
}

?>
<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/header.php');
require_once('page-sections/header-elements.php');
?>

<div class="container">
    <div class="border-box main-content">
		<h1 class="heading heading__2">Client Details</h1>
		<p><strong>Name</strong> : <?= $first_name;?>&nbsp;<?= $last_name;?>&emsp;&emsp;&emsp;<strong>Email</strong> : <?=$email_address;?></p>
		<form class="asset-form">

            <div class="content client">

				<div class="row">
					<div class="col-md-12">
						<h3 class="heading heading__2">Activity</h3>
						<canvas class="my-4 w-100 chartjs-render-monitor" id="barchart" height="400"></canvas>
					</div>
					
					<div class="col-md-12">
						<h3 class="heading heading__2">Page Visits</h3>
						<canvas class="my-4 w-100 chartjs-render-monitor" id="piechart" height="400"></canvas>
					</div>
				</div>

            </div><!--content-->

           

            <div class="control">
                <h3 class="heading heading__2">Account Actions</h3>
                <p class="mb1">Last edited by <?= $confirmed_by; ?> on <?= $confirmed_date; ?></p>
                <a href="#modalCLIENT" data-toggle="modal" class="button button__raised button__inline mb1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.59 19.59"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M0,9.79A9.84,9.84,0,0,1,9.79,0a9.85,9.85,0,0,1,9.8,9.79,9.85,9.85,0,0,1-9.8,9.8A9.85,9.85,0,0,1,0,9.79Zm16.11,5.76a8.53,8.53,0,1,0-12.63,0c.9-1.24,3.24-2.47,6.31-2.47S15.21,14.3,16.11,15.55ZM6.46,7.71a3.48,3.48,0,0,1,3.33-3.6,3.48,3.48,0,0,1,3.33,3.6A3.46,3.46,0,0,1,9.79,11.4,3.48,3.48,0,0,1,6.46,7.71Z"/></g></g></svg>Client Side View</a>
                <button type="submit" class="button button__raised mb1">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 21.8 21.8" style="enable-background:new 0 0 21.8 21.8;" xml:space="preserve">
                        <style type="text/css">
                            .st0{fill:#96E8C4;}
                        </style>
                        <g id="Layer_2_1_">
                            <g id="Layer_1-2">
                                <path class="st0" d="M7.7,19.4c-0.1-0.1-0.3-0.2-0.5-0.2H4.9c-1.6,0-2.3-0.7-2.3-2.3v-2.3c0-0.2-0.1-0.4-0.2-0.5l-1.6-1.6
                                    c-0.9-0.7-1.1-1.9-0.4-2.8c0.1-0.1,0.2-0.3,0.4-0.4l1.6-1.6c0.1-0.1,0.2-0.3,0.2-0.5V4.9c0-1.6,0.7-2.3,2.3-2.3h2.3
                                    c0.2,0,0.4-0.1,0.5-0.2l1.6-1.6c0.6-0.9,1.8-1.1,2.7-0.5c0.2,0.1,0.4,0.3,0.5,0.5l1.6,1.6c0.1,0.1,0.3,0.2,0.5,0.2h2.3
                                    c1.6,0,2.3,0.7,2.3,2.3v2.2c0,0.2,0.1,0.4,0.2,0.5L21,9.3c0.9,0.7,1.1,1.9,0.4,2.8c-0.1,0.1-0.2,0.3-0.4,0.4l-1.6,1.6
                                    c-0.2,0.1-0.2,0.3-0.2,0.5v2.3c0,1.6-0.7,2.3-2.3,2.3h-2.3c-0.2,0-0.4,0.1-0.5,0.2L12.5,21c-0.6,0.9-1.8,1.1-2.7,0.5
                                    c-0.2-0.1-0.3-0.3-0.5-0.5L7.7,19.4z M11.7,20.1l1.9-1.9c0.2-0.2,0.4-0.3,0.7-0.3H17c0.9,0,1.1-0.2,1.1-1.1v-2.7
                                    c0-0.3,0.1-0.5,0.3-0.7l1.9-1.9c0.6-0.6,0.6-0.9,0-1.5l-1.9-1.9C18.1,8.1,18,7.8,18,7.6V4.9c0-0.9-0.2-1.1-1.1-1.1h-2.7
                                    c-0.3,0-0.5-0.1-0.7-0.3l-1.9-1.9C11,1,10.8,1,10.1,1.7L8.3,3.5C8.1,3.7,7.8,3.9,7.6,3.8H4.9C4,3.8,3.8,4,3.8,4.9v2.7
                                    c0,0.3-0.1,0.5-0.3,0.7l-1.9,1.9C1,10.8,1,11,1.7,11.7l1.9,1.9c0.2,0.2,0.3,0.4,0.3,0.7v2.7C3.8,17.8,4,18,4.9,18h2.7
                                    c0.3,0,0.5,0.1,0.7,0.3l1.9,1.9C10.8,20.8,11,20.8,11.7,20.1L11.7,20.1z M8.9,15.4l-3.2-3.6c-0.1-0.1-0.2-0.3-0.2-0.4
                                    c0-0.4,0.3-0.6,0.7-0.6c0.2,0,0.3,0.1,0.4,0.2l2.7,3l5.1-7.2c0.2-0.3,0.6-0.4,0.9-0.2c0.2,0.1,0.3,0.3,0.3,0.5
                                    c0,0.1-0.1,0.3-0.1,0.4l-5.6,7.9c-0.1,0.2-0.3,0.2-0.5,0.2C9.2,15.5,9,15.5,8.9,15.4L8.9,15.4z"/>
                            </g>
                        </g>
                    </svg>
                    Save Changes
              </button>
                <a href="" class="button button__raised button__inline button__danger"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.82 21.82"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.71,19.39a.71.71,0,0,0-.54-.22H4.91c-1.57,0-2.26-.69-2.26-2.26V14.65a.67.67,0,0,0-.23-.53L.83,12.5a2,2,0,0,1,0-3.19l1.59-1.6a.72.72,0,0,0,.23-.54V4.92c0-1.59.69-2.27,2.26-2.27H7.17a.73.73,0,0,0,.54-.22L9.31.83a1.94,1.94,0,0,1,3.19,0l1.61,1.6a.71.71,0,0,0,.54.22h2.26c1.57,0,2.26.69,2.26,2.27V7.17a.72.72,0,0,0,.23.54L21,9.31a2,2,0,0,1,0,3.19L19.4,14.12a.67.67,0,0,0-.23.53v2.26c0,1.57-.69,2.26-2.26,2.26H14.65a.71.71,0,0,0-.54.22L12.5,21a1.94,1.94,0,0,1-3.18,0Zm4,.76,1.87-1.88a.89.89,0,0,1,.7-.29h2.67c.89,0,1.07-.17,1.07-1.07V14.23a1,1,0,0,1,.28-.69l1.89-1.87c.63-.64.63-.87,0-1.52L18.26,8.28a.94.94,0,0,1-.28-.7V4.92c0-.9-.18-1.08-1.07-1.08H14.24a.89.89,0,0,1-.7-.29L11.67,1.67C11,1,10.79,1,10.15,1.67L8.28,3.55a.89.89,0,0,1-.7.29H4.91C4,3.84,3.84,4,3.84,4.92V7.58a.94.94,0,0,1-.28.7L1.67,10.15c-.63.65-.63.88,0,1.52l1.89,1.87a1,1,0,0,1,.28.69v2.68c0,.9.17,1.07,1.07,1.07H7.58a.89.89,0,0,1,.7.29l1.87,1.88C10.79,20.79,11,20.79,11.67,20.15ZM6.89,14.38a.55.55,0,0,1,.18-.44l3-3-3-3a.54.54,0,0,1-.18-.44A.6.6,0,0,1,7.5,7a.54.54,0,0,1,.43.19l3,3,3-3A.57.57,0,0,1,14.32,7a.6.6,0,0,1,.61.6.58.58,0,0,1-.18.43l-3,3,3,3a.64.64,0,0,1,.19.45.61.61,0,0,1-.61.61.58.58,0,0,1-.45-.2l-3-3L8,14.79a.57.57,0,0,1-.45.2A.61.61,0,0,1,6.89,14.38Z"/></g></g></svg>Cancel</a>
            </div>

        </form>

    </div>
</div>

<?php
require_once('page-sections/footer-elements.php');
require_once('modals/delete.php');
require_once('modals/logout.php');
require_once('modals/login_as.php');
require_once('modals/delete-cat.php');
require_once(__ROOT__.'/global-scripts.php');?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script>
      feather.replace()
    </script>

    <script>

    $( document ).ready(function() {
		
		Chart.defaults.global.legend.display = false;

		var ctxbar = document.getElementById('barchart');
		var BarChart = new Chart(ctxbar, {
			type: 'bar',
			data: {
				datasets: [
					{
						fill:'origin',
						lineTension:0.2,
						borderColor:'rgba(150, 232, 196, 1)',
						backgroundColor:'rgba(150, 232, 196, 0.1)',
						borderWidth:2,
						color: 'rgba(150, 232, 196, 0.95)',
						label:'Total',
						data:[<?=$data_count;?>]
					}
					],
				labels: [<?=$labels;?>]
			},

			options: {
				scales: {
					yAxes: [{
					  ticks: {
						beginAtZero: true,
					  }
					}]
				},
				tooltips: {
					enabled: true,
					displayColors: false,
				},
				legend: {
					display: false,
					labels: {
						fontColor: 'rgb(255, 255, 255)'
					}
				},
				elements: {
                    point:{
                        radius: 0
                    }
                }
			}
		});
		
		/////////////////////////////////////////
		
		var ctxpie = document.getElementById('piechart');
		var PieChart = new Chart(ctxpie, {
			type: 'pie',
			data: {
				datasets: [{
					data: [<?=$pie_count;?>],
					backgroundColor: ['rgba(150, 232, 196, 0.75)','rgba(150, 196, 232, 0.75)','rgba(196, 232, 150, 0.75)','rgba(232, 150, 196, 0.75)','rgba(196, 150, 232, 0.75)'],
					label: 'Page Visits'
				}],
				labels: [<?=$pie_labels;?>]
			},
			options: {
				tooltips: {
					enabled: true,
					displayColors: false,
				},
				legend: {
					display: true,
					labels: {
						fontColor: 'rgb(255, 255, 255)'
					}
				}
			}
		});

    });

    $('#modalCLIENT').on('show.bs.modal', function(e) {
        var url = 'https://dashboard.featherstonepartners.co.uk/admin/autologinas.php?cid=<?=$client_id;?>';
        $("#modalCLIENT iframe").attr("src", url);
    });

    $(".toggler").click(function(e){
      e.preventDefault();
      $('.'+$(this).attr('data-prod-name')).toggle();
      $('.head'+$(this).attr('data-prod-name')).toggleClass( "highlight normal" );
      $('.arrow'+$(this).attr('data-prod-name'), this).toggleClass("fa-caret-up fa-caret-down");
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
