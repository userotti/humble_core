<?php
	$result = array(
		'message' => 'Got Call',
		'recipes' => array(),
	);

	$productguid = $params['productguid'];
	$result['productguid'] = $productguid;

	if (!empty($productguid)) {
		$arr = $db->get_results("select * from recipes where productguid = '".$productguid."'");
		$result['recipes'] = $arr;
	}
	$result['message'] = 'OK';
	return $result;
?>