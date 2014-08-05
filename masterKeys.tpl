{include file="~widgets/billboard.tpl"} 
<div class="alert alert-warning text-align-center">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h3><i class="fa fa-key fa-5x"></i><br/><strong>Master Keys</strong></h3>
	<div class="input-group">
	    <span class="input-group-btn">
	        <button class="btn btn-warning active" type="button">
	            <i class="fa fa-key "></i>
	        </button>
	    </span>
	    <input id="swipe_key" type="email"
	           data-trigger="change" required="required" 
	           class="form-control"
	           name="key[email]" value="">
	    <span class="input-group-btn">
	        <button class="btn btn-success" type="button" onclick="window.updateNexusServer(this);">
	             <i class="fa fa-envelope "></i>
	        </button>
	    </span>
	</div>
    <p>The Master Key Grants a User Power over the Whole Domain</p>
</div>