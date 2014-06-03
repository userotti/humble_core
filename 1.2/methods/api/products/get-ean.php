<?php
/*

Caldoza Engine ------------------------

File	:	api/products/get-ean.php
Created	: 	2013-12-04

*/

$ean = $db->get_results($db->prepare("

SELECT
	`guid`,
	`productguid`,
	`productguid` AS `product_guid`,
	`ean`,
	`live`
FROM
	`ean`
WHERE
	`companyguid` = %s
AND
	`guid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s) limit 50
	", $user->cguid, $user->deviceGUID));

if(empty($ean)){
	return array('message'=>'no ean available');
}

foreach($ean as $key=>&$set){
	if($set->live == '0'){
		$ean[$key]->disabled = 'true';
	}
}

$out['message'] = 'OK';
$out['confirm_guid'] = null;
$out['total'] = count($ean);
$out['ean'] = $ean;
$out['confirm_guid'] = set_cached_object($out['ean']);

return $out;