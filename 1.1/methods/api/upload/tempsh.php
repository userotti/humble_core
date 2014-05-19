<?php

	
	


	//date_default_timezone_set(timezone_name_from_abbr("CST"));

	

	$guid = $params['guid'];
	$table = "tempSH";
	$fields = array(
		"descr",
		"sdate",
		"siteguid",
		"cashierguid",
		"completed",
		"live",
	);
	$arr = array( 
		"guid" => $guid,
	);
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$arr[$field] = $_POST[$field];
		}
	}
	$db->replace($table,$arr);

	

	$db->delete('confirmed',array('itemguid' => $guid));

	

	return array('message'=>'OK', 'guid'=>$params['guid']);
?>