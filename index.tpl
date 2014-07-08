<div class="single-widget-container">
    <section class="widget login-widget">
        <!-- <header class="text-align-center">
            <h4>Login to your account</h4>
        </header> -->
        <div class="body">
            <form id="box-login" class="no-margin"
                  action="/.json" method="POST" onsubmit="return window.login.submit(this);">
                <div class="form-group no-margin"> 

                    <div class="input-group input-group-lg">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                        <input name="login[username]" id="login[username]" type="text" class="form-control input-transparent input-lg"
                               placeholder="Your Username">
                    </div>

                </div>

                <div class="form-group"> 

                    <div class="input-group input-group-lg">
                            <span class="input-group-addon">
                                <i class="fa fa-lock"></i>
                            </span>
                        <input name="login[password]" id="login[password]" type="password" class="form-control input-transparent input-lg"
                               placeholder="Your Password">
                    </div>
 
                {if $SUPER_ADMIN === false}
                {* FRESH INSTALL *} 
                    <div class="input-group input-group-lg">
                            <span class="input-group-addon">
                                <i class="fa fa-key"></i>
                            </span>
                        <input name="login[confirm]" id="login[confirm]" type="password" class="form-control input-transparent input-lg"
                               placeholder="Confirm">
                    </div>
 

                    <div class="input-group input-group-lg">
                            <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                            </span>
                        <input name="login[email]" id="login[email]" type="email" class="form-control input-transparent input-lg"
                               placeholder="Your Email">
                    </div>
 
                {/if}

                </div>
                <div class="col-md-12 btn-group">  
                    <button type="submit" class="btn   btn-lg btn-success col-md-7">
                        <i class="fa fa-lock"></i>
                        <small>Unlock</small>
                    </button>
                     <button type="submit" class="btn   btn-lg btn-info col-md-5">
                        <i class="fa fa-question"></i>
                        <small>Forgot</small>
                    </button>
                </div>
            </form>
        </div>
        <script type="text/javascript">
        	window.login = {
        		submit : function (e) {
        			E = e;
        			// var postData = $(this).serializeArray();
		            // var formURL = $(this).attr("action");
		              //e.preventDefault();
					  dataString = $("#box-login").serialize();

					  $.ajax({
						type     : "POST",
						url      : $("#box-login").attr("action"),
						data     : dataString,
						dataType : "json",
					    success: function(data)
					    {
					      // Handle the server response (display errors if necessary)

					      $.pjax({ 
                            container : '.content',
                            fragment  : '.content',
                            timeout   : 5000,
                            url       : window.location.pathname+window.location.search+window.location.hash
					      });

					    }
					  });
		            return false;
        		}
        	};
        </script>
       <!--  <footer>
            <div class="facebook-login">
                <a href="index.html"><span><i class="fa fa-facebook-square fa-lg"></i> LogIn with Facebook</span></a>
            </div>
        </footer> -->
    </section>
</div>