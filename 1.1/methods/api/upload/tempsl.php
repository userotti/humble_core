<?php

	$guid = $params['guid'];
	$table = "tempSL";
	$fields = array(
		"guid",
		"line",
		"siteguid",
		"saleguid",
		"basketguid",
		"basketline",
		"productguid",
		"productdescr",
		"qty",
		"serial",
		"unitcost",
		"unitsell",
		"unitdisc",
		"unitvat",
		"unitrebate",
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