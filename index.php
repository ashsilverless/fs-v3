<?php include 'header.php';
include('connection.php');

$m_alert = '';
$m_show = getMShow();


if($m_show==1){
	$m_alert = '<h4>System currently undergoing Maintenenance</h4>';
};



$my_t=getdate(date("U"));

$my_t['mon'] < 10 ? $theMonth = "0".$my_t['mon'] : $theMonth = $my_t['mon'];
$my_t['mday'] < 10 ? $theDay = "0".$my_t['mday'] : $theDay = $my_t['mday'];

$today=$my_t['year']."-".$theMonth."-".$theDay;

$csrf		=	$connect->real_escape_string($_POST["csrf"]);
if ($csrf == $_SESSION["fs_client_token"]) {
	$username	= $connect->real_escape_string($_POST['username']);
	$password	= $connect->real_escape_string($_POST['password']);
	$_SESSION['login_email'] = $_POST['username'];
	/* Check Username and Password */
	$query		= db_query("select * from tbl_fsusers where email_address='".$username."' AND destruct_date > '$today';");
	$resuser = mysqli_num_rows($query);

	if($resuser == 1){
		$row = mysqli_fetch_array($query);
		session_regenerate_id();
		$_SESSION['fs_client_email'] 	= $row['email_address'];
		$dbhash 	= $row['password_hash'];
		$_SESSION['fs_client_secret'] = $row['googlecode'];

		$_SESSION['fs_client_name'] = $row['first_name'];
		$_SESSION['fs_client_username'] = $row['user_name'];
		$_SESSION['fs_client_phone'] = $row['telephone'];
		$_SESSION['fs_client_user_id'] = $row['id'];
		$_SESSION['fs_client_company_id'] = $row['company_id'];
		$_SESSION['fs_client_agent_level'] = $row['agent_level'];
		$_SESSION['fs_client_id'] = session_id();
		$_SESSION['fs_client_featherstone_cc'] = $row['fs_client_code'];
		$_SESSION['fs_client_featherstone_uid'] = $row['id'];

		$row['last_logged_in'] != '' ?	$_SESSION['fs_client_newregister'] = 0 : $_SESSION['fs_client_newregister'] = 1;

		$_SESSION['last_logged_in'] 	= $row['last_logged_in'];

		if(password_verify($password,$dbhash)){
			$_SESSION['loggedin'] = TRUE;
			header('Location:device_confirmations.php');
			exit();
		}else{
			$_SESSION['loggedin'] = FALSE;
			$strmsg = "Invalid Username or Password";
		}


	}else{
		$strmsg = "Invalid Username or Password";
	}

}
?>


  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Countries Row -->
          <div class="row">
            <div class="clearfix"></div>
            <div class="col-6 offset-3 login">
						<div class="text-center border-box login__inner">
								<h1 id="loginlogo" class="logo">
                                    <?php include 'client/images/fs-logo.php'; ?>
                                </h1>
                                <form name="reg" action="index.php" method="POST">
    <!--   ERROR BOX/   --><div class="error_box"><?=$strmsg;?></div><!--   /ERROR BOX   -->
    <input type="hidden" name="csrf" 	 value="<?php print $_SESSION["fs_client_token"]; ?>" >
    <input type="hidden" name="passcode" value="<?php echo $passcode; ?>" >
    <div class="form-group">
        <label>Email Address</label>
        <input type="text" name="username" id="username" autocomplete="off" required value="<?=$_SESSION['login_email'];?>">

    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" id="password" autocomplete="off" value="" required>

    </div>
    <div class="silverless-button">
        <?php if($m_alert==''){?><input  id="go" type="submit" value="Log in"><?php }?>
    </div>

     <div class="form-text text-right">
         <p>Forgot password? Click <a href="forgotten_password.php">here</a></p>
         <?=$m_alert;?>
    </div>

</form>

						</div>
					</div>
          </div>


        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Silverless 2019</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <?php define('__ROOT__', dirname(dirname(__FILE__)));
  require_once(__ROOT__.'/footer.php');?>
