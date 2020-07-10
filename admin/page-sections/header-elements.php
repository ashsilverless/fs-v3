<section>
<div class="container">
	
	<div id="time-out" class="counter-wrapper">
    <div class="message">User inactive.  Ending session in <span id='time-counter'></span> minutes. <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.47 22.27"><defs><style>.cls-1{fill:#97ceb5;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M21.27,11.32l-6.53,6.54a.75.75,0,0,1-.53.25.64.64,0,0,1-.65-.65.71.71,0,0,1,.19-.49l2.86-2.88,2.86-2.66-2.11.06H6.4a.63.63,0,0,1-.65-.67.62.62,0,0,1,.65-.65h11l2.11.06L16.61,7.56,13.75,4.7a.74.74,0,0,1-.19-.5.63.63,0,0,1,.65-.65.75.75,0,0,1,.53.25l6.53,6.53a.68.68,0,0,1,.2.5A.66.66,0,0,1,21.27,11.32ZM12.5,21.59a.68.68,0,0,0-.68-.67H4.5a3.16,3.16,0,0,1-3.15-3.15V4.5A3.15,3.15,0,0,1,4.5,1.35h7.32A.68.68,0,0,0,12.5.67.68.68,0,0,0,11.82,0H4.5A4.51,4.51,0,0,0,0,4.5V17.77a4.51,4.51,0,0,0,4.5,4.5h7.32A.69.69,0,0,0,12.5,21.59Z"/></g></g></svg></div>
    <div id="progress-bar"></div>
    </div>
	
  <div class="row">
      <nav class="col-md-12">
          <div class="row">
              <div id="logo" class="col-md-2">
                  <?php define('__ROOT__', dirname(dirname(__FILE__)));
                  include(__ROOT__.'/client/images/fs-logo.php'); ?>
              </div>
              <div id="topmenu" class="col-md-10">
                  <div id="menuitems" class="main-menu admin">
                    <?php // get current page name
                    function PageName() {
                      return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
                    }
                    $current_page = PageName();?>
                      <a class="main-menu__item <?php echo $current_page == 'home.php' ? 'active':NULL ?>" href="home.php">Dashboard</a>
                      <a class="main-menu__item <?php echo $current_page == 'funds.php' ? 'active':NULL ?>" href="funds.php">Funds</a>
                      <a class="main-menu__item <?php echo $current_page == 'assets.php' ? 'active':NULL ?>" href="assets.php">Asset Allocation & Holdings</a>
                      <a class="main-menu__item <?php echo $current_page == 'themes.php' ? 'active':NULL ?>" href="themes.php">Themes</a>
                      <a class="main-menu__item <?php echo $current_page == 'peers.php' ? 'active':NULL ?>" href="peers.php">Peers</a>
					  <a class="main-menu__item <?php echo $current_page == 'accounts.php' ? 'active':NULL ?>" href="accounts.php">Accounts</a>
                      <a class="main-menu__item <?php echo $current_page == 'clients.php' ? 'active':NULL ?>" href="clients.php">Clients</a>
                      <a class="main-menu__item <?php echo $current_page == 'staff.php' ? 'active':NULL ?>" href="staff.php">Staff</a>
                      <a class="button button__raised" href="#" data-toggle="modal" data-target="#logoutModal">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.64 17.54"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M8.21,17,.53,9.79a1.4,1.4,0,0,1-.53-1,1.39,1.39,0,0,1,.53-1L8.21.57A1.69,1.69,0,0,1,9.32,0a1,1,0,0,1,1,1V4.92h.34c6.64,0,10,4.07,10,11.53,0,.69-.4,1.09-.85,1.09a1,1,0,0,1-1-.66c-1.57-3.16-4.08-4.22-8.16-4.22h-.34v3.89a.93.93,0,0,1-.95,1A1.7,1.7,0,0,1,8.21,17Zm.87-1.22v-4c0-.22.09-.31.3-.31h1.09c4.72,0,7.69,1.53,8.88,4.27,0,.09.06.15.12.15s.1,0,.1-.14c-.13-5.55-2.7-9.73-9.1-9.73H9.38c-.21,0-.3-.08-.3-.3V1.68a.15.15,0,0,0-.15-.15.35.35,0,0,0-.21.1L1.52,8.45a.46.46,0,0,0-.18.32.42.42,0,0,0,.18.32l7.21,6.77a.31.31,0,0,0,.2.1A.15.15,0,0,0,9.08,15.79Z"/></g></g></svg>
                          Log Out</a>
                  </div>
              </div>
          </div>
      </nav>
  </div>
</div>
