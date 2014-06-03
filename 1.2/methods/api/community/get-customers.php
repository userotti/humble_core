<?php
/*

Caldoza Engine ------------------------

File	:	api/community/get-customers.php
Created	: 	2013-12-04

*/


$customers = $db->get_results($db->prepare("

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
	`communitytype` = 0
	AND
    `guid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s);
	
	", $user->cguid, $user->deviceGUID));


if(empty($customers)){
	$out['message'] = 'No Customers Available';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($customers);
	$out['customers'] = $customers;	
} else {
	$out['message'] = 'OK';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($customers);
	$out['customers'] = $customers;
	$out['confirm_guid'] = set_cached_object($out['customers']);	
}
return $out;

