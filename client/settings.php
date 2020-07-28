<?php
include 'inc/db.php';     # $host  -  $user  -  $pass  -  $db

$msg = $_GET['msg'];

$user_id = $_SESSION['fs_client_user_id'];
$client_code = $_SESSION['fs_client_featherstone_cc'];

$last_date = getLastDate('tbl_fs_transactions','fs_transaction_date','fs_transaction_date','fs_client_code = "'.$client_code.'"');

$lastlogin = date('g:ia \o\n D jS M y',strtotime(getLastDate('tbl_fsusers','last_logged_in','last_logged_in','id = "'.$_SESSION['fs_client_user_id'].'"')));

try {
  // Connect and create the PDO object
  $conn = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
  $conn->exec("SET CHARACTER SET $charset");      // Sets encoding UTF-8


	$query = "SELECT * FROM tbl_fsusers where id LIKE '$user_id' AND bl_live = 1;";

    $result = $conn->prepare($query);
    $result->execute();

	while($row = $result->fetch(PDO::FETCH_ASSOC)) {

		$clientData[] = $row;
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
require_once(__ROOT__.'/page-sections/header-elements.php');
require_once(__ROOT__.'/page-sections/sidebar-elements.php');
?>
<style>
	.Short {  
        width: 100%;  
        background-color: #dc3545;  
        margin: 10px 0; 
        height: 3px;  
        color: #dc3545;  
        font-weight: 500;  
        font-size: 1em;  
    }  
    .Weak {  
        width: 100%;  
        background-color: #ffc107;  
        margin: 10px 0;    
        height: 3px;  
        color: #ffc107;  
        font-weight: 500;  
        font-size: 1em;   
    }    
    .Strong {  
        width: 100%;  
        background-color: #28a745;  
        margin: 10px 0;   
        height: 3px;  
        color: #28a745;  
        font-weight: 500;  
        font-size: 1em;   
    }  

</style>
    <div class="col-md-9">
        <div class="border-box main-content">
            <div class="main-content__head">
                <h1 class="heading heading__1">Account Settings</h1>
            </div>

		    <form action="editclient.php" method="post" id="editclient" name="editclient" class="settings">

                <div class="fixed-details">
                    <h2 class="heading heading__2">Details</h2>
                    <!--<label>Prefix</label>
                        <div class="select-wrapper">
                            <select name="user_prefix" id="user_prefix" class="select-css">
								<option value="Mr" <?php if($clientData[0]['first_name']=='Mr'){?>selected<?php }?>>Mr</option>
                                <option value="Mrs" <?php if($clientData[0]['first_name']=='Mrs'){?>selected<?php }?>>Mrs</option>
                                <option value="Miss" <?php if($clientData[0]['first_name']=='Miss'){?>selected<?php }?>>Miss</option>
                                <option value="Dr" <?php if($clientData[0]['first_name']=='Dr'){?>selected<?php }?>>Dr</option>
                            </select>
                        </div>-->
					
					<label for="first_name" id="firstnamelabel">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?=$clientData[0]['first_name'];?>">
					
					<label for="last_name" id="lastnamelabel">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?=$clientData[0]['last_name'];?>">
					
					<label for="email_address" id="emaillabel">Email</label>
                    <input type="text" id="email_address" name="email_address" value="<?=$clientData[0]['email_address'];?>">
                </div>

                <div class="variable-details">
					
					<?php if($msg=='updated'){?>
					  <fieldset class="whtbrdr">
						<div id='updated'>
						<h3>Account Settings Successfully Updated</h3>
						</div>
					</fieldset>
					<?php } ?>
					<?php if($msg=='badpass'){?>
					  <fieldset class="whtbrdr">
						<div id='updated'>
						<h3 style="color:#FF1616;">Incorrect Current Password</h3>
						</div>
					</fieldset>
					<?php } ?>
					
                    <h2 class="heading heading__2">Change Password</h2>
                    <label for="password" id="currentpasswordlabel" >Current Password</label>
                    <input type="password" id="password" name="password" value="" autocomplete="new-password" autofill="off" class="mb1">

                    <label for="newpassword" id="newpasswordlabel" >New Password</label>
                    <input type="password" id="newpassword" name="newpassword" value="" class="mb1">
					
					<div class="confirm-message mb3" id="strengthMessage"></div>
					
                    <label for="confirmpassword" id="confirmpasswordlabel" class="mt3">Confirm Password</label>
                    <input type="password" id="confirmpassword" name="confirmpassword" value="" class="mb1">
					
					
					<div class="confirm-message">
						<span id="message"></span>
					</div>
                </div>
				 
                
                <!-- ##########################		     Client Settings    ####################### -->
                <input name="client_code" type="hidden" id="client_code" value="<?=$user_id?>">

                <input id="submit" type="submit" name="submit" value="Save Changes" />

            </form>

        </div><!--border box-->

		

    </div>
  </div>
</div>

<!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="../index.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

<!--    Logged Out  -->
    <div class="modal fade" id="loggedout" tabindex="-1" role="dialog" aria-labelledby="LoggedOut" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Your Session has Timed Out</h5>
        </div>
        <div class="modal-body">Select "Login" below if you want to continue your session.</div>
        <div class="modal-footer">
		  <a class="btn btn-primary" href="../index.php">Login</a>
          <a class="btn btn-secondary quit" href="">Quit</a>
        </div>
      </div>
    </div>
  </div>

  <?php define('__ROOT__', dirname(dirname(__FILE__)));
  require_once(__ROOT__.'/global-scripts.php');
  require_once('../page-sections/footer-elements.php');?>
  
    <script>
      feather.replace()
    </script>

    <script type="text/javascript">
		
		
		$(document).ready(function () { 
			
			$('#newpassword').on('keyup', function() {
				checkPasswordStrength($('#newpassword').val());	
			});
			
			$('#confirmpassword').on('keyup', function() {
				 
			    if ($('#newpassword').val() == $('#confirmpassword').val()) {
					$('#message').html('Matching').css('color', 'green');
					$('#submit').val('Save Changes');
					$('#submit').prop('disabled', false);
			    } else {
					$('#message').html('Not Matching').css('color', 'red');
					$('#submit').val('>> Disabled <<');
					$('#submit').prop('disabled', true);
			    }

			});
			
			function checkPasswordStrength(password) {
				var numbers = /([0-9])/;
				var lettersLower = /([a-z])/;
				var lettersUpper = /([A-Z])/;
				var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,>,<])/;

				if(password.length<6) {
					$('#strengthMessage').removeClass();
					$('#strengthMessage').addClass('Short');
					$('#submit').val('>> Disabled <<');
					$('#submit').prop('disabled', true);
					$('#strengthMessage').html("Weak (should be atleast 6 characters.)");
				} else {
					if(password.match(numbers) && password.match(lettersLower) && password.match(lettersUpper) && password.match(special_characters)) {
						$('#strengthMessage').removeClass();
						$('#strengthMessage').addClass('Strong');
						$('#strengthMessage').html("Strong Password");
					} else {
						$('#strengthMessage').removeClass();
						$('#strengthMessage').addClass('Weak');
						$('#strengthMessage').html("Weak (should include letters with mixed case, numbers and special characters.)");
						$('#submit').val('>> Disabled <<');
						$('#submit').prop('disabled', true);
					}
				}
			}

			
		}); 

    </script>
  </body>
</html>
