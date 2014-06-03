<?php
/*

Caldoza Engine ------------------------

File	:	api/community/get-suppliers.php
Created	: 	2013-12-04

*/


$suppliers = $db->get_results($db->prepare("

SELECT
	`guid`,
	`descr`,
	`communitytype`,
	`live`
FROM
	`community`
WHERE
	`companyguid` = %s
	AND
	`communitytype` = 1
	AND
    `guid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s);
	
	", $user->cguid, $user->deviceGUID));


if(empty($suppliers)){
	$out['message'] = 'No Suppliers Available';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($suppliers);
	$out['suppliers'] = $suppliers;	
} else {
	$out['message'] = 'OK';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($suppliers);
	$out['suppliers'] = $suppliers;
	$out['confirm_guid'] = set_cached_object($out['suppliers']);	
}
return $out;

