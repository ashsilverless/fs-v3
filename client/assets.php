<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

function array_flatten($array) { 
  if (!is_array($array)) { 
    return FALSE; 
  } 
  $result = array(); 
  foreach ($array as $key => $value) { 
    if (is_array($value)) { 
      $result = array_merge($result, array_flatten($value)); 
    } 
    else { 
      $result[$key] = $value; 
    } 
  } 
  return $result; 
} 


$user_id = $_SESSION['fs_client_featherstone_uid'];
$client_code = $_SESSION['fs_client_featherstone_cc'];
$last_date = getLastDate('tbl_fs_transactions','fs_transaction_date','fs_transaction_date','fs_client_code = "'.$client_code.'"');
$lastlogin = date('g:ia \o\n D jS M y',strtotime(getLastDate('tbl_fsusers','last_logged_in','last_logged_in','id = "'.$_SESSION['fs_client_user_id'].'"')));



$strategy = getField('tbl_fsusers','strategy','id',$_SESSION['fs_client_user_id']);
$strat_id = getField('tbl_fs_strategy_names','id','strat_name',$strategy);



try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

    $query = "SELECT DISTINCT cat_id FROM `tbl_fs_asset_strat_vals` where strat_id LIKE '$strat_id' AND strat_val > 0 AND bl_live = 1 order by cat_id ASC;";
    $result = $conn->prepare($query);
    $result->execute();

          // Parse returned data
          while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			 $clientcats[] =  $row['cat_id'];
        }
	
	$client_cats = array_flatten($clientcats);
	
	
	$query = "SELECT *  FROM `tbl_fs_categories` where bl_live = 1 order by id desc limit 1;";
    $result = $conn->prepare($query);
    $result->execute();

          // Parse returned data
          while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			 $confirmed_date =  date('j M y',strtotime($row['correct_at']));

        }
	

  // $conn = null;        // Disconnect

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

		    <div class="col-md-9">

                <div class="border-box main-content">

                    <div class="main-content__head">
                        <h1 class="heading heading__1">Holdings & Asset Allocation</h1>
                        <p class="mb3">Data accurate as at <?= $confirmed_date;?></p>
                    </div>

<div class="asset-wrapper">
	
	<!-- ###########################       THE DONUT      ###################### -->
    <div class="asset-wrapper__chart">

        <svg width="100%" height="100%" viewBox="0 0 42 42" class="donut" aria-labelledby="" role="img" style="transform:rotate(-90deg);">
            <circle class="donut-hole" cx="21" cy="21" r="15.91549430918954" fill="#484848" role="presentation"></circle>
            <circle class="donut-ring" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#414141" stroke-width="10" role="presentation"></circle>
            <!--For each holding, create a segment like this
            Params =
            Stroke-dasharray: two figures.  The first is the value of the holding (ie, 30%); the second is the first value minus 100 (ie 30 - 100) therefore 70.

            Stroke-dashoffset: This is the running sum of the value of the holding, expressed as a negative value to enable positioning.
            -->
            <?php 
			foreach($client_cats as $catid) {
				
				$asset_color = getField('tbl_fs_categories','cat_colour','id',$catid);
				$asset_name = getField('tbl_fs_categories','cat_name','id',$catid);
				$thisAsset = 0;
				
				$query = "SELECT * FROM `tbl_fs_asset_strat_vals` where strat_id LIKE '$strat_id' AND cat_id LIKE '$catid' AND bl_live = 1;";
				$result = $conn->prepare($query);
				$result->execute();

				  // Parse returned data
				  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
					 $thisAsset += $row['strat_val'];
				  }
				
				$assetBalance = 100 - $thisAsset;
				?>
				<circle id="asset<?=$catid;?>" class="donut-segment <?=$catid;?> <?=$asset_name;?> asset<?=$catid;?>" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="<?= $asset_color;?>" stroke-width="10" stroke-dasharray="<?=$thisAsset;?> <?=$assetBalance;?>" stroke-dashoffset="-<?=$assetTotal;?>"></circle>
               <text x="22" y="22" text-anchor="middle" alignment-baseline="middle" class="asset<?=$catid;?>"><?=$thisAsset;?>%</text>
				
			<?php $assetTotal = $thisAsset += $assetTotal; }?>

        </svg>
		
		
        <div class="key border-box">
            <?php foreach($client_cats as $catid) {

                $asset_color = getField('tbl_fs_categories','cat_colour','id',$catid);
				$asset_name = getField('tbl_fs_categories','cat_name','id',$catid);
            ?>
            <div class="key__item">
                <div class="color" style="background-color:<?= $asset_color;?>;"></div>
                <h4 class="heading heading__4"><?=$asset_name;?></h4>
            </div>
            <?php }?>
        </div>
    </div>
    
	
	<!-- ###########################       LIST OF ASSETS      ###################### -->
	
	<div class="asset-wrapper__table">
        <div class="head">
            <h4 class="heading heading__4">Fund</h4>
            <h4 class="heading heading__4"><!--Growth Rate--></h4>
        </div>
        <?php 
			foreach($client_cats as $catid) {
				
				$asset_color = getField('tbl_fs_categories','cat_colour','id',$catid);
				$asset_name = getField('tbl_fs_categories','cat_name','id',$catid);
				
				
				

				?>
				<div id="asset<?=$catid;?>" class="item asset<?=$catid;?>" data-asset="asset<?=$catid;?>">
					
					<h4 class="heading heading__4"><div class="key__item"><div class="color" style="background-color:<?= $asset_color;?>;"></div><?=$asset_name;?></div></h4>
					<div class="toggle button button__raised button__toggle">
						<i class="fas fa-caret-down arrow"></i>
					</div>
					<p><table><?php 
						$query = "SELECT *  FROM `tbl_fs_assets` where cat_id LIKE '$catid' AND bl_live = 1 order by id ASC;";
						$result = $conn->prepare($query);
						$result->execute();

							  // Parse returned data
							  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
								  echo ('<tr><td><strong>'.$row['fs_asset_name'].'</strong></td></tr>');
								  echo ('<tr><td>'.$row['fs_asset_narrative'].'</td></tr>');
							}
					?></table></p>
				</div>
		
				
				<?php
				/*$query = "SELECT * FROM `tbl_fs_asset_strat_vals` where strat_id LIKE '$strat_id' AND cat_id LIKE '$catid' AND bl_live = 1;";
				$result = $conn->prepare($query);
				$result->execute();

				  // Parse returned data
				  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
					 $cat[$catid][] =  $row;
				  }*/

			}
		
		
		
		
		
		
		
		
	/*	foreach($assetData as $asset) {

          $asset_color = getField('tbl_fs_assets','asset_color','id',$asset['asset_id']);
				$asset_name = getField('tbl_fs_assets','fs_asset_name','id',$asset['asset_id']);
		  $asset_narrative = getField('tbl_fs_assets','fs_asset_narrative','id',$asset['asset_id']);
				$thisAsset = $asset['strat_val'];
				$assetBalance = 100 - $thisAsset;
        ?>
        <div id="asset<?=$asset['asset_id'];?>" class="item asset<?=$asset['asset_id'];?>" data-asset="asset<?=$asset['asset_id'];?>">
            <h4 class="heading heading__4"><?=$asset_name;?><?=$asset['asset_id'];?></h4>
            <h4 class="heading heading__4"><?=$thisAsset;?></h4>
            <div class="toggle button button__raised button__toggle">
                <i class="fas fa-caret-down arrow"></i>
            </div>
            <p><?=$asset_narrative;?></p>
        </div>
        <?php }*/?>
    </div>
	
	
	
</div>
    </div>
</div>

<?php define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/global-scripts.php');
require_once(__ROOT__.'/modals/logout.php');
require_once(__ROOT__.'/modals/time-out.php');
require_once('../page-sections/footer-elements.php');?>

  </body>
</html>
