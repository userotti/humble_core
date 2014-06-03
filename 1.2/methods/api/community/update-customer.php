<?php
/*

Caldoza Engine ------------------------

File	:	api/community/update-customer.php
Created	: 	2013-12-17

Updates / creates a new customer

*/

$allowed = array(
	'descr',
	'communitytype',
	'pastelid',
);


if($params['guid'] == 'new'){
	// create

	$customer = array(
		'guid'					=>	gen_uuid(),
		'companyguid'			=>	$user->cguid,
		'live'					=>	1,
		'communitytype'			=>	0,
	);

	foreach($allowed as $field){
		if(isset($_POST[$field])){
			$customer[$field] = $_POST[$field];
		}
	}

	$db->insert('community', $customer);


}else{
	// update

	$customer = $db->get_row("SELECT * FROM `community` WHERE `guid` = '".$params['guid']."' AND `companyguid` = '".$user->cguid."';", ARRAY_A);

	foreach($allowed as $field){
		if(isset($_POST[$field])){
			$customer[$field] = $_POST[$field];
		}
	}

	$db->update('community', $customer, array('guid' => $params['guid'], 'companyguid' => $user->cguid ) );

}

$customer = $db->get_row("SELECT * FROM `community` WHERE `guid` = '".$customer['guid']."' AND `companyguid` = '".$user->cguid."';");
$customer->message = 'OK';

checkCustomers($user->siteguid);

return $customer;