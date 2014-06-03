<?php
/*

Caldoza Engine ------------------------

File	:	api/channel/get-channel.php
Created	: 	2013-12-04

*/


$sale_types = $db->get_results($db->prepare("
SELECT
	`guid`,
	`ord`,
	`title`,
	`descr`,
	`color`,
	`asktariff`,
	`askmsisdn`,
	`askemail`,
	`live`
FROM
	`saleTypes`
WHERE
	`channel` = %s
	AND
    `guid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s)

	;", $params['channelguid'], $user->deviceGUID));

if(empty($sale_types)){
	$out['message'] = 'No Sale Types Available';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($sale_types);
	$out['sale_types'] = $sale_types;	
} else {
	$out['message'] = 'OK';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($sale_types);
	$out['sale_types'] = $sale_types;
	$out['confirm_guid'] = set_cached_object($out['sale_types']);	
}
return $out;

/*if(empty($sale_types)){
	return array('message'=>'no sale types found');
}
$out['message'] = 'OK';
$out['sale_types'] = $sale_types;*/
return $out;

