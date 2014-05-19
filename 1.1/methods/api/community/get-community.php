<?php
/*

Caldoza Engine ------------------------

File	:	api/community/get-community.php
Created	: 	2013-12-04

*/


$community = $db->get_results($db->prepare("

SELECT
	`guid`,
	`descr` AS `description`,
	`communitytype` AS `community_type`,
	`live`
FROM
	`community`
WHERE
	`companyguid` = %s
	AND
    `guid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s);
	
	", $user->cguid, $user->deviceGUID));


if(empty($community)){
	$out['message'] = 'No Community Available';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($community);
	$out['community'] = $community;	
} else {
	$out['message'] = 'OK';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($community);
	$out['community'] = $community;
	$out['confirm_guid'] = set_cached_object($out['community']);	
}
return $out;


/*if(empty($community)){
	return array('message'=>'no community available');
}
$out['message'] = 'OK';
$out['total'] = count($community);
$out['community'] = $community;*/
return $out;