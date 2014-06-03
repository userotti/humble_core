<?php
/*

Caldoza Engine ------------------------

File	:	api/upload/cashup.php
Created	: 	2013-12-18

*/



/// cleanups
$data = $_POST;

if(empty($data['guid'])){
	$data['guid'] = gen_uuid();
}
if(empty($data['siteguid'])){
	$data['siteguid'] = $user->siteguid;
}
if(empty($data['datetime'])){
	$data['datetime'] = date('Y-m-d H:i:s');
}
if(empty($data['cashierguid'])){
	$data['cashierguid'] = $user->userGUID;
}
if(empty($data['deviceguid'])){
	$data['deviceguid'] = $user->deviceGUID;
}
if(empty($data['devicename'])){
	$data['devicename'] = $user->deviceName;
}




if(!$db->insert('cashups', $data)){
	/*if(!empty($_POST)){
		ob_start();
		dump($db,0);
		$debug = ob_get_clean();
		$db->insert('debugnotes', array('message'=>$debug));
	}*/
	
	return array('message'=>'OK', 'guid'=>$data['guid']);//$db->last_error);
}

return array('message'=>'OK', 'guid'=>$data['guid']);