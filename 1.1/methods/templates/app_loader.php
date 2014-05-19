<?php

$isclean = null;
if(!empty($_GET['cleanLoad'])){
	$isclean = 'data-cache-purge="true" ';	
}

//dump($_COOKIE);
// App Loader - cahce objects etc...

// check the user hasa site first:
if( empty( $user->siteguid )){

	?>
	<div class="row" id="loader-panel">
		<div class="col-sm-4 col-md-4 col-sm-offset-4">
			<span class="trigger" id="select-site" data-call="sites" data-cache-session="sites" data-target="#select-site" data-template-url="http://api.humble.co.za/1.1/template/sites-list" data-autoload="true"></span>
		</div>
	</div>
	<?php
	return;
}


if($user->locked == '1'){
	/*
?>
      <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
          <div class="account-wall">
            <div style="text-align: center; margin-bottom: 20px;"><img style="width:259px; height: 80px;" src="http://www.humble.co.za/images/humblelogo.png"></div>
            <div class="form-signin">
            <p class="text-center">Please sign in with your Cashier PIN</p>
              <span class="error-line"></span>
              <div class="row">
              	<div class="col-sm-3">
              		<input style="text-align:center" type="password" class="form-control cashier-pin trigger" data-event="keyup" data-before="unlock_checklength" data-callback="humble_unlock" data-load-element=".cashier-pin" data-call="lock_till" maxlength="1" size="1" required autofocus>
              	</div>
              	<div class="col-sm-3">
              		<input style="text-align:center" type="password" class="form-control cashier-pin trigger" data-event="keyup" data-before="unlock_checklength" data-callback="humble_unlock" data-load-element=".cashier-pin" data-call="lock_till" maxlength="1" size="1" required autofocus>
              	</div>
              	<div class="col-sm-3">
              		<input style="text-align:center" type="password" class="form-control cashier-pin trigger" data-event="keyup" data-before="unlock_checklength" data-callback="humble_unlock" data-load-element=".cashier-pin" data-call="lock_till" maxlength="1" size="1" required autofocus>
              	</div>
              	<div class="col-sm-3">
              		<input style="text-align:center" type="password" class="form-control cashier-pin trigger" data-event="keyup" data-before="unlock_checklength" data-callback="humble_unlock" data-load-element=".cashier-pin" data-call="lock_till" maxlength="1" size="1" required autofocus>
              	</div>
              </div>
            </div>
          </div>
          
        </div>
      </div>*/ ?>
      <script>
      //jQuery('body').addClass('login-body');
      //jQuery('.cashier-pin').eq(0).focus();
      window.location = 'http://dev.humble.co.za/signin.php';
      </script>
<?php
  return;
}


?>
<div class="hidden-xs" style="height: 100px;">
	
</div>
<div class="row" id="loader-panel">
	<div class="col-sm-4 col-md-4 col-sm-offset-4">
		<label>Loading humble</label>
		<div class="progress">
			<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
		</div>
		<div id="loaders">
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="template/till" data-cache-session="till_tmpl" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="template/inventory" data-cache-session="inventory_tmpl" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="template/settings" data-cache-purge="true" data-cache-session="settings_tmpl" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="template/cashup" data-cache-session="cashup_tmpl" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="template/reports" data-cache-session="reports_tmpl" data-callback="update_progress"></span>

			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="customers" data-cache-session="customers" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="suppliers" data-cache-session="suppliers" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="products" data-cache-session="products" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="ean" data-cache-session="eans" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="community" data-cache-session="community" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="users" data-cache-session="users" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="sites" data-cache-session="sites" data-callback="update_progress"></span>
			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="categories" data-cache-session="categories" data-callback="update_progress"></span>

			<span class="trigger" data-autoload="true" <?php echo $isclean; ?>data-call="app_sync" data-cache-session="appsync" data-callback="update_progress"></span>



		</div>
		<span id="load-dashboard" class="" data-autoload="true" data-call="dashboard" data-target="#main-panel"></span>
	</div>
</div>
<script type="text/javascript">

var loaders = jQuery('#loaders').children().length,
	partsize = 100/loaders,
	current = 0,
	bar = jQuery('.progress-bar');

function update_progress(obj){
	
	current += partsize;
	this.trigger.remove();
	bar.width(current + '%');

	if( Math.round(current) >= 100 ){
		jQuery('#loader-panel').slideUp(100);
		jQuery('#load-dashboard').addClass('trigger');
		baldrickTrigger();
	}
}

</script>