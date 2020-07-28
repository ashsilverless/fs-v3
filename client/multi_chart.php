<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
/*  
ini_set ("display_errors", "1");	error_reporting(E_ALL);
*/


$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $user_id = $_SESSION['fs_client_user_id'];
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "DELETE FROM tbl_multi_chart WHERE user_id = '$user_id';";
		$conn->exec($sql);
	$conn = null;




$start_time = microtime(true);

$time_period = $_GET['t'];  $dl_data = $_GET['dl_data'];

if ($time_period == ''){ $time_period = 180; };


$time_period > 366 ? $step = 7 : $step = 1;


 $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
 $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

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


		  //    Get the Funds
		  $query = "SELECT * FROM `tbl_funds` where bl_live = 1;";

		  $result = $conn->prepare($query);
		  $result->execute();

		  // Parse returned data
		  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			  $isincode[] = $row['fu_isin'];
			  $f_name[$row['fu_isin']] = getfield('tbl_funds','fu_fund_name','fu_isin',$row['fu_isin']);

		  }






if( $dl_data == 'dl'){
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=".$clientCode.".csv");
}

$thisday = $today;

$time = strtotime($today.' -'.$time_period.' day');
$lastyear = date("Y-m-d", $time);  $lastyearday = date("w", $time);


	$time = strtotime("$lastyear first Monday");
	$lastyear = date("Y-m-d", $time);


if( $dl_data == 'dl'){
			echo ($title."\n\n");
			echo ("Date,ISIN,Value,Shares,Fund Price\n");
		}


foreach ($accounts as $account):

		$query = "SELECT * FROM `tbl_accounts` where id = ".$account['id']." AND bl_live = 1;";

		  $result = $conn->prepare($query);
		  $result->execute();

		  // Parse returned data
		  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			  $clientCode = $row['ac_client_code'];
			  $designation = $row['ac_designation'];
			  $producttype = $row['ac_product_type'];
		  }

		for($a=0;$a<$time_period;$a+=$step){

			$time = strtotime($lastyear.'+'.$a.' day');
			$thedate = date("Y-m-d", $time);
			$weekday = (int)date("N", $time)+0;


			if($weekday<6){

				$cumulative = 0;
				foreach ($isincode as $isin):

					$query1 = "SELECT current_price FROM `tbl_fs_fund` where isin_code like '" . $isin . "' AND correct_at <= '" . $thedate . "' ORDER BY correct_at desc LIMIT 1;";

					$result1 = $conn->prepare($query1);
					$result1->execute();

					while($row1 = $result1->fetch(PDO::FETCH_ASSOC)) {
						$fundPrice1 = $row1['current_price'];
					}

					$query2 = "SELECT SUM(fs_shares) AS value_sum FROM `tbl_fs_transactions` WHERE fs_client_code like '" . $clientCode . "' AND fs_designation LIKE '" . $designation . "' AND fs_product_type LIKE '" . $producttype . "' AND fs_isin_code LIKE '" . $isin . "' AND fs_transaction_date <= '".$thedate."';";
					$result2 = $conn->prepare($query2);
					$result2->execute();

					while($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
						$sum_of_shares = $row2['value_sum'];

					}

					// ###################     Temporary Sanity Check       ################## //
							if($fundPrice1 > 10){ $fundPrice1 = $fundPrice1 / 100; debug("Whooops ".$fundPrice1); };
					// ###################     Temporary Sanity Check       ################## //			

					$product1 = $sum_of_shares*$fundPrice1;          $cumulative += $product1;

					$product1 != '' ? $data[$isin] .= round($product1,2).',' : $data[$isin] .= '0,';
				
					############################     AGGREGATION OF FUNDS    ################################

					$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
					$account_id = $account['id'];

					$sql = "INSERT INTO tbl_multi_chart (user_id,dt_date,num_val,week_day,fs_isin_code,fs_designation,fs_product_type,fs_account_id,sum_shares,fund_price) VALUES('$user_id','$thedate','$product1','$weekday','$isin','$designation','$producttype','$account_id','$sum_of_shares','$fundPrice1')";

					$conn->exec($sql);
				
				

					if( $dl_data == 'dl'){
						echo ($thedate . ',' . $isin . ',' . $product1 . ',' . $sum_of_shares . ',' . $fundPrice1 . "\n");
					}

				endforeach;

				#################

				

			}

		}

endforeach;

// ######################     Now aggregate the data from the multi-chart table    ############################### //
$the_sum_dates = db_query("SELECT DISTINCT dt_date FROM tbl_multi_chart WHERE user_id = '$user_id'");

foreach ($the_sum_dates as $dt):

	$query_sum = "SELECT SUM(num_val) AS value_sum FROM `tbl_multi_chart` WHERE dt_date like '" . $dt['dt_date'] . "' AND user_id LIKE '" . $user_id . "';";

	$result_sum = $conn->prepare($query_sum);
	$result_sum->execute();

    while($row = $result_sum->fetch(PDO::FETCH_ASSOC)) {
		$thelabeldate = date("d-m-Y", strtotime($dt['dt_date']));
		$labels .= '"'.$thelabeldate.'",';
		$data_sum .= $row['value_sum'].',';
    }


	foreach ($accounts as $account):
		$query_sum = "SELECT SUM(num_val) AS account_sum FROM `tbl_multi_chart` WHERE dt_date like '" . $dt['dt_date'] . "' AND user_id LIKE '" . $user_id . "' AND fs_account_id = ".$account['id'].";";

		$result_sum = $conn->prepare($query_sum);
		$result_sum->execute();

		while($row = $result_sum->fetch(PDO::FETCH_ASSOC)) {
			$data_sum_ac['ac'.$account['id']] .= $row['account_sum'].',';
		}

	endforeach;

endforeach;



$sql = "DELETE FROM tbl_multi_chart WHERE user_id = '$user_id';";
//$conn->exec($sql);

$conn = null;        // Disconnect

if( $dl_data != 'dl'){

	$time_elapsed_secs = microtime(true) - $start_time;

	$style1 = 'style = "text-decoration: underline;"';
?>


<div class="container account-chart-wrapper">
    <div class="row">
        <div class="col-md-12 controls">
            <h5 class="heading heading__5">Chart Period</h5>
            <a href="#?t=180" class="button button__inline multigraphtime" <?php if($time_period==180){ echo($style1); };?>>6 Months</a>
            <a href="#?t=365" class="button button__inline multigraphtime" <?php if($time_period==365){ echo($style1); };?>>1 Year</a>
            <a href="#?t=1095" class="button button__inline multigraphtime" <?php if($time_period==1095){ echo($style1); };?>>3 Years</a>
			<a href="#?t=1825" class="button button__inline multigraphtime" <?php if($time_period==1825){ echo($style1); };?>>5 Years</a>
			<a href="#?t=3650" class="button button__inline multigraphtime" <?php if($time_period==3650){ echo($style1); };?>>10 Years</a>
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
	   
	   var formatter = new Intl.NumberFormat('en-GB', {
		  style: 'currency',
		  currency: 'GBP',
		});

		Chart.defaults.global.legend.display = false;

		var ctxline = document.getElementById('linechart');
		var myLineChart = new Chart(ctxline, {
			type: 'line',
			data: {
				datasets: [
					<?php foreach ($data_sum_ac as $id => $val):?>
					{
						fill:'origin',
						lineTension:0,
						borderColor:['rgba(150, 232, 196,0.3)'],
						backgroundColor:['rgba(0, 0, 0, 0)'],
						borderWidth:2,
						color: ['rgba(150, 232, 196, 0.1)'],
						label:'<?=getField('tbl_accounts','ac_display_name','id',str_replace("ac","",$id));?>',
						pointBorderColor:['rgba(72, 72, 72, 0.1)'],
						pointHitRadius: 4,
						data:[<?=$val?>],
					},	
					<?php endforeach;?>
					{
						fill:'origin',
						lineTension:0,
						borderColor:['rgba(225,135,67,1.00)'],
						backgroundColor:['rgba(225,135,67, 0.1)'],
						borderWidth:2,
						color: ['rgba(225,135,67, 0.95)'],
						label:'Total',
						pointBorderColor:['rgba(72, 72, 72, 0.1)'],
						pointHitRadius: 4,
						data:[<?=$data_sum;?>],
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
					enabled: true,
					displayColors: false,
					callbacks: {
						label: function(tooltipItem, data) {
							return formatter.format(tooltipItem.yLabel);
						},
					}
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
<?php } ?>
