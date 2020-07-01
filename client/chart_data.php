<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
/*  
ini_set ("display_errors", "1");	error_reporting(E_ALL);

  */
$start_time = microtime(true);

$ac_id = $_GET['ac_id'];

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

echo ('<p>Title = '.$title.'&emsp;&emsp;Client Code = '.$clientCode.'&emsp;&emsp;Designation = '.$designation.'&emsp;&emsp;Product Type = '.$producttype.'</p>');

$isincode = 'GB0009346486';
$isincode2 = 'GB00BJQWRN41';
$isincode3 = 'GB00B1LB2Z79';

$thisday = $today;

$time = strtotime($today.' -1 year');
$lastyear = date("Y-m-d", $time);



$fundprice = $fundprice2 = $fundprice3 = array(); $dn = 0;

for($a=0;$a<365;$a++){
	
	$time = strtotime($lastyear.'+'.$a.' day');
	$thedate = date("Y-m-d", $time);
	$weekday = date("N", $time);
	
	if($weekday<6){
		
		$query = "SELECT current_price FROM `tbl_fs_fund` where isin_code like '" . $isincode . "' AND correct_at <= '" . $thedate . "' ORDER BY correct_at desc LIMIT 1;";
		
		$result = $conn->prepare($query);
	    $result->execute();

	    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		    $fundprice[$thedate] = $row['current_price'];
	    }
		
		#################
		
		$query = "SELECT current_price FROM `tbl_fs_fund` where isin_code like '" . $isincode2 . "' AND correct_at <= '" . $thedate . "' ORDER BY correct_at desc LIMIT 1;";
		
		$result = $conn->prepare($query);
	    $result->execute();

	    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		    $fundprice2[$thedate] = $row['current_price'];
	    }
		
		#################
		
		$query = "SELECT current_price FROM `tbl_fs_fund` where isin_code like '" . $isincode3 . "' AND correct_at <= '" . $thedate . "' ORDER BY correct_at desc LIMIT 1;";
		
		$result = $conn->prepare($query);
	    $result->execute();

	    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		    $fundprice3[$thedate] = $row['current_price'];
	    }

	}
	
}

echo ('<p>' . $isincode . ' Count = ' . count($fundprice) . '</p>');
echo ('<p>' . $isincode2 . ' Count = ' . count($fundprice2) . '</p>');
echo ('<p>' . $isincode3 . ' Count = ' . count($fundprice3) . '</p>');

echo ('<table border="0" cellspacing="2" cellpadding="2"><tr><td><b>ISIN CODE</b></td><td><b>DATE</b></td><td><b>SHARES</b></td><td><b>FUND PRICE</b></td><td><b>CALCULATION</b></td></tr>');


foreach ($fundprice as $queryDate => $fundPrice):

	$query = "SELECT SUM(fs_shares) AS value_sum FROM `tbl_fs_transactions` WHERE fs_client_code like '" . $clientCode . "' AND fs_designation LIKE '" . $designation . "' AND fs_product_type LIKE '" . $producttype . "' AND fs_isin_code LIKE '" . $isincode . "' AND fs_transaction_date <= '".$queryDate."';";
	$result = $conn->prepare($query);
    $result->execute();

	$sum = 0;

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $sum = $row['value_sum'];
    }

	echo ('<tr><td>'.$isincode . '</td><td>' . $queryDate . '</td><td>' . $sum. "</td><td>".$fundPrice."</td><td>".round($sum*$fundPrice,2)."</td></tr>");

	$data .= round($sum*$fundPrice,2).',';
	$labels .= "'".$queryDate."',";

endforeach;

echo ('</table>');

echo ('<table border="0" cellspacing="2" cellpadding="2"><tr><td><b>ISIN CODE</b></td><td><b>DATE</b></td><td><b>SHARES</b></td><td><b>FUND PRICE</b></td><td><b>CALCULATION</b></td></tr>');

foreach ($fundprice2 as $queryDate => $fundPrice):

	$query = "SELECT SUM(fs_shares) AS value_sum FROM `tbl_fs_transactions` WHERE fs_client_code like '" . $clientCode . "' AND fs_designation LIKE '" . $designation . "' AND fs_product_type LIKE '" . $producttype . "' AND fs_isin_code LIKE '" . $isincode2 . "' AND fs_transaction_date <= '".$queryDate."';";
	$result = $conn->prepare($query);
    $result->execute();

	$sum2 = 0;

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $sum2 = $row['value_sum'];
    }

	echo ('<tr><td>'.$isincode2 . '</td><td>' . $queryDate . '</td><td>' . $sum2. "</td><td>".$fundPrice."</td><td>".round($sum2*$fundPrice,2)."</td></tr>");

	$data2 .= round($sum2*$fundPrice,2).',';

endforeach;

echo ('</table>');

echo ('<table border="0" cellspacing="2" cellpadding="2"><tr><td><b>ISIN CODE</b></td><td><b>DATE</b></td><td><b>SHARES</b></td><td><b>FUND PRICE</b></td><td><b>CALCULATION</b></td></tr>');

foreach ($fundprice3 as $queryDate => $fundPrice):

	$query = "SELECT SUM(fs_shares) AS value_sum FROM `tbl_fs_transactions` WHERE fs_client_code like '" . $clientCode . "' AND fs_designation LIKE '" . $designation . "' AND fs_product_type LIKE '" . $producttype . "' AND fs_isin_code LIKE '" . $isincode3 . "' AND fs_transaction_date <= '".$queryDate."';";
	$result = $conn->prepare($query);
    $result->execute();

	$sum3 = 0;

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $sum3 = $row['value_sum'];
    }

	echo ('<tr><td>'.$isincode3 . '</td><td>' . $queryDate . '</td><td>' . $sum3. "</td><td>".$fundPrice."</td><td>".round($sum3*$fundPrice,2)."</td></tr>");


	$data3 .= round($sum3*$fundPrice,2).',';

endforeach;

echo ('</table>');

$conn = null;        // Disconnect

$time_elapsed_secs = microtime(true) - $start_time;
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/global-scripts.php');
?>


<div class="container">

        <div class="row">
            <div class="col-md-12">
                <canvas class="my-4 w-100 chartjs-render-monitor" id="linechart" height="400"></canvas>

            </div>
        </div>

    </div>



<!--<p style="font-size:0.7em; color:#666;">Execution Time : <?=$time_elapsed_secs;?></p>-->
   <script>

		Chart.defaults.global.legend.display = false;

		var ctxline = document.getElementById('linechart');
		var myLineChart = new Chart(ctxline, {
			type: 'line',
			data: {
				datasets: [{
					fill:false,
					lineTension:.3,
					borderColor:['rgba(0, 255, 0, 1)'],
					borderWidth:1,
                    color: ['rgba(253, 0, 0, 0.95)'],
					label:'<?=$isincode;?>',
					data:[<?=$data;?>],
				},{
					fill:false,
					lineTension:.3,
					borderColor:['rgba(255, 0, 0, 1)'],
					borderWidth:1,
                    color: ['rgba(253, 0, 0, 0.95)'],
					label:'<?=$isincode2;?>',
					data:[<?=$data2;?>],
				},{
					fill:false,
					lineTension:.3,
					borderColor:['rgba(0, 0, 255, 1)'],
					borderWidth:1,
                    color: ['rgba(253, 0, 0, 0.95)'],
					label:'<?=$isincode3;?>',
					data:[<?=$data3;?>],
				}],
				labels: [<?=$labels;?>]
			},

			options: { 
				tooltips: {
					enabled: true
				},
				legend: {
					display: true,
					labels: {
						fontColor: 'rgb(30, 30, 30)'
					}
				},
				title: {
					display: true,
					text: '<?=$title;?>'
				}
			}
		});
    </script>