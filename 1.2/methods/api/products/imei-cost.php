<?php
/*

Caldoza Engine ------------------------

File	:	api/products/imei-cost.php
Created	: 	2013-12-23

*/



$cost = $db->get_row($db->prepare(
	"SELECT `productguid`,`cost` FROM `imeicost` WHERE `companyguid` = %s AND `imei` = %s", $user->cguid, $params['imei']));

if(empty($cost)){
	
	$cost = $db->get_row($db->prepare(
	"SELECT `guid` as `productguid`,`cost` FROM `products` WHERE `companyguid` = %s AND `guid` = %s", $user->cguid, $params['itemguid']));
	if(empty($cost)){	
		return array('message'=>'no imei / product found');
	}
}

$out['message'] = 'OK';
$out['cost'] = $cost->cost;
$out['productguid'] = $cost->productguid;
$out['imei'] = $params['imei'];

return $out;
