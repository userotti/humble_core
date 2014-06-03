<?php

	

	$productguid = $params['productguid'];
	$ingredient = $_POST['ingredient'];
	$setType = $_POST['ingredienttype'];
	$result = array(
		'message'     => "OK",
		'setType'     => $setType,
		'productguid' => $productguid,
	);	
	$fields = array(
		'guid',
		'productguid',
		'ingredientguid',
		'ingredientqty',
		'ingredientunit',
	);
	$obj = json_decode($ingredient);
	$arr = array();
	foreach ($fields as $field) {
		if (isset($obj->$field)) {
			$arr[$field] = $obj->$field;
		}
	}
	
	
	if ($setType == "add") {
		$ret = $db->insert("recipes",$arr);
		if ($ret) {
			$result['message'] = "OK";
		} else {
			$result['message'] = "Could Not Insert";
		}
	} elseif ($setType == "del") {
		$ret = $db->delete('recipes',array('guid' => $arr['guid']));
		if ($ret) {
			$result['message'] = "OK";
		} else {
			$result['message'] = "Could Not Delete";
		}
	}
	return $result;
?>