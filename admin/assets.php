<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

$sortby = (isset($_GET['sortby']))? $_GET['sortby'] : "id";
$ascdesc = ($_GET['ad']=='asc')? 'ASC' : 'DESC';


try {
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

  $query = "SELECT *  FROM `tbl_fs_categories` where bl_live = 1 order by id desc limit 1;";
    $result = $conn->prepare($query);
    $result->execute();

          // Parse returned data
          while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			 $correct_at =  date('j M y',strtotime($row['correct_at']));

        }

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
<style>
	th.sorted.ascending:after {
	content: "  \2191";
}

th.sorted.descending:after {
	content: " \2193";
}
</style>

<div class="container">
    <div class="border-box main-content daily-data">
<div id="assetdetails" class="expand-panel newasset"></div>
<div id="editasset" class="expand-panel editasset-target"></div>
<div class="split-head">
    <div>
        <a href="#" class="addasset button button__raised button__inline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.82 16.22"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.25,15.57V8.78H.66a.67.67,0,0,1,0-1.33H7.25V.65a.66.66,0,0,1,1.32,0v6.8h6.6a.67.67,0,0,1,0,1.33H8.57v6.79a.66.66,0,0,1-1.32,0Z"/></g></g></svg>
            Add Asset</a>
        <h1 class="heading heading__2">Asset Allocation & Holdings</h1></div>

    <div class="text-right"><h3 class="heading heading__4"><span class=".update-date">Data Accurate as of <input class="accurateat" type="text" title="Data Accurate as of" value="<?=$correct_at;?>" size="12"></span></h3></div>

</div>
<div class="asset-table recess-box">	
	<table width="100%" border="0" cellpadding="10" cellspacing="2" id="assets" class="sortable-theme-bootstrap" data-sortable>
		<thead>
		<tr>
			<th class="heading heading__4">Asset Name</th>
			<th class="heading heading__4">Category</th>
			<?php $stratHeadings =  getTable('tbl_fs_strategy_names');
			foreach ($stratHeadings as $strathead):
				$portfolioChar = $strathead['strat_name']; ?>
			<th class="heading heading__4"><?=$portfolioChar;?></th>
			<?php endforeach; ?>
			<th class="heading heading__4" data-sortable="false">&nbsp;</th>
		</tr>
	</thead>
		
  <tbody>
	  <?php
        try {
          // Connect and create the PDO object
          $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
          $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

            $query = "SELECT *  FROM `tbl_fs_assets` where bl_live = 1";

            $result = $conn->prepare($query);
            $result->execute();

                  // Parse returned data
                  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
					  $catname = getField('tbl_fs_categories','cat_name','id',$row['cat_id']);;
                  ?>
	  <tr>
		  <td><p style="color: #629FD6;font-size: 16px;text-transform: uppercase;"><?= $row['fs_asset_name'];?></p></td>
		  <td><p style="color: white;font-size: 16px;text-transform: uppercase;"><?= $catname;?></p></td>
		<?php foreach ($stratHeadings as $strathead):
					  $sval = '';
					 $sval = getStratVal($row['id'],$strathead['id'],'strat_val');
					  $sval == 0 ? $svalShow = '' : $svalShow = $sval;
                      $total_val[$strathead['id']] += $sval; ?>
		  <td><p style="color: #96E8C4;;font-size: 16px;text-transform: uppercase;"><?=$svalShow;?></p></td>  
		<?php endforeach; ?>
		<td><p><a href="#?id=<?=$row['id'];?>" class="editasset-trigger button button__raised">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.77 20.77"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M3.69,9.72a.66.66,0,0,1,0,1.32h-3a.66.66,0,1,1,0-1.32ZM5.2,14.65a.64.64,0,0,1,.92,0,.66.66,0,0,1,0,.93L4,17.71a.67.67,0,0,1-.93,0,.66.66,0,0,1,0-.93ZM3.07,4A.65.65,0,1,1,4,3.07L6.12,5.21a.64.64,0,0,1,0,.92.65.65,0,0,1-.92,0Zm6.2,6.61a.9.9,0,0,1,0-1.26.87.87,0,0,1,1.25,0l9.35,9.38a.91.91,0,0,1,0,1.26.88.88,0,0,1-1.26,0Zm3.92,2.26L10.27,9.93c-.16-.16-.32-.19-.47-.06a.31.31,0,0,0,0,.47l2.91,2.93ZM11,3.68a.66.66,0,1,1-1.31,0v-3A.66.66,0,0,1,11,.65Zm0,16.43a.66.66,0,1,1-1.31,0v-3a.66.66,0,1,1,1.31,0Zm5.74-17a.65.65,0,0,1,.93,0,.67.67,0,0,1,0,.93L15.57,6.13a.65.65,0,0,1-.93,0,.64.64,0,0,1,0-.92Zm.31,8a.66.66,0,1,1,0-1.32h3a.66.66,0,0,1,0,1.32Z"/></g></g></svg>
				Edit</a><a href="#" data-href="deleteasset.php?id=<?= $row['id'];?>" data-toggle="modal" data-target="#confirm-delete" class="button button__raised button__danger"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.82 21.82"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.71,19.39a.71.71,0,0,0-.54-.22H4.91c-1.57,0-2.26-.69-2.26-2.26V14.65a.67.67,0,0,0-.23-.53L.83,12.5a2,2,0,0,1,0-3.19l1.59-1.6a.72.72,0,0,0,.23-.54V4.92c0-1.59.69-2.27,2.26-2.27H7.17a.73.73,0,0,0,.54-.22L9.31.83a1.94,1.94,0,0,1,3.19,0l1.61,1.6a.71.71,0,0,0,.54.22h2.26c1.57,0,2.26.69,2.26,2.27V7.17a.72.72,0,0,0,.23.54L21,9.31a2,2,0,0,1,0,3.19L19.4,14.12a.67.67,0,0,0-.23.53v2.26c0,1.57-.69,2.26-2.26,2.26H14.65a.71.71,0,0,0-.54.22L12.5,21a1.94,1.94,0,0,1-3.18,0Zm4,.76,1.87-1.88a.89.89,0,0,1,.7-.29h2.67c.89,0,1.07-.17,1.07-1.07V14.23a1,1,0,0,1,.28-.69l1.89-1.87c.63-.64.63-.87,0-1.52L18.26,8.28a.94.94,0,0,1-.28-.7V4.92c0-.9-.18-1.08-1.07-1.08H14.24a.89.89,0,0,1-.7-.29L11.67,1.67C11,1,10.79,1,10.15,1.67L8.28,3.55a.89.89,0,0,1-.7.29H4.91C4,3.84,3.84,4,3.84,4.92V7.58a.94.94,0,0,1-.28.7L1.67,10.15c-.63.65-.63.88,0,1.52l1.89,1.87a1,1,0,0,1,.28.69v2.68c0,.9.17,1.07,1.07,1.07H7.58a.89.89,0,0,1,.7.29l1.87,1.88C10.79,20.79,11,20.79,11.67,20.15ZM6.89,14.38a.55.55,0,0,1,.18-.44l3-3-3-3a.54.54,0,0,1-.18-.44A.6.6,0,0,1,7.5,7a.54.54,0,0,1,.43.19l3,3,3-3A.57.57,0,0,1,14.32,7a.6.6,0,0,1,.61.6.58.58,0,0,1-.18.43l-3,3,3,3a.64.64,0,0,1,.19.45.61.61,0,0,1-.61.61.58.58,0,0,1-.45-.2l-3-3L8,14.79a.57.57,0,0,1-.45.2A.61.61,0,0,1,6.89,14.38Z"/></g></g></svg> Delete</a></p></td>
    </tr>
				  <?php }

					$conn = null;        // Disconnect

					}

					catch(PDOException $e) {
					echo $e->getMessage();
					}
					?>
	  
	  <tr>
		  <td colspan="2"><h3 class="heading heading__4" style="text-align:right;">Total</h3></td>
		  <?php foreach ($total_val as $total):
		  if(($total/10) != 10){ $style="color:red;";}else{$style="color:white";};?>
		  <td data-value="ZZZZZZZZZZZ"><p style="<?= $style ;?>"><?= $total ;?>%</p></td>
                <?php endforeach; ?>
		  <td>&nbsp;</td>
	  </tr>
    
  </tbody>
</table>

</div>

  </div>
    </div>

<?php
require_once('page-sections/footer-elements.php');
require_once('modals/delete-asset.php');
require_once('modals/logout.php');
require_once('modals/delete-cat.php');
require_once(__ROOT__.'/global-scripts.php');?>
<link rel="stylesheet" href="css/sortable-theme-dark.css" />
<script src="js/sortable.min.js"></script>
    <script>

		$('.accurateat').datepicker({  format: "yyyy-mm-dd" , todayHighlight: true });

        $('.accurateat').change(function() {
            var thedate = $(this).val();

            $.ajax({
                type: "POST",
                url: 'update_correctat.php',
                data: {ud: thedate},
                success: function(response)
                {
					$('.accurateat').val(response);

                  // nothing really to do, unless you want a tick to say its been done I suppose.
               }
           });
        });



		$(".toggler").click(function(e){
          e.preventDefault();
          $('.'+$(this).attr('data-prod-name')).toggle();
          $('.head'+$(this).attr('data-prod-name')).toggleClass( "highlight normal" );
          $('.arrow'+$(this).attr('data-prod-name'), this).toggleClass("fa-caret-up fa-caret-down");
    	});

		$(".addasset").click(function(e){
          e.preventDefault();
		  $("#assetdetails").load("add_asset.php");
          $('.expand-panel.newasset').addClass('open');
          $('.expand-panel__cancel-button').addClass('visible');
		});

        $(".expand-panel__cancel-button").click(function(e){
          e.preventDefault();
          $('.expand-panel').removeClass('open');
          $('.expand-panel__cancel-button').removeClass('visible');
          $('.addasset.button').show();
		});

		$(".editasset-trigger").click(function(e){
            e.preventDefault();
            var theme_id = getParameterByName('id',$(this).attr('href'));
            $("html, body").animate({ scrollTop: 0 }, "slow");
            $("#editasset").load("edit_asset.php?id="+theme_id);
            $('.expand-panel.editasset-target').addClass('open');
            $('.expand-panel__cancel-button').addClass('visible');
            $('.addasset.button').hide();
            //$('.expand-panel__cancel-button').hide();
		});

		$('#confirm-delete, #confirm-catdelete').on('show.bs.modal', function(e) {
			$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
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
