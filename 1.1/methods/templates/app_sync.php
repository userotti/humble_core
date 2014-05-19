<?php


// App Sync - rebuilds app cache


?>


<div class="progress">
	<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
</div>

<div id="loaders">
	<span class="trigger" data-autoload="true" data-call="template/till" data-cache-purge="true" data-cache-session="till_tmpl" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="template/inventory" data-cache-purge="true" data-cache-session="inventory_tmpl" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="template/settings" data-cache-purge="true" data-cache-session="settings_tmpl" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="template/cashup" data-cache-purge="true" data-cache-session="cashup_tmpl" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="template/reports" data-cache-purge="true" data-cache-session="reports_tmpl" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="customers" data-cache-purge="true" data-cache-session="customers" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="suppliers" data-cache-purge="true" data-cache-session="suppliers" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="products" data-cache-purge="true" data-cache-session="products" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="ean" data-cache-purge="true" data-cache-session="eans" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="community" data-cache-purge="true" data-cache-session="community" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="users" data-cache-purge="true" data-cache-session="users" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="sites" data-cache-purge="true" data-cache-session="sites" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="app_sync" data-cache-purge="true" data-cache-session="appsync" data-callback="update_progress"></span>
	<span class="trigger" data-autoload="true" data-call="categories" data-cache-session="categories" data-cache-purge="true" data-callback="update_progress"></span>
	
</div>

<script type="text/javascript">

var loaders = jQuery('#loaders').children().length,
	partsize = 100/loaders,
	current = 0,
	vis = jQuery('#sync_baldrickModalLable'),
	bar = jQuery('.progress-bar');

function update_progress(obj){
	
	current += partsize;
	this.trigger.remove();
	bar.width(current + '%');
	vis.html( 'Cloud Sync ' + Math.round(current) + '%');



	if( Math.round(current) >= 100 ){
		jQuery('#loader-panel').slideUp(100);
		jQuery('#load-dashboard').addClass('trigger');
		setTimeout(function(){
			jQuery('#sync_baldrickModal').modal("hide");
		}, 500);
	}
}

</script>