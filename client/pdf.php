<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
//ini_set ("display_errors", "1");	error_reporting(E_ALL);
require_once('tcpdf_include.php');

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
	
  //    Get the funds   ///
  $query = "SELECT * FROM tbl_funds where bl_live = 1;";

  $result = $conn->prepare($query);
  $result->execute();

  // Parse returned data
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
	  $funds[] = $row;

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


//     Create the daily valuation data table
$dv_table = '';
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

        $dv_table .= '<table border="0" cellspacing="4" cellpadding="4" style="margin-bottom:16px;">';
        $dv_table .= '<tr><td>Account Name</td><td>Invested</td><td>Value</td><td>Gain (&pound;)</td><td>Gain(%)</td></tr>';
        $dv_table .= '<tr><td>'.$ac['ac_display_name'].'</td><td>'.$account_total.'</td><td>'.$grand_total_value.'</td><td>'.$GrandGain.'</td><td>'.$GrandGainPercent.'</td></tr>';
        $dv_table .= '</table>';
    }

endforeach;	

###############################################################################################################
###############################################################################################################
###############################################################################################################

###################################            PDF CREATION               #####################################

###############################################################################################################
###############################################################################################################
###############################################################################################################



// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Featherstoned');
$pdf->SetTitle('Portfolio Report');
$pdf->SetSubject('Portfolio Report');
$pdf->SetKeywords('Portfolio Report');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', '');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// create some HTML content
$html = '<h1>Portfolio Report</h1><h3>'.$user_name.'</h3>
<p>'.$dv_table.'</p>
<p></p>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


// reset pointer to the last page
$pdf->lastPage();

//Close and output PDF document
$pdf->Output('portfolio_report.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
