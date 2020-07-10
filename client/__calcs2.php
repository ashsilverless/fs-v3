<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
/*
ini_set ("display_errors", "1");	error_reporting(E_ALL);

  */
$user_id = $_SESSION['fs_client_user_id'];
$ca_linked = $_GET['ca_lnk'];


$lastlogin = date('g:ia \o\n D jS M y',strtotime(getLastDate('tbl_fsusers','last_logged_in','last_logged_in','id = "'.$_SESSION['fs_client_user_id'].'"')));
$testVar = 'test';
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



     //    Get the funds   ///


  $query = "SELECT * FROM tbl_funds where bl_live = 1;";


  $result = $conn->prepare($query);
  $result->execute();

  // Parse returned data
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
	  $funds[] = $row;

  }


  //    Get the Client Accounts   ///

  $query = "SELECT * FROM `tbl_fs_client_accounts` where fs_client_id = '$user_id' AND ca_linked = '$ca_linked' AND bl_live = 1 ORDER by ca_order_by DESC;;";

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

				// Connect and create the PDO object
			  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
			  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

				$super_invested = $super_value= 0;

				foreach ($accounts as $ac):

					$ac_name = '<span style="margin-left:24px; font-size:0.8em;">Account : (' . $ac['ac_client_code'] . ' / ' . $ac['ac_designation'] . ' / ' . $ac['ac_product_type'] . ')</span>';



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

					$acData[$isin_code]['fund_name'] = $fund_name;
					$acData[$isin_code]['isin'] = $isin_code;
					$acData[$isin_code]['invested_sub_total'] = $account_subtotal;
					$acData[$isin_code]['shares_in_fund'] = round($account_subshares,4);
					$acData[$isin_code]['book_cost'] = $book_cost;
					$acData[$isin_code]['value'] = $currentvalue;
					$acData[$isin_code]['gain_pounds'] = $gain_pounds;
					$acData[$isin_code]['gain_percent'] = $gain_percent;
					$acData[$isin_code]['benchmark'] = number_format(get_benchmark($isin_code),2);

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
								<p class="heading heading__4">&pound;<?=number_format($account_total,2);?></p>
							</div>
							<div>
								<p class="heading heading__4">&pound;<?=number_format($grand_total_value,2);?></p>
							</div>
							<div>
								<p class="heading heading__4">&pound;<?=number_format($GrandGain,2);?></p>
							</div>
							<div>
								<p class="heading heading__4"><?=number_format($GrandGainPercent,2);?>&percnt;</p>
							</div>
							<div>
								<a href="#?ac_id=<?=$ac['id'];?>" class="accountchart">
								<div class="button button__raised button__inline chart-select">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 29.96 25.73"><defs><style>.cls-1{fill:#97ceb5;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M29.31,25.73H0V.65a.65.65,0,0,1,1.3,0V24.43h28a.65.65,0,0,1,0,1.3ZM10.3,22V14.69A.65.65,0,0,0,9.65,14H4.27a.65.65,0,0,0-.65.65V22a.65.65,0,0,0,.65.65H9.65A.65.65,0,0,0,10.3,22ZM4.92,15.34H9v6H4.92ZM18.69,22V2.46A.65.65,0,0,0,18,1.81H12.65a.65.65,0,0,0-.65.65V22a.65.65,0,0,0,.65.65H18A.65.65,0,0,0,18.69,22ZM13.3,3.11h4.09V21.35H13.3ZM27.07,22V10.65a.65.65,0,0,0-.65-.65H21a.65.65,0,0,0-.65.65V22a.65.65,0,0,0,.65.65h5.38A.65.65,0,0,0,27.07,22ZM21.69,11.3h4.08V21.35H21.69Z"/></g></g></svg>
                                    <span>Chart</span>
                                </div></a>

							</div>
						</div>
                        <div class="toggle-wrapper">
                            <div class="toggle button button__raised button__toggle reveal-detail">
                                <i class="fas fa-caret-down arrow"></i> <span class="data-toggle-button">View Detailed Breakdown</span>
                            </div>
                        </div>

						<?php   $super_invested += $account_total;
								$super_value += $grand_total_value;  ?>

					<div class="toggle-section">
						<div class="data-table__extended titles">
							<div>
								<h4 class="heading heading__5">Fund Name</h4>
							</div>
							<div class="split">
								<div><h4 class="heading heading__5">Holding</h4></div>
								<div><h4 class="heading heading__5">Invested</h4></div>
							</div>
							<div class="split">
								<div><h4 class="heading heading__5">Book Cost</h4></div>
								<div><h4 class="heading heading__5">Value</h4></div>
							</div>
							<div class="split">
								<div><h4 class="heading heading__5">Growth(£)</h4></div>
								<div><h4 class="heading heading__5">Growth(%)</h4></div>
							</div>
							<div>
								<h4 class="heading heading__5">Benchmark</h4>
							</div>
						</div>
<?php
					foreach ($acData as $data):

						$holding = round(($data['shares_in_fund'] / $account_shares) * 100,2);
						if($data['shares_in_fund']>0){
							?>
						<div class="data-table__extended">
							<div><?=$data['fund_name'];?></div>
							<div class="split">
								<div><h4 class="heading heading__5"><?=number_format($holding,2);?>&percnt;</h4></div>
								<div><h4 class="heading heading__5">&pound;<?=number_format($data['invested_sub_total'],2);?></h4></div>
							</div>
							<div class="split">
								<div><h4 class="heading heading__5">&pound;<?=number_format($data['book_cost'],2);?></h4></div>
								<div><h4 class="heading heading__5">&pound;<?=number_format($data['value'],2);?></h4></div>
							</div>
							<div class="split">
								<div><h4 class="heading heading__5">&pound;<?=number_format($data['gain_pounds'],2);?></h4></div>
								<div><h4 class="heading heading__5"><?=number_format($data['gain_percent'],2);?>&percnt;</h4></div>
							</div>
							<div>
								<h4 class="heading heading__5"><?=$data['benchmark'];?>&percnt;</h4>
							</div>
						</div>

						<?php

						}

					endforeach;

					echo ( '</div>' );

					}
					echo ('</div>');
				endforeach;

				$SuperGain = $super_value -  $super_invested;
				$SuperGainPercent = round(((($super_value / $super_invested) * 100) - 100),2);




				?>

            </div>

</div>

<div class="data-table__foot">
                        <div>
                            <h3 class="heading heading__4">Totals</h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">&pound;<?=number_format($super_invested,2);?></h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">&pound;<?=number_format($super_value,2);?></h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4">&pound;<?=number_format($SuperGain,2);?></h3>
                        </div>
                        <div>
                            <h3 class="heading heading__4"><?=number_format($SuperGainPercent,2);?>&percnt;</h3>
                        </div>
                    </div>
