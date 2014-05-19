<?php
/*

Caldoza Engine ------------------------

File	:	api/upload/cashup.php
Created	: 	2013-12-18

*/

$payout = array(
	'guid'			=>	gen_uuid(),
	'datetime'		=>	date('Y-m-d H:i:s'),
	'cashierguid'	=>	$user->uguid,
	'siteguid'		=>	$user->siteguid,
	'deviceguid'	=>	$user->deviceGUID,
	'devicename'	=>	$user->deviceName	
);

$payout = array_merge($payout, $_POST);

if(!$db->insert('payouts', $payout)){
	/*if(!empty($_POST)){
		ob_start();
		dump($db,0);
		$debug = ob_get_clean();
		$db->insert('debugnotes', array('message'=>$debug));
	}*/
	
	return array('message'=>'ERROR', 'guid'=>$payout['guid']);//$db->last_error);
}

return array('message'=>'OK', 'guid'=>$payout['guid'], "pop" => chr(27)."p07Q");