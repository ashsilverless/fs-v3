<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
/*
ini_set ("display_errors", "1");	error_reporting(E_ALL);

  */
$start_time = microtime(true);

$ac_id = $_GET['ac_id'];  $time_period = $_GET['t'];  $dl_data = $_GET['dl_data'];

if ($time_period == ''){ $time_period = 365; };



$time_period > 366 ? $step = 7 : $step = 1;

 $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
 $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

		  $query = "SELECT * FROM `tbl_accounts` where id = ".$ac_id." AND bl_live = 1;";

		  $result = $conn->prepare($query);
		  $result->execute();

		  // Parse returned data
		  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			  $title = $row['ac_display_name'];
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
			  $f_name[$row['fu_isin']] = getfield('tbl_funds','fu_fund_name','fu_isin',$row['fu_isin']);

		  }






if( $dl_data == 'dl'){
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=".$clientCode.".csv");
}
$thisday = $today;

$time = strtotime($today.' -'.$time_period.' day');
$lastyear = date("Y-m-d", $time);

$time = strtotime("$lastyear first Monday");
	$lastyear = date("Y-m-d", $time);




if( $dl_data == 'dl'){
			echo ($title."\n\n");
			echo ("Date,ISIN,Value,Shares,Fund Price\n");
		}

$data1 = $data2 = $data3 = $data4 = array();

for($a=0;$a<$time_period;$a+=$step){

	$time = strtotime($lastyear.'+'.$a.' day');
	$thedate = date("Y-m-d", $time);
	$weekday = date("N", $time);

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

			$product1 = $sum_of_shares*$fundPrice1;          $cumulative += $product1;

			$product1 != '' ? $data[$isin] .= round($product1,2).',' : $data[$isin] .= '0,';

			if( $dl_data == 'dl'){
				echo ($thedate . ',' . $isin . ',' . $product1 . ',' . $sum_of_shares . ',' . $fundPrice1 . "\n");
			}

		endforeach;

		$thelabeldate = date("d-m-Y", strtotime($thedate));

		$labels .= "'".$thelabeldate."',";

		#################

		############################     AGGREGATION OF FUNDS    ################################

		$data_sum .= $cumulative . ',';

	}

}

$conn = null;        // Disconnect

if( $dl_data != 'dl'){

	$time_elapsed_secs = microtime(true) - $start_time;

	$style1 = 'style = "text-decoration: underline;"';
?>


<div class="container account-chart-wrapper">
    <div class="row">
        <div class="col-md-12 controls">
			<h3 class="heading heading__3" style="text-align:center;"><?=$title;?></h3>
            <p class="heading heading__5" style="text-align:center;">Chart Period&emsp;:&emsp;
            <a href="#?t=180&ac_id=<?=$ac_id;?>" class="button button__inline graphtime" <?php if($time_period==180){ echo($style1); };?>>6 Months</a>
            <a href="#?t=365&ac_id=<?=$ac_id;?>" class="button button__inline graphtime" <?php if($time_period==365){ echo($style1); };?>>1 Year</a>
            <a href="#?t=1095&ac_id=<?=$ac_id;?>" class="button button__inline graphtime" <?php if($time_period==1095){ echo($style1); };?>>3 Years</a>
			<a href="#?t=1825&ac_id=<?=$ac_id;?>" class="button button__inline graphtime" <?php if($time_period==1825){ echo($style1); };?>>5 Years</a>
			<a href="#?t=3650&ac_id=<?=$ac_id;?>" class="button button__inline graphtime" <?php if($time_period==3650){ echo($style1); };?>>10 Years</a></p>
        </div>
    </div>
	<div class="row">
		<div class="col-md-12">
			<canvas class="my-4 w-100 chartjs-render-monitor" id="linechart" height="400"></canvas>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<p style="font-size:0.6em; color:#868686;">Execution Time : <?=$time_elapsed_secs;?></p>
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
						data:[<?=$data_sum;?>],
					}
					],
				labels: [<?=$labels;?>]
			},

			options: {
				scales: {
					yAxes: [{
					  ticks: {
						beginAtZero: false,
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
