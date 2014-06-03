<?php

	
	$base_audit = array();
	if(!empty($params['token'])){
		$base_audit = array(
			'guid'			=>	gen_uuid(),
			'datetime'		=>	date('Y-m-d H:i:s'),
			'siteguid'		=>	$user->siteguid,
			'deviceguid'	=>	$user->deviceGUID,
			'cashierguid'	=>	$user->uguid,
			'note'			=>	''
		);
	}
	$log = array_merge($base_audit, $_POST);
	$rows = $db->get_var("select guid from audit where guid = '".$log['guid']."'");
	if (count($rows) == 0) {
		$db->insert('audit', $log);
	}
	return array('message' => 'OK', 'guid' => $log['guid']);

?>