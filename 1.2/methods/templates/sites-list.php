<?php
/*

Caldoza Engine ------------------------

File	:	templates/sites-list.php
Created	: 	2014-01-13

*/




?>
<div class="panel panel-primary">
	<div class="panel-heading">
		Select Site
	</div>
	<ul class="list-group">
		{{#each sites}}
			<a style="cursor:pointer;" class="list-group-item trigger" data-call="setsite/{{guid}}" data-guid="{{guid}}" data-method="POST" data-callback="dosomething" data-animate="true" data-active-class="success" >{{site_name}}</a>
		{{/each}}
	</ul>
</div>

<script type="text/javascript">
	function dosomething(obj){
		if(obj.data.message === 'OK'){			
			jQuery.cookie('siteguid', obj.params.trigger.data('guid'), {expires: 365, path: '/', domain: 'humble.co.za'});
		}
		//jQuery('#site-select_baldrickModal').modal('hide');
		jQuery('#load-dashboard').trigger('click');
	}
</script>