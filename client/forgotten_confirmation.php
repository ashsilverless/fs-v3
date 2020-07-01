<?php include 'header.php';
@session_start();
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

									<div class="col-12 mb-4"><p style="font-size:1.4em;">Thank you.</p>
										<p style="font-size:1.25em;">If the email address exists in the system, and is valid, an email will be sent to that account with a temporary password.<br>After logging into the system, please change your password with your 'Account Settings'</p> 
									</div>


    <div class="silverless-button">
		<a href="index.php" id="go" >&laquo; Back to Login</a>
    </div>


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
