<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

//  Record per page
if($_GET['rpp']!=""){
	$_SESSION["rpp"] = $_GET['rpp'];
}

if($_GET['page']!=""){
	$page=$_GET['page'];
}

if($page==""){
	$page = 0;
}

$recordsPerPage = $_SESSION["rpp"];

if($recordsPerPage==""){
	$recordsPerPage = 10;
}

debug($recordsPerPage.'  :  '.$page.'  :  '.$_GET['a_name']);

//    Get the account and fund details
try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8


	#######################################################################
	#######################################################################
	if($_GET['a_name']!=""){
		$query = "SELECT * FROM `tbl_accounts` where bl_live = 1 AND ac_display_name LIKE '".$_GET['a_name']."' order by ac_client_code asc;";

		$result = $conn->prepare($query);
		$result->execute();

		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}

		$num_rows = count($rows);

		$totalPageNumber = ceil($num_rows / $recordsPerPage);
		$offset = $page*$recordsPerPage;

		$query = "SELECT * FROM `tbl_accounts` where bl_live = 1 AND ac_display_name LIKE '".$_GET['a_name']."' order by ac_client_code asc LIMIT $offset,$recordsPerPage;";

		$result = $conn->prepare($query);
		$result->execute();

		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$accounts[] = $row;
		}

	}else{

		$query = "SELECT id FROM `tbl_accounts` where bl_live = 1 order by ac_client_code asc;";

		$result = $conn->prepare($query);
		$result->execute();

		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}

		$num_rows = count($rows);

		$totalPageNumber = ceil($num_rows / $recordsPerPage);
		$offset = $page*$recordsPerPage;

		debug($num_rows);

		$query = "SELECT *  FROM `tbl_accounts` where bl_live = 1 order by ac_client_code asc LIMIT $offset,$recordsPerPage;";

		$result = $conn->prepare($query);
		$result->execute();

		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$accounts[] = $row;
		}
	}
	#######################################################################
	#######################################################################

  $conn = null;        // Disconnect

}

catch(PDOException $e) {
  echo $e->getMessage();
}

$rspaging = '<div style="margin:auto; padding:15px 0 0 0; text-align: center; font-size:16px; font-family: \'Ubuntu\',sans-serif;"><strong>'.$num_rows.'</strong> results in <strong>'.$totalPageNumber.'</strong> pages.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page : ';

if($page<3){
	$start=1;
	$end=7;
}else{
	$start=$page-2;
	$end=$page+4;
}

if($end >= $totalPageNumber){
  $endnotifier = "";
  $end = $totalPageNumber;
}else{
  $endnotifier = "...";
}

$frst = '<a href="?page=0&a_name='.$_GET['a_name'].'" style="font-size:13px; margin:5px; padding:5px; font-weight:bold;">|&laquo;</a>';
$last = '<a href="?page='.($totalPageNumber-1).'&a_name='.$_GET['a_name'].'" style="font-size:13px; margin:5px; padding:5px; font-weight:bold;">&raquo;|</a>';

$rspaging .=  $frst;
for($a=$start;$a<=$end;$a++){
	$a-1 == $page ? $lnk='<strong style="font-size:13px; border: solid 1px #BBB; margin:5px; padding:5px;">'.$a.'</strong>' : $lnk='<a href="?page='.($a-1).'&a_name='.$_GET['a_name'].'" style="font-size:13px; margin:5px; padding:5px;">'.$a.'</a>';
	$rspaging .=  $lnk;
}

$ipp = '<span style="margin-left:35px;">Show <a href="?rpp=10&a_name='.$_GET['a_name'].'">10</a>&nbsp;|&nbsp;<a href="?rpp=30&a_name='.$_GET['a_name'].'">30</a>&nbsp;|&nbsp;<a href="?rpp=50&a_name='.$_GET['a_name'].'">50</a>&nbsp;|&nbsp;<a href="?rpp=999&a_name='.$_GET['a_name'].'"><strong>All</strong></a></span>';

$rspaging .= $endnotifier.$last.'</div><div style="margin:auto; padding:5px 0 15px 0; text-align: center; font-size:16px; font-family: \'Ubuntu\',sans-serif;">'.$ipp.'</div>';

define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/header.php');
require_once('page-sections/header-elements.php');
?>

<div class="container">
    <div class="border-box main-content">
        <div id="add-account" class="expand-panel newaccount"></div>
        <div id="edit-account" class=""></div>
        <div class="split-head">
            <div>
                <a href="#" class="addAccount button button__raised button__inline">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.82 16.22"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.25,15.57V8.78H.66a.67.67,0,0,1,0-1.33H7.25V.65a.66.66,0,0,1,1.32,0v6.8h6.6a.67.67,0,0,1,0,1.33H8.57v6.79a.66.66,0,0,1-1.32,0Z"/></g></g></svg>
                    Add Account</a>
        		<h1 class="heading heading__2">Accounts</h1>
            </div>
            <div class="border-box search-box">
                <form action="accounts_tf.php" method="get" id="account_search" name="account_search">
                    <label>Search for Account</label>
                    <div class="search-input"><input type="text" name="a_name" size="30" class="a_name" placeholder="Account Name / Code"></div>
                        <button type="submit" class="button button__raised mb1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.36 18.54"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M0,7.39a7.4,7.4,0,0,1,14.79,0A7.29,7.29,0,0,1,13.15,12l5,5a.93.93,0,0,1,.25.65.91.91,0,0,1-1.55.66l-5-5A7.38,7.38,0,0,1,0,7.39Zm13.54,0a6.15,6.15,0,1,0-6.15,6.15A6.18,6.18,0,0,0,13.54,7.39Z"/></g></g></svg>
                            Search
                       </button>
                          <button onClick="window.location.reload();" class="button button__raised button__danger mb1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.82 21.82"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.71,19.39a.71.71,0,0,0-.54-.22H4.91c-1.57,0-2.26-.69-2.26-2.26V14.65a.67.67,0,0,0-.23-.53L.83,12.5a2,2,0,0,1,0-3.19l1.59-1.6a.72.72,0,0,0,.23-.54V4.92c0-1.59.69-2.27,2.26-2.27H7.17a.73.73,0,0,0,.54-.22L9.31.83a1.94,1.94,0,0,1,3.19,0l1.61,1.6a.71.71,0,0,0,.54.22h2.26c1.57,0,2.26.69,2.26,2.27V7.17a.72.72,0,0,0,.23.54L21,9.31a2,2,0,0,1,0,3.19L19.4,14.12a.67.67,0,0,0-.23.53v2.26c0,1.57-.69,2.26-2.26,2.26H14.65a.71.71,0,0,0-.54.22L12.5,21a1.94,1.94,0,0,1-3.18,0Zm4,.76,1.87-1.88a.89.89,0,0,1,.7-.29h2.67c.89,0,1.07-.17,1.07-1.07V14.23a1,1,0,0,1,.28-.69l1.89-1.87c.63-.64.63-.87,0-1.52L18.26,8.28a.94.94,0,0,1-.28-.7V4.92c0-.9-.18-1.08-1.07-1.08H14.24a.89.89,0,0,1-.7-.29L11.67,1.67C11,1,10.79,1,10.15,1.67L8.28,3.55a.89.89,0,0,1-.7.29H4.91C4,3.84,3.84,4,3.84,4.92V7.58a.94.94,0,0,1-.28.7L1.67,10.15c-.63.65-.63.88,0,1.52l1.89,1.87a1,1,0,0,1,.28.69v2.68c0,.9.17,1.07,1.07,1.07H7.58a.89.89,0,0,1,.7.29l1.87,1.88C10.79,20.79,11,20.79,11.67,20.15ZM6.89,14.38a.55.55,0,0,1,.18-.44l3-3-3-3a.54.54,0,0,1-.18-.44A.6.6,0,0,1,7.5,7a.54.54,0,0,1,.43.19l3,3,3-3A.57.57,0,0,1,14.32,7a.6.6,0,0,1,.61.6.58.58,0,0,1-.18.43l-3,3,3,3a.64.64,0,0,1,.19.45.61.61,0,0,1-.61.61.58.58,0,0,1-.45-.2l-3-3L8,14.79a.57.57,0,0,1-.45.2A.61.61,0,0,1,6.89,14.38Z"/></g></g></svg>
                              Clear
                        </button>
                    </form>
            </div>
        </div>

		<div class="accountlist">
			<div class="account-table">
    <div class="account-table__head">
        <h3 class="heading heading__4">Number</h3>
        <h3 class="heading heading__4">Display Name</h3>
        <h3 class="heading heading__4">Type</h3>
    </div>

	<div class="recess-box">

		<?php foreach ($accounts as $ac):   ?>
			<div class="account-table__item">
		        <h3 class="heading heading__4"><?= $ac['ac_client_code']?></h3>
				<h3 class="heading heading__4"><?= $ac['ac_display_name']?></h3>
				<h3 class="heading heading__4"><?= $ac['ac_product_type']?></h3>
				<!--<a href="?ac_id=<?= $ac['id'];?>" class="delaccount" style="margin-right:10px;">DEL</a>-->
				<a href="#?id=<?= $ac['id'];?>" class="button button__raised editAccount">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.77 20.77"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M3.69,9.72a.66.66,0,0,1,0,1.32h-3a.66.66,0,1,1,0-1.32ZM5.2,14.65a.64.64,0,0,1,.92,0,.66.66,0,0,1,0,.93L4,17.71a.67.67,0,0,1-.93,0,.66.66,0,0,1,0-.93ZM3.07,4A.65.65,0,1,1,4,3.07L6.12,5.21a.64.64,0,0,1,0,.92.65.65,0,0,1-.92,0Zm6.2,6.61a.9.9,0,0,1,0-1.26.87.87,0,0,1,1.25,0l9.35,9.38a.91.91,0,0,1,0,1.26.88.88,0,0,1-1.26,0Zm3.92,2.26L10.27,9.93c-.16-.16-.32-.19-.47-.06a.31.31,0,0,0,0,.47l2.91,2.93ZM11,3.68a.66.66,0,1,1-1.31,0v-3A.66.66,0,0,1,11,.65Zm0,16.43a.66.66,0,1,1-1.31,0v-3a.66.66,0,1,1,1.31,0Zm5.74-17a.65.65,0,0,1,.93,0,.67.67,0,0,1,0,.93L15.57,6.13a.65.65,0,0,1-.93,0,.64.64,0,0,1,0-.92Zm.31,8a.66.66,0,1,1,0-1.32h3a.66.66,0,0,1,0,1.32Z"/></g></g></svg>
					Edit
				</a>
				<a href="#" data-href="deleteaccount.php?id=<?= $ac['id'];?>" data-toggle="modal" data-target="#confirm-delete" class="button button__raised button__danger">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.82 21.82"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.71,19.39a.71.71,0,0,0-.54-.22H4.91c-1.57,0-2.26-.69-2.26-2.26V14.65a.67.67,0,0,0-.23-.53L.83,12.5a2,2,0,0,1,0-3.19l1.59-1.6a.72.72,0,0,0,.23-.54V4.92c0-1.59.69-2.27,2.26-2.27H7.17a.73.73,0,0,0,.54-.22L9.31.83a1.94,1.94,0,0,1,3.19,0l1.61,1.6a.71.71,0,0,0,.54.22h2.26c1.57,0,2.26.69,2.26,2.27V7.17a.72.72,0,0,0,.23.54L21,9.31a2,2,0,0,1,0,3.19L19.4,14.12a.67.67,0,0,0-.23.53v2.26c0,1.57-.69,2.26-2.26,2.26H14.65a.71.71,0,0,0-.54.22L12.5,21a1.94,1.94,0,0,1-3.18,0Zm4,.76,1.87-1.88a.89.89,0,0,1,.7-.29h2.67c.89,0,1.07-.17,1.07-1.07V14.23a1,1,0,0,1,.28-.69l1.89-1.87c.63-.64.63-.87,0-1.52L18.26,8.28a.94.94,0,0,1-.28-.7V4.92c0-.9-.18-1.08-1.07-1.08H14.24a.89.89,0,0,1-.7-.29L11.67,1.67C11,1,10.79,1,10.15,1.67L8.28,3.55a.89.89,0,0,1-.7.29H4.91C4,3.84,3.84,4,3.84,4.92V7.58a.94.94,0,0,1-.28.7L1.67,10.15c-.63.65-.63.88,0,1.52l1.89,1.87a1,1,0,0,1,.28.69v2.68c0,.9.17,1.07,1.07,1.07H7.58a.89.89,0,0,1,.7.29l1.87,1.88C10.79,20.79,11,20.79,11.67,20.15ZM6.89,14.38a.55.55,0,0,1,.18-.44l3-3-3-3a.54.54,0,0,1-.18-.44A.6.6,0,0,1,7.5,7a.54.54,0,0,1,.43.19l3,3,3-3A.57.57,0,0,1,14.32,7a.6.6,0,0,1,.61.6.58.58,0,0,1-.18.43l-3,3,3,3a.64.64,0,0,1,.19.45.61.61,0,0,1-.61.61.58.58,0,0,1-.45-.2l-3-3L8,14.79a.57.57,0,0,1-.45.2A.61.61,0,0,1,6.89,14.38Z"/></g></g></svg>
					Delete
				</a>

			</div>
		<?php endforeach; ?>
<?php echo ($rspaging);?>
	</div><!--recess-->

</div><!--account-table-->
		</div>

    </div>
</div>

<?php
require_once('page-sections/footer-elements.php');
require_once('modals/delete-account.php');
require_once('modals/logout.php');
require_once('modals/login_as.php');
require_once('modals/delete-cat.php');
require_once(__ROOT__.'/global-scripts.php');?>

<script>
  feather.replace()
</script>
<script src="js/typeahead.js"></script>
    <script>

	$(document).ready(function() {

		$('input.a_name').typeahead({
                name: 'a_name',
                remote: 'a_name.php?query=%QUERY'

            });

		/*$("#account_search").submit(function(e) {

			e.preventDefault(); // avoid to execute the actual submit of the form.

			var form = $(this);
			var url = 'getaccounts.php';

			$.ajax({
				   type: "GET",
				   url: url,
				   data: form.serialize(), // serializes the form's elements.
				   success: function(data)
				   {
					   $(".accountlist").html(data); // show response from the php script.
				   }
				 });


		});
*/

		$('#confirm-delete').on('show.bs.modal', function(e) {
			$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
		});

		//$(".accountlist").load("getaccounts.php");

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

    $(".addAccount").click(function(e){
      e.preventDefault();
      $("#add-account").load("add_account.php");
      $("#add-account").addClass("open");
    });

	$(document).on('click', '.editAccount', function(e) {
      e.preventDefault();
	  var ac_id = getParameterByName('id',$(this).attr('href'));
		console.log(ac_id);
      $("#edit-account").load("edit_account.php?id="+ac_id);
      $("#edit-account").show();
      $("html, body").animate({ scrollTop: 0 }, "slow");
    });

    </script>
  </body>
</html>
