<?php include 'header.php';
@session_start();
$e=$_GET['e'];
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
							<h2>Forgotten Password</h2>
							<?php if($e=='t'){?><div class="error_box" style="border:1px solid #F64;"><h3>ERROR</h3><p style="font-size:1.3em;">Invalid Password</p></div><?php }?>
                                <form name="reg" action="f_password.php" method="POST">
									<div class="col-12 mb-4"><p style="font-size:1.25em;">Please enter your email address below.</p>
										<p style="font-size:1.25em;">An email will be sent to that account with a temporary password.<br>After logging into the system, please change your password with your 'Account Settings'</p> 
									</div>
    <div class="form-group">
        <label>Email Address</label>
        <input type="text" name="username" id="username" autocomplete="off" required value="<?=$_SESSION['login_email'];?>">

    </div>

    <div class="silverless-button">
        <input  id="go" type="submit" value="Submit &raquo;">
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
