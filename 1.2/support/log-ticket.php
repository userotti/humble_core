<?php
/*

Caldoza Engine ------------------------

File	:	support/log-ticket.php
Created	: 	2014-01-15

*/


$newTicket = array(
	'guid'	=> gen_uuid(),
	'name'	=> $_POST['name'],
	'email'	=> $_POST['email'],
	'contactnr'	=> $_POST['contactnr'],
	'issue'	=> $_POST['issue']
);

$db->insert('tickets', $newTicket);

$newTicket['message'] = 'OK';

//return $newTicket;

?><div style="display: block;" class="modal hide in" id="modal_support" aria-hidden="false">
    	<div class="modal-header">
    		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
    		<h3 id="modal_support_title">Thank you. <?php echo $_POST['name']; ?></h3>
    	</div>
    	<div style="max-height:800px" class="modal-body">
    	<p>Your support ticket has been logged. We will contact you shortly.</p>
    	</div>
    	<div class="modal-footer" id="modal_support_footer">
    		<button aria-hidden="true" data-dismiss="modal" class="btn btn-success" type="button">Close</button>
    	</div>
    </div>