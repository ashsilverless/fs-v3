<div class="container">
  <div class="row">
      <div class="col-md-3">
        <div class="border-box sidebar">
              <h2 class="heading heading__3">Hello <?=$_SESSION['name'];?></h2>
              <p class="prompt">Not you ?  Click <a href="#">here</a></p>
              <a class="button button__raised mb1" href="settings.php">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.82 21.51"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M10.08,20.22l-.54,1A.47.47,0,0,1,9,21.5a.46.46,0,0,1-.41-.44L8.44,20a9.76,9.76,0,0,1-1.56-.58L6,20.12a.47.47,0,0,1-.6.07.49.49,0,0,1-.23-.55l.24-1.1a8.58,8.58,0,0,1-1.28-1.06l-1,.43a.46.46,0,0,1-.59-.16.41.41,0,0,1,0-.59l.6-.95a8,8,0,0,1-.82-1.45l-1.15.06a.46.46,0,0,1-.49-.33.46.46,0,0,1,.18-.57l.88-.71a8,8,0,0,1-.27-1.63L.36,11.23A.46.46,0,0,1,0,10.75a.47.47,0,0,1,.36-.49l1.08-.34a7.82,7.82,0,0,1,.27-1.63L.83,7.57A.42.42,0,0,1,.65,7a.45.45,0,0,1,.49-.33l1.15,0a8.91,8.91,0,0,1,.82-1.44l-.61-1a.41.41,0,0,1,0-.56.44.44,0,0,1,.59-.16l1,.42A10.08,10.08,0,0,1,5.45,3L5.21,1.86a.49.49,0,0,1,.23-.55.48.48,0,0,1,.6.08l.84.72a9.63,9.63,0,0,1,1.56-.55L8.6.44A.44.44,0,0,1,9,0a.47.47,0,0,1,.54.25l.54,1c.28,0,.54,0,.83,0s.55,0,.83,0l.53-1A.47.47,0,0,1,12.8,0a.45.45,0,0,1,.41.43l.16,1.12a10.29,10.29,0,0,1,1.56.55l.83-.73a.48.48,0,0,1,.61-.07.46.46,0,0,1,.22.55L16.37,3A10,10,0,0,1,17.64,4l1-.42a.45.45,0,0,1,.6.16.43.43,0,0,1,0,.57l-.61,1a9,9,0,0,1,.83,1.44l1.13,0a.45.45,0,0,1,.5.33.46.46,0,0,1-.17.55l-.89.72a9.35,9.35,0,0,1,.28,1.64l1.08.33a.51.51,0,0,1,0,1l-1.08.34a8.78,8.78,0,0,1-.28,1.64l.89.71a.48.48,0,0,1,.17.57.46.46,0,0,1-.5.33l-1.13-.06a8.64,8.64,0,0,1-.83,1.45l.61.95a.44.44,0,0,1,0,.59.47.47,0,0,1-.6.16l-1-.43a8.5,8.5,0,0,1-1.27,1.06l.22,1.1a.46.46,0,0,1-.22.55.48.48,0,0,1-.61-.07l-.83-.75a8.56,8.56,0,0,1-1.56.57l-.16,1.12a.46.46,0,0,1-.4.44.48.48,0,0,1-.54-.26l-.53-1a7.86,7.86,0,0,1-.83.05A7.4,7.4,0,0,1,10.08,20.22ZM6.19,17.67l3.12-5.13a2.28,2.28,0,0,1,0-3.48L6.22,3.82a8.34,8.34,0,0,0-3.57,6.94A8.35,8.35,0,0,0,6.19,17.67Zm13-6.39H13.25a2.42,2.42,0,0,1-2.35,1.93,2.38,2.38,0,0,1-.69-.1L7.12,18.22a8.28,8.28,0,0,0,12-6.94Zm0-1a8.28,8.28,0,0,0-12-7L10.19,8.5a2.28,2.28,0,0,1,.71-.12,2.44,2.44,0,0,1,2.34,1.87Zm-8.25,1.86a1.33,1.33,0,0,0,1.33-1.32,1.33,1.33,0,0,0-2.65,0A1.34,1.34,0,0,0,10.91,12.11Z"/></g></g></svg>
                  Settings</a>
              <a class="button button__raised" href="#" data-toggle="modal" data-target="#logoutModal">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.64 17.54"><defs><style>.cls-1{fill:#1d1d1b;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M8.21,17,.53,9.79a1.4,1.4,0,0,1-.53-1,1.39,1.39,0,0,1,.53-1L8.21.57A1.69,1.69,0,0,1,9.32,0a1,1,0,0,1,1,1V4.92h.34c6.64,0,10,4.07,10,11.53,0,.69-.4,1.09-.85,1.09a1,1,0,0,1-1-.66c-1.57-3.16-4.08-4.22-8.16-4.22h-.34v3.89a.93.93,0,0,1-.95,1A1.7,1.7,0,0,1,8.21,17Zm.87-1.22v-4c0-.22.09-.31.3-.31h1.09c4.72,0,7.69,1.53,8.88,4.27,0,.09.06.15.12.15s.1,0,.1-.14c-.13-5.55-2.7-9.73-9.1-9.73H9.38c-.21,0-.3-.08-.3-.3V1.68a.15.15,0,0,0-.15-.15.35.35,0,0,0-.21.1L1.52,8.45a.46.46,0,0,0-.18.32.42.42,0,0,0,.18.32l7.21,6.77a.31.31,0,0,0,.2.1A.15.15,0,0,0,9.08,15.79Z"/></g></g></svg>
                  Log Out</a>
			<p>&nbsp;</p>
              <p class="last-login"><span>Last Login</span><?=$lastlogin;?></p>
        </div>
    </div>
