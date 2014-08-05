<div class="alert alert-warning text-align-center">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h3><i class="fa {$LANG.XLOGIN.$method.ICON} fa-5x"></i><br/><strong>
       {$LANG.XLOGIN.$method.JUMBO.HEAD}
    </strong></h3>
    <form action="/{$toBackDoor}/{$Xtra}/{$method}/.json" onsubmit="window.addMasterKey(event,this);" >
	    
		<div class="input-group">
		    <span class="input-group-btn">
		        <button class="btn btn-warning active" type="button">
		            <i class="fa {$LANG.XLOGIN.$method.ICON} "></i>
		        </button>
		    </span>
		   	<input id="email_key" type="email"
		           data-trigger="change" required="required" 
		           class="form-control"
		           name="key[email]" value="">
		    <span class="input-group-btn">
		        <button class="btn btn-success" type="submit" >
		             <i class="fa fa-envelope "></i>
		        </button>
		    </span>
		    <script type="text/javascript">
				window.addMasterKey = function(e,f){ 
					var b = $(f).find('button[type="submit"]');
					b.toggleClass('btn-danger');
					var i = b.find('i')
					i.toggleClass('fa-envelope');
					i.toggleClass('fa-spinner fa-spin');

					$.ajax({
						type     : "POST",
						url      : "/{$toBackDoor}/{$Xtra}/{$method}/.json",
						data     : {
						    key : {
						        email : $('#email_key').val()
						    }
						},
						dataType : "json",
						success: function(data)
						{
						  // Handle the server response (display errors if necessary)
						  	DATA = data;
								i.toggleClass('fa-spinner fa-spin');
						    if(data.success){
						    	b.toggleClass('btn-success');
						    	i.toggleClass('fa-envelope'); 
						    }else{  
						    	b.toggleClass('btn-warning');
						    	i.toggleClass('fa-warning');
						    	alert(data.error);
						    }
						}
					});
					e.preventDefault();
				};
	        </script>
		</div>
	</form>
    <p> {$LANG.XLOGIN.$method.JUMBO.QUOTE}</p>
</div>


