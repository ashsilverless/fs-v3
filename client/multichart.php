<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

// ini_set ("display_errors", "1");	error_reporting(E_ALL);
$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $user_id = $_SESSION['fs_client_user_id'];
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "DELETE FROM tbl_multichart WHERE user_id = '$user_id';";
		$conn->exec($sql);
	$conn = null;




function date_compare($a, $b)
{
    $t1 = strtotime($a['datetime']);
    $t2 = strtotime($b['datetime']);
    return $t1 - $t2;
}

function insertdata($dt,$val,$wd){
	global $host,$user, $pass, $db, $charset;
	$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $user_id = $_SESSION['fs_client_user_id'];
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO tbl_multichart (user_id,dt_date,num_val,week_day) VALUES('$user_id','$dt','$val','$wd')";
	//debug($sql);
		$conn->exec($sql);
	$conn = null;
}



$user_id = $_SESSION['fs_client_user_id'];

try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8
	$query = "SELECT * FROM `tbl_fs_client_accounts` where fs_client_id = '$user_id' AND ca_linked = '0' AND bl_live = 1 ORDER by ca_order_by DESC;;";
	
	debug($query);

  $result = $conn->prepare($query);
  $result->execute();

  // Parse returned data
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $client_accounts[] = $row;
  }


	foreach ($client_accounts as $ca):

		  $query = "SELECT * FROM `tbl_accounts` where id = ".$ca['ac_account_id']." AND bl_live = 1;";
			debug($query);
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


$start_time = microtime(true);

$time_period = 180; //$_GET['t'];

if ($time_period == ''){ $time_period = 365; };

$user_id = $_SESSION['fs_client_user_id'];

foreach ($accounts as $account):


		$time_period > 366 ? $step = 7 : $step = 1;

		 $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
		 $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

				  $query = "SELECT * FROM `tbl_accounts` where id = ".$account['id']." AND bl_live = 1;";

				  $result = $conn->prepare($query);
				  $result->execute();

				  // Parse returned data
				  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
					  $clientCode = $row['ac_client_code'];
					  $designation = $row['ac_designation'];
					  $producttype = $row['ac_product_type'];
				  }


				  //    Get the Funds
				  $query = "SELECT * FROM `tbl_funds` where bl_live = 1;";

				  $result = $conn->prepare($query);
				  $result->execute();

				  // Parse returned data
				  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
					  $isincode[] = $row['fu_isin'];

				  }

		$thisday = $today;

		$time = strtotime($today.' -'.$time_period.' day');
		$lastyear = date("Y-m-d", $time);


		for($a=0;$a<$time_period;$a+=$step){

			$time = strtotime($lastyear.'+'.$a.' day');
			$thedate = date("Y-m-d", $time);
			$weekday = date("N", $thedate);

			if($weekday<6){

				$cumulative = 0;
				foreach ($isincode as $isin):

					$query1 = "SELECT current_price,correct_at FROM `tbl_fs_fund` where isin_code like '" . $isin . "' AND correct_at <= '" . $thedate . "' ORDER BY correct_at desc LIMIT 1;";

					$result1 = $conn->prepare($query1);
					$result1->execute();

					while($row1 = $result1->fetch(PDO::FETCH_ASSOC)) {
						$fundPrice1 = $row1['current_price'];
						$correct_at = $row1['correct_at'];
					}

					$query2 = "SELECT SUM(fs_shares) AS value_sum FROM `tbl_fs_transactions` WHERE fs_client_code like '" . $clientCode . "' AND fs_designation LIKE '" . $designation . "' AND fs_product_type LIKE '" . $producttype . "' AND fs_isin_code LIKE '" . $isin . "' AND fs_transaction_date <= '".$thedate."';";
					$result2 = $conn->prepare($query2);
					$result2->execute();

					while($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
						$sum_of_shares = $row2['value_sum'];

					}

					$cumulative += $sum_of_shares*$fundPrice1;
				
					
					$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$sql = "INSERT INTO tbl_multichart (user_id,dt_date,num_val,week_day,fs_isin_code,fs_designation,fs_product_type) VALUES('$user_id','$correct_at','$cumulative','$weekday','$isin','$designation','$producttype')";

						$conn->exec($sql);

					//insertdata($correct_at,$cumulative,$weekday);
				
				endforeach;



			}

		}

		


			$time_elapsed_secs = microtime(true) - $start_time;

			$style1 = 'style = "text-decoration: underline;"';

endforeach;


$the_sum_dates = db_query("SELECT DISTINCT dt_date FROM tbl_multichart WHERE user_id = '$user_id'");

					
					


$is_ar = array('GB0009346486','GB00B1LB2Z79','GB00BJQWRN41');
					

foreach ($the_sum_dates as $dt):

	$query2 = "SELECT SUM(num_val) AS value_sum FROM `tbl_multichart` WHERE dt_date like '" . $dt['dt_date'] . "' AND user_id LIKE '" . $user_id . "' AND fs_isin_code LIKE 'GB0009346486';";

	$result2 = $conn->prepare($query2);
	$result2->execute();

    while($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
		$thelabeldate = date("d-m-Y", strtotime($dt['dt_date']));
		$labels .= $thelabeldate.',';
		$data_sum1 .= $row2['value_sum'].',';
    }

endforeach;

foreach ($the_sum_dates as $dt):

	$query2 = "SELECT SUM(num_val) AS value_sum FROM `tbl_multichart` WHERE dt_date like '" . $dt['dt_date'] . "' AND user_id LIKE '" . $user_id . "' AND fs_isin_code LIKE 'GB00B1LB2Z79';";

	$result2 = $conn->prepare($query2);
	$result2->execute();

    while($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
		$thelabeldate = date("d-m-Y", strtotime($dt['dt_date']));
		$data_sum2 .= $row2['value_sum'].',';
    }

endforeach;

foreach ($the_sum_dates as $dt):

	$query2 = "SELECT SUM(num_val) AS value_sum FROM `tbl_multichart` WHERE dt_date like '" . $dt['dt_date'] . "' AND user_id LIKE '" . $user_id . "' AND fs_isin_code LIKE 'GB00BJQWRN41';";

	$result2 = $conn->prepare($query2);
	$result2->execute();

    while($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
		$thelabeldate = date("d-m-Y", strtotime($dt['dt_date']));
		$data_sum3 .= $row2['value_sum'].',';
    }

endforeach;




$conn = null;        // Disconnect

?>


<div class="container account-chart-wrapper">
    <div class="row">
        <div class="col-md-12 controls">
            <h5 class="heading heading__5">Chart Period</h5>
            <a href="#?t=180" class="button button__inline graphtime" <?php if($time_period==180){ echo($style1); };?>>6 Months</a>
            <a href="#?t=365" class="button button__inline graphtime" <?php if($time_period==365){ echo($style1); };?>>1 Year</a>
            <a href="#?t=1095" class="button button__inline graphtime" <?php if($time_period==1095){ echo($style1); };?>>3 Years</a>
        </div>
    </div>
	<div class="row">
		<div class="col-md-12">
			<canvas class="my-4 w-100 chartjs-render-monitor" id="linechart" height="400"></canvas>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<p style="font-size:0.7em; color:#AAA;">Execution Time : <?=$time_elapsed_secs;?></p>
		</div>
	</div>
</div>




   <script>

		Chart.defaults.global.legend.display = false;

		var ctxline = document.getElementById('linechart');
		var myLineChart = new Chart(ctxline, {
			type: 'line',
			data: {
				datasets: [

					{
						fill:'origin',
						lineTension:0,
						borderColor:['rgba(150, 232, 196, 1)'],
						backgroundColor:['rgba(150, 232, 196, 0.1)'],
						borderWidth:2,
						color: ['rgba(150, 232, 196, 0.95)'],
						label:'Total',
						pointBorderColor:['rgba(72, 72, 72, 0.1)'],
						pointHitRadius: 4,
						data:[<?=$data_sum1;?>],
					},
					{
						fill:'origin',
						lineTension:0,
						borderColor:['rgba(232, 232, 196, 1)'],
						backgroundColor:['rgba(232, 232, 196, 0.1)'],
						borderWidth:2,
						color: ['rgba(233, 232, 196, 0.95)'],
						label:'Total',
						pointBorderColor:['rgba(72, 72, 72, 0.1)'],
						pointHitRadius: 4,
						data:[<?=$data_sum2;?>],
					},
					{
						fill:'origin',
						lineTension:0,
						borderColor:['rgba(150, 232, 232, 1)'],
						backgroundColor:['rgba(150, 232, 232, 0.1)'],
						borderWidth:2,
						color: ['rgba(150, 232, 232, 0.95)'],
						label:'Total',
						pointBorderColor:['rgba(72, 72, 72, 0.1)'],
						pointHitRadius: 4,
						data:[<?=$data_sum3;?>],
					}
					],
				labels: [<?=$labels;?>]
			},

			options: {
				scales: {
					yAxes: [{
					  ticks: {
						beginAtZero: true,
						  userCallback: function(value, index, values) {
							value = value.toString();
							value = value.split(/(?=(?:...)*$)/);
							value = value.join('.');
							return '£' + value;
						   }
					  }
					}]
				},
				tooltips: {
					enabled: true
				},
				legend: {
					display: false,
					labels: {
						fontColor: 'rgb(255, 255, 255)'
					}
				},
				title: {
				},
				elements: {
                    point:{
                        radius: 0
                    }
                }
			}
		});

    </script>

