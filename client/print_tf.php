<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
$user_id = $_SESSION['fs_client_user_id'];
//ini_set ("display_errors", "1");	error_reporting(E_ALL);

try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8


  //    Get the general products data for Client   ///
  $query = "SELECT * FROM tbl_fsusers where id LIKE '$user_id' AND bl_live = 1;";

  $result = $conn->prepare($query);
  $result->execute();

  // Parse returned data
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
	  $user_name = $row['user_name'];

  }

  $conn = null;        // Disconnect

}

catch(PDOException $e) {
  echo $e->getMessage();
}


define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/header.php');
require_once(__ROOT__.'/global-scripts.php');
?>
<div class="curtain"></div>

<div class="page-wrapper cover">
    <div class="left">
        <?php include(__ROOT__.'/client/images/fs-logo.php'); ?>
        <div></div>
    </div><!--left-->
    <div class="main">
        <div class="top-section">
            <h2 class="heading heading__3">
                <span>Advise</span>
                <span>Invest</span>
                <span>Manage</span>
            </h2>
        </div>
        <div class="main-section">
            <h1 class="heading heading__1">PORTFOLIO REPORT</h1>
            <h2 class="heading heading__2"><?=$user_name;?><span><?=$str_date;?></span>
        </div>
    </div><!--main-->
</div>

<!--==== END COVER PAGE=======-->

<?php

try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

  //    Get the funds   ///
  $query = "SELECT * FROM tbl_funds where bl_live = 1;";

  $result = $conn->prepare($query);
  $result->execute();

  // Parse returned data
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
	  $funds[] = $row;

  }


  //    Get the Client Accounts   ///

  $query = "SELECT * FROM `tbl_fs_client_accounts` where fs_client_id = '$user_id' AND bl_live = 1 ORDER by ca_order_by DESC;";
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
<div class="page-wrapper">
    <div class="left">
        <?php include(__ROOT__.'/client/images/fs-logo.php'); ?>
        <div></div>
    </div><!--left-->
    <div class="main">
        <div class="top-section">
            <h2 class="heading heading__3">Daily Valuation Data</h2>
        </div>
        <div class="main-section">
            <p>Data accurate as at <?= date('j M y',strtotime($last_date));?></p>

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

                    <?php
                        // Connect and create the PDO object
			  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
			  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8
				
				foreach ($accounts as $ac):

					$account_total = $account_shares = $grand_total_value = 0;   $acData = array();

						foreach ($funds as $isin):
				
							$isin_code =  $isin['fu_isin'];    $fund_name = $isin['fu_fund_name'];
				
							$account_subtotal = $account_subshares = $runningsharetotal = 0;
							$count = $totshares = $book_cost = $currentvalue = $gain_pounds = $gain_percent = 0;
							$average_cps = $total_shares = array();

				
							//    Get the Transactional Data   ///

							  $query = "SELECT * FROM `tbl_fs_transactions` where fs_client_code like '" . $ac['ac_client_code'] . "' AND fs_designation LIKE '" . $ac['ac_designation'] . "' AND fs_product_type LIKE '" . $ac['ac_product_type'] . "' AND fs_isin_code LIKE '" . $isin['fu_isin'] . "';";


							  $result = $conn->prepare($query);
							  $result->execute();

							  // Parse returned data

							  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
								  
								  
								  if ( $row['fs_deal_type'] != 'Periodic Advisor Charge' ){
									  
									  $value = round($row['fs_shares'] * $row['fs_t_price'],2);
									  
									  $account_total += $value;  
									  $pac = '';   $account_subtotal += $value;
								  } else {
									    $pac = '<span style="font-size:0.6em; margin-right:10px;">(Periodic Advisor Charge)</span>';
								  }
								  
								  $account_subshares = round($row['fs_shares']+$account_subshares,4);
								  
								  $row['fs_shares'] <= 0 ? $sold = '<span style="color:red"><b>Sold</b></span> ' : $sold = '<span style="color:CornflowerBlue"><b>Bought</b></span> ';
								  
								  
								  
								  
								  /* ############################################################################ */
								  /* ############################################################################ */
								  /* ############################################################################ */
								    $totshares += $row['fs_shares'];
						
									$total_shares[$count] = $total_shares[$count-1] + $row['fs_shares'];


									if($row['fs_shares'] > 0){
										$average_cps[$count] = (($average_cps[$count-1] * $total_shares[$count-1])+$value)/$totshares;
									}else{
										$average_cps[$count] = $average_cps[$count-1];
									}

									$book_cost = round($average_cps[$count] * $total_shares[$count],2);

									$invested_in_fund = $account_subtotal;

									$currentvalue = round($totshares * get_current_price($row['fs_isin_code'],$row['fs_transaction_date']),2);

									$gain = $currentvalue - $invested_in_fund;

								    $count++;

							  }
							
				
							$gain_pounds = round(($currentvalue - $account_subtotal),2);
							$gain_percent = round(((($currentvalue / $account_subtotal) * 100) - 100),2);
				
							$grand_total_value += $currentvalue;
				
							$account_shares = round($account_subshares+$account_shares,4);

				
						endforeach;
				
				
					$GrandGain = $grand_total_value -  $account_total;
					$GrandGainPercent = round(((($grand_total_value / $account_total) * 100) - 100),2);




					if($account_total >0){

?>
				
					<div class="data-table__account-wrapper <?=$ac['id'];?>">

						<div class="data-table__body">
							<div>
								<p class="heading heading__4"><?=$ac['ac_display_name'];?></p>
							</div>
							<div>
								<p class="heading heading__4"><?=$account_total;?></p>
							</div>
							<div>
								<p class="heading heading__4"><?=$grand_total_value;?></p>
							</div>
							<div>
								<p class="heading heading__4"><?=$GrandGain;?></p>
							</div>
							<div>
								<p class="heading heading__4"><?=$GrandGainPercent;?></p>
							</div>
						</div>
					<?php }
					echo ('</div>');
				endforeach;	?>
            </div>

</div>
                </div><!--data-table-->

            </div><!--data section-->
		<div class="charts"><?php foreach ($accounts as $account): ?><div class="chart<?=$account['id'];?>"></div><?php endforeach; ?></div>
        </div>
    </div><!--main-->
</div>
<!--
 <script>
	$(document).ready(function() {
		<?php foreach ($accounts as $account): ?>
			$(".chart<?=$account['id'];?>").load("chart_print.php?ac_id=<?=$account['id'];?>&t=365");
		<?php endforeach; ?>
	});

</script>
-->
<!--==== END VALUATION PAGE=======-->

<?php
$strategy = getField('tbl_fsusers','strategy','id',$_SESSION['fs_client_user_id']);
$strat_id = getField('tbl_fs_strategy_names','id','strat_name',$strategy);

try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

    $query = "SELECT *  FROM `tbl_fs_asset_strat_vals` where strat_id LIKE '$strat_id' AND bl_live = 1;";
    $result = $conn->prepare($query);
    $result->execute();

          // Parse returned data
          while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			 $assetData[] =  $row;

        }

  $conn = null;        // Disconnect

}

catch(PDOException $e) {
  echo $e->getMessage();
}

?>

<div class="page-wrapper">
    <div class="left">
        <?php include(__ROOT__.'/client/images/fs-logo.php'); ?>
        <div></div>
    </div><!--left-->
    <div class="main">
        <div class="top-section">
            <h2 class="heading heading__3">Holdings & Asset Allocation</h2>
        </div>
        <div class="main-section">
            <p class="mb3">Data accurate as at <?= $confirmed_date;?></p>
            <div class="asset-wrapper">
                <div class="asset-wrapper__chart">

					<svg width="100%" height="100%" viewBox="0 0 42 42" class="donut" aria-labelledby="" role="img" style="transform:rotate(-90deg);">
						<circle class="donut-hole" cx="21" cy="21" r="15.91549430918954" fill="#484848" role="presentation"></circle>
						<circle class="donut-ring" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#414141" stroke-width="10" role="presentation"></circle>
						<!--For each holding, create a segment like this
						Params =
						Stroke-dasharray: two figures.  The first is the value of the holding (ie, 30%); the second is the first value minus 100 (ie 30 - 100) therefore 70.

						Stroke-dashoffset: This is the running sum of the value of the holding, expressed as a negative value to enable positioning.
						-->
						<?php foreach($assetData as $asset) {
							$asset_color = getField('tbl_fs_assets','asset_color','id',$asset['asset_id']);
							$asset_name = getField('tbl_fs_assets','fs_asset_name','id',$asset['asset_id']);
							$thisAsset = $asset['strat_val'];
							$assetBalance = 100 - $thisAsset;
						?>

						   <circle id="asset<?=$asset['asset_id'];?>" class="donut-segment <?=$asset['asset_id'];?> <?=$asset_name;?> asset<?=$asset['asset_id'];?>" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="<?= $asset_color;?>" stroke-width="10" stroke-dasharray="<?=$thisAsset;?> <?=$assetBalance;?>" stroke-dashoffset="-<?=$assetTotal;?>"></circle>
						   <text x="22" y="22" text-anchor="middle" alignment-baseline="middle" class="asset<?=$asset['asset_id'];?>"><?=$thisAsset;?>%</text>
						   <?php $assetTotal = $thisAsset += $assetTotal;?>
					   <?php }?>
					</svg>
					<div class="key border-box">
						<?php foreach($assetData as $asset) {

						  $asset_color = getField('tbl_fs_assets','asset_color','id',$asset['asset_id']);
							$asset_name = getField('tbl_fs_assets','fs_asset_name','id',$asset['asset_id']);
							$thisAsset = $asset['strat_val'];
							$assetBalance = 100 - $thisAsset;
						?>
						<div class="key__item">
							<div class="color" style="background-color:<?= $asset_color;?>;"></div>
							<h4 class="heading heading__4"><?=$asset_name;?></h4>
						</div>
						<?php }?>
					</div>
				</div>
				
				<div class="asset-wrapper__table">
					<div class="head">
						<h4 class="heading heading__4">Fund</h4>
						<h4 class="heading heading__4">Growth Rate</h4>
					</div>
					<?php foreach($assetData as $asset) {

					  		$asset_color = getField('tbl_fs_assets','asset_color','id',$asset['asset_id']);
							$asset_name = getField('tbl_fs_assets','fs_asset_name','id',$asset['asset_id']);
					  		$asset_narrative = getField('tbl_fs_assets','fs_asset_narrative','id',$asset['asset_id']);
							$thisAsset = $asset['strat_val'];
							$assetBalance = 100 - $thisAsset;
					?>
					<div id="asset<?=$asset['asset_id'];?>" class="item asset<?=$asset['asset_id'];?>" data-asset="asset<?=$asset['asset_id'];?>">
						<h4 class="heading heading__4"><?=$asset_name;?></h4>
						<h4 class="heading heading__4"><?=$thisAsset;?></h4>
						<div class="toggle button button__raised button__toggle">
							<i class="fas fa-caret-down arrow"></i>
						</div>
						<p><?=$asset_narrative;?></p>
					</div>
					<?php }?>
				</div>
            </div>
        </div>
    </div><!--main-->
</div><!--page-wrapper-->

<!--==== END ASSETS PAGE=======-->

<?php

?>

<div class="page-wrapper">
    <div class="left">
        <?php include(__ROOT__.'/client/images/fs-logo.php'); ?>
        <div></div>
    </div><!--left-->
    <div class="main">
        <div class="top-section">
            <h2 class="heading heading__3">Current Investment Themes</h2>
        </div>
        <div class="main-section">
            <p class="mb3">Data accurate as at <?= $confirmed_date;?></p>

            <div class="themes-table front">
                	<?php
                	try {
                	  // Connect and create the PDO object
                	  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
                	  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8
                		$query = "SELECT *  FROM `tbl_fs_theme_strats` where strat_id = '$strat_id' AND strat_val = '1' AND bl_live = 1;";

                		debug($query);
                		$result = $conn->prepare($query);
                		$result->execute();
                			  // Parse returned data
                			  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
								$icon = getField('tbl_fs_themes','fs_theme_icon','id',$row['theme_id']);
								$title =  getField('tbl_fs_themes','fs_theme_title','id',$row['theme_id']);
								$narrative =  getField('tbl_fs_themes','fs_theme_narrative','id',$row['theme_id']);


							?>
                    		<div class="themes-table__item">
                    			<img src="../icons_folder/<?= $icon;?>">
                                <h3 class="heading heading__4"><?= $title;?></h3>
                    			<p><?= substr($narrative,0,385);?>...</p>
                    		</div>
                	<?php }
                	$conn = null;        // Disconnect
                	}
                	catch(PDOException $e) {
                	echo $e->getMessage();
                	}?>
                        </div>
            </div>

            </div>
        </div>
    </div><!--main-->
</div>

<!--==== END THEMES PAGE=======-->

<?php
$user_id = $_SESSION['fs_client_featherstone_uid'];
$client_code = $_SESSION['fs_client_featherstone_cc'];
$last_date = getLastDate('tbl_fs_transactions','fs_transaction_date','fs_transaction_date','fs_client_code = "'.$client_code.'"');

$lastlogin = date('g:ia \o\n D jS M y',strtotime(getLastDate('tbl_fsusers','last_logged_in','last_logged_in','id = "'.$_SESSION['fs_client_user_id'].'"')));
$confirmed_date = $row['confirmed_date']= date('d M Y');
try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8


     //    Get the Peer Group Data   ///

  $query = "SELECT * FROM tbl_fs_peers WHERE bl_live = 1 AND fs_trend_line = '0' ;";
  $peer_data = $peer_colour = $peer_name = '';

  $result = $conn->prepare($query);
  $result->execute();

  // Parse returned data
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
	  $peer_data .= "{ x: ".$row['fs_peer_return'].", y:".$row['fs_peer_volatility'].", n:'".$row['fs_peer_name']."'},";
	  $peer_colour .= '"'.$row['fs_peer_color'].'",';
	  $peer_name .= '"'.$row['fs_peer_name'].'",';
	  //$peer_data .= "[ ".$row['fs_peer_return'].",".$row['fs_peer_volatility'].", '".$row['fs_peer_name']."', 'point { size: 4; fill-color: ".$row['fs_peer_color']."; }','".$row['fs_peer_volatility']."% Volatility'],";
  }


$query = "SELECT * FROM tbl_fs_peers WHERE bl_live = 1 AND fs_trend_line = '1' ;";
  $peer_data_line = '';

  $result = $conn->prepare($query);
  $result->execute();

  // Parse returned data
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
	  $peer_data_line .= "{ x: ".$row['fs_peer_return'].", y:".$row['fs_peer_volatility'].", n:'".$row['fs_peer_name']."'},";
	  $peer_colour_line .= '"'.$row['fs_peer_color'].'",';
	  $peer_name_line .= '"'.$row['fs_peer_name'].'",';
  }

  $conn = null;        // Disconnect

}

catch(PDOException $e) {
  echo $e->getMessage();
}

?>

<div class="page-wrapper">
    <div class="left">
        <?php include(__ROOT__.'/client/images/fs-logo.php'); ?>
        <div></div>
    </div><!--left-->
    <div class="main">
        <div class="top-section">
            <h2 class="heading heading__3">Peer Group Comparison</h2>
        </div>
        <div class="main-section">
            <p class="mb3">Data accurate as at <?= $confirmed_date;?></p>
            <div class="chart-wrapper">
                <div class="chart-wrapper__x-axis">
                    <?php //create x axis values
                    $sum = 0;
                    for($i = 1; $i<=11; $i++) {?>
                        <div class="x-axis-values" style="left:<?php echo $sum * 10;?>%;"><?php echo $sum;?></div>
                    <?php $sum = $sum + 1;
                    }?>
                </div>
                <div class="chart-wrapper__y-axis">
                    <?php //create y axis values
                    $sum = 10;
                    for($i=10; $i>=0; $i--){?>
                        <div class="y-axis-values" style="bottom:<?php echo $sum * 10;?>%;"><?php echo $sum;?></div>
                        <?php $sum = $sum - 1;
                        }?>
                </div>
                <div class="chart-wrapper__y-label">Annualised Return (%)</div>
                <div class="chart-wrapper__x-label">Annualised Volatility (%)</div>
                <div class="chart-wrapper__inner">
                    <?php //create chart inner
                    $sum = 0;
                    for($i = 1; $i<=11; $i++) {?>
                        <div class="x-axis" style="left:<?php echo $sum * 10;?>%;"></div>
                        <div class="y-axis" style="top:<?php echo $sum *10;?>%;"></div>
                    <?php $sum = $sum + 1;
                    }?>

                    <?php //create data points
                    try {
                      // Connect and create the PDO object
                      $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
                      $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

                        $query = "SELECT *  FROM `tbl_fs_peers` where bl_live = 1;";

                        $result = $conn->prepare($query);
                        $result->execute();

                              // Parse returned data
                              while($row = $result->fetch(PDO::FETCH_ASSOC)) {  ?>

                                <div class="data-point trendline<?= $row['fs_trend_line'];?>" style="bottom:<?= $row['fs_peer_volatility'] * 10;?>%; left:<?= $row['fs_peer_return'] * 10;?>%;"><?= $row['fs_peer_name'];?></div>

                              <?php }
                              $conn = null;        // Disconnect
                          }
                          catch(PDOException $e) {
                          echo $e->getMessage();
                    }?>

                    <svg id="trendline" width='100%' height='100%' viewBox="0 0 100 100" preserveAspectRatio="none">

                        <polyline points="
                        <?php //create trendline
                        try {
                          // Connect and create the PDO object
                          $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
                          $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

                            $query = "SELECT *  FROM `tbl_fs_peers` where bl_live = 1;";

                            $result = $conn->prepare($query);
                            $result->execute();

                                  // Parse returned data
                                  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                    // first coord is multiplied by 10
                                    // second coord is multiplied by 100 and removed from 100
                                    //54,37 76,30
                                if($row['fs_trend_line'] == 1) {
                                    $trendRet = ($row['fs_peer_return'] * 10);
                                    $trendVol = 100 - ($row['fs_peer_volatility'] * 10);
                                    echo $trendRet. ',' .$trendVol. ' ';
                                }
                                }
                                  $conn = null;        // Disconnect
                              }
                              catch(PDOException $e) {
                              echo $e->getMessage();
                        }?>
                        "fill="none"/>
                    </svg>

                </div>
            </div><!--chart wrapper-->
        </div>
    </div><!--main-->
</div>

<!--==== END PEERS PAGE=======-->

<script src="https://code.jquery.com/jquery-3.4.1.js"
            integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
            crossorigin="anonymous"></script>
<!-- Print function run on page load-->
<script type="text/javascript" defer>

    $("html").delay(1000).queue(function(next) {
        window.print();
        //console.log('sssss');
    });
    window.onafterprint = function(){
        window.history.back();
    }
</script>
