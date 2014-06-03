<?php
	$guid = $params['guid'];
	$table = "tempBaskets";
	$fields = array(
		"guid",
		"siteguid",
		"saleguid",
		"ord",
		"saletypeguid",
		"tariffguid",
		"msisdn",
		"email",
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
	$db->delete($table,array('guid' => $guid));
	$db->replace($table,$arr);
	$db->delete('confirmed',array('itemguid' => $guid));
	
	return array('message'=>'OK', 'guid'=>$params['guid']);
?>