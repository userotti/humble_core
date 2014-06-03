<?php
/*

Caldoza Engine ------------------------

File	:	api/community/update-supplier.php
Created	: 	2013-12-17

Updates / creates a new supplier

*/

$allowed = array(
	'descr',
	'communitytype',
	'pastelid',
);


if($params['guid'] == 'new'){
	// create

	$supplier = array(
		'guid'					=>	gen_uuid(),
		'companyguid'			=>	$user->cguid,
		'live'					=>	1,
		'communitytype'			=>	1,
	);

	foreach($allowed as $field){
		if(isset($_POST[$field])){
			$supplier[$field] = $_POST[$field];
		}
	}

	$db->insert('community', $supplier);


}else{
	// update

	$supplier = $db->get_row("SELECT * FROM `community` WHERE `guid` = '".$params['guid']."' AND `companyguid` = '".$user->cguid."';", ARRAY_A);

	foreach($allowed as $field){
		if(isset($_POST[$field])){
			$supplier[$field] = $_POST[$field];
		}
	}

	$db->update('community', $supplier, array('guid' => $params['guid'], 'companyguid' => $user->cguid ) );

}

$supplier = $db->get_row("SELECT * FROM `community` WHERE `guid` = '".$supplier['guid']."' AND `companyguid` = '".$user->cguid."';");
$supplier->message = 'OK';

checkSuppliers($user->siteguid);

return $supplier;