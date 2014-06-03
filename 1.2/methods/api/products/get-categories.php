<?php
/*

Caldoza Engine ------------------------

File	:	api/products/get-categories.php
Created	: 	2013-12-04

*/


$categories = $db->get_results($db->prepare("

SELECT
    `guid`,
    `cat`,
    `category`,
    `live`
FROM
	`categories`
WHERE
	`companyguid` = %s
	AND
    `guid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s)

	", $user->cguid, $user->deviceGUID));


if(empty($categories)){
	$out['message'] = 'No Categories Available';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($categories);
	$out['categories'] = $categories;	
} else {
	$out['message'] = 'OK';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($categories);
	$out['categories'] = $categories;
	$out['confirm_guid'] = set_cached_object($out['categories']);	
}
return $out;

/*if(empty($categories)){
	return array('message'=>'no categories available');
}
$out['message'] = 'OK';
$out['total'] = count($categories);
$out['categories'] = $categories;*/
return $out;