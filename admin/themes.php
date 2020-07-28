<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db
?>
<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/header.php');
require_once('page-sections/header-elements.php');
?>

<div class="container">
    <div class="border-box main-content">
        <a href="#" class="addasset button button__raised button__inline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.82 16.22"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.25,15.57V8.78H.66a.67.67,0,0,1,0-1.33H7.25V.65a.66.66,0,0,1,1.32,0v6.8h6.6a.67.67,0,0,1,0,1.33H8.57v6.79a.66.66,0,0,1-1.32,0Z"/></g></g></svg>
            Add Theme</a>
        <div id="assetdetails" class="expand-panel newasset"></div>
        <div id="editasset" class="expand-panel editasset-target"></div>
<h1 class="heading heading__2">Themes</h1>

<div class="themes-table">
    <div class="themes-table__head">
        <h3 class="heading heading__4">Theme Name</h3>
        <h3 class="heading heading__4">Icon</h3>
        <h3 class="heading heading__4">Narrative</h3>
    </div>

    <div class="recess-box">

<?php
try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8

    $query = "SELECT *  FROM `tbl_fs_themes` where bl_live = 1;";

    $result = $conn->prepare($query);
    $result->execute();

          // Parse returned data
          while($row = $result->fetch(PDO::FETCH_ASSOC)) {  ?>
    <div class="themes-table__item">
        <h3 class="heading heading__4"><?= $row['fs_theme_title'];?></h3>
        <img src="../icons_folder/<?= $row['fs_theme_icon'];?>">
        <p><?= substr($row['fs_theme_narrative'],0,385);?>...</p>
        <a href="#?id=<?=$row['id'];?>" class="editasset-trigger button button__raised">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.77 20.77"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M3.69,9.72a.66.66,0,0,1,0,1.32h-3a.66.66,0,1,1,0-1.32ZM5.2,14.65a.64.64,0,0,1,.92,0,.66.66,0,0,1,0,.93L4,17.71a.67.67,0,0,1-.93,0,.66.66,0,0,1,0-.93ZM3.07,4A.65.65,0,1,1,4,3.07L6.12,5.21a.64.64,0,0,1,0,.92.65.65,0,0,1-.92,0Zm6.2,6.61a.9.9,0,0,1,0-1.26.87.87,0,0,1,1.25,0l9.35,9.38a.91.91,0,0,1,0,1.26.88.88,0,0,1-1.26,0Zm3.92,2.26L10.27,9.93c-.16-.16-.32-.19-.47-.06a.31.31,0,0,0,0,.47l2.91,2.93ZM11,3.68a.66.66,0,1,1-1.31,0v-3A.66.66,0,0,1,11,.65Zm0,16.43a.66.66,0,1,1-1.31,0v-3a.66.66,0,1,1,1.31,0Zm5.74-17a.65.65,0,0,1,.93,0,.67.67,0,0,1,0,.93L15.57,6.13a.65.65,0,0,1-.93,0,.64.64,0,0,1,0-.92Zm.31,8a.66.66,0,1,1,0-1.32h3a.66.66,0,0,1,0,1.32Z"/></g></g></svg>
            Edit</a><a href="#" data-href="deletetheme.php?id=<?= $row['id'];?>" data-toggle="modal" data-target="#confirm-delete" class="button button__raised button__danger"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.82 21.82"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.71,19.39a.71.71,0,0,0-.54-.22H4.91c-1.57,0-2.26-.69-2.26-2.26V14.65a.67.67,0,0,0-.23-.53L.83,12.5a2,2,0,0,1,0-3.19l1.59-1.6a.72.72,0,0,0,.23-.54V4.92c0-1.59.69-2.27,2.26-2.27H7.17a.73.73,0,0,0,.54-.22L9.31.83a1.94,1.94,0,0,1,3.19,0l1.61,1.6a.71.71,0,0,0,.54.22h2.26c1.57,0,2.26.69,2.26,2.27V7.17a.72.72,0,0,0,.23.54L21,9.31a2,2,0,0,1,0,3.19L19.4,14.12a.67.67,0,0,0-.23.53v2.26c0,1.57-.69,2.26-2.26,2.26H14.65a.71.71,0,0,0-.54.22L12.5,21a1.94,1.94,0,0,1-3.18,0Zm4,.76,1.87-1.88a.89.89,0,0,1,.7-.29h2.67c.89,0,1.07-.17,1.07-1.07V14.23a1,1,0,0,1,.28-.69l1.89-1.87c.63-.64.63-.87,0-1.52L18.26,8.28a.94.94,0,0,1-.28-.7V4.92c0-.9-.18-1.08-1.07-1.08H14.24a.89.89,0,0,1-.7-.29L11.67,1.67C11,1,10.79,1,10.15,1.67L8.28,3.55a.89.89,0,0,1-.7.29H4.91C4,3.84,3.84,4,3.84,4.92V7.58a.94.94,0,0,1-.28.7L1.67,10.15c-.63.65-.63.88,0,1.52l1.89,1.87a1,1,0,0,1,.28.69v2.68c0,.9.17,1.07,1.07,1.07H7.58a.89.89,0,0,1,.7.29l1.87,1.88C10.79,20.79,11,20.79,11.67,20.15ZM6.89,14.38a.55.55,0,0,1,.18-.44l3-3-3-3a.54.54,0,0,1-.18-.44A.6.6,0,0,1,7.5,7a.54.54,0,0,1,.43.19l3,3,3-3A.57.57,0,0,1,14.32,7a.6.6,0,0,1,.61.6.58.58,0,0,1-.18.43l-3,3,3,3a.64.64,0,0,1,.19.45.61.61,0,0,1-.61.61.58.58,0,0,1-.45-.2l-3-3L8,14.79a.57.57,0,0,1-.45.2A.61.61,0,0,1,6.89,14.38Z"/></g></g></svg> Delete</a>
    </div>
<?php }
$conn = null;        // Disconnect
}
catch(PDOException $e) {
echo $e->getMessage();
}?>


    </div>
</div><!--themes table-->


</div><!--col-12-->


      </div>
    </div>

<?php require_once('page-sections/footer-elements.php');
require_once('modals/delete-theme.php');
require_once('modals/logout.php');
require_once(__ROOT__.'/global-scripts.php');?>

    <script>

		$(".toggler").click(function(e){
          e.preventDefault();
          $('.'+$(this).attr('data-prod-name')).toggle();
          $('.head'+$(this).attr('data-prod-name')).toggleClass( "highlight normal" );
          $('.arrow'+$(this).attr('data-prod-name'), this).toggleClass("fa-caret-up fa-caret-down");
    	});

		$(".addtheme").click(function(e){
          e.preventDefault();
		  $("#themedetails").load("add_theme.php");
		});

		$(".edittheme").click(function(e){
          e.preventDefault();
		  var theme_id = getParameterByName('id',$(this).attr('href'));
			console.log(theme_id);
		  $("#themedetails").load("edit_theme.php?id="+theme_id);
		});

        $(".addasset").click(function(e){
          e.preventDefault();
		  $("#assetdetails").load("add_theme.php");
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
            $("#editasset").load("edit_theme.php?id="+theme_id);
            $('.expand-panel.editasset-target').addClass('open');
            $('.addasset.button').hide();
            //$('.expand-panel__cancel-button').hide();
		});

		$('#confirm-delete').on('show.bs.modal', function(e) {
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
