<?php
/*

Caldoza Engine ------------------------

File	:	api/tariffs/get-tariffs.php
Created	: 	2013-12-04

*/

$tariffs = $db->get_results($db->prepare("

SELECT
	`guid`,
	`tariffdescr` as `tariff_description`,
	`weight`,
	`category`,
	`subs`,
	`live`
FROM
	`tariffs`
WHERE
	`channelguid` = %s
	AND
    `guid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s);

	", $params['channel'], $user->deviceGUID));

if(empty($tariffs)){
	$out['message'] = 'No Tariffs Available';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($tariffs);
	$out['tariffs'] = $tariffs;	
} else {
	$out['message'] = 'OK';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($tariffs);
	$out['tariffs'] = $tariffs;
	$out['confirm_guid'] = set_cached_object($out['tariffs']);	
}
return $out;


/*if(empty($tariffs)){
	return array('message'=>'no tariffs available');
}
$out['message'] = 'OK';
$out['total'] = count($tariffs);
$out['tariffs'] = $tariffs;*/
return $out;