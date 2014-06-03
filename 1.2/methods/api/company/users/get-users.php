<?php
/*

Caldoza Engine ------------------------

File	:	api/company/users/get-users.php
Created	: 	2013-12-04

*/

$users = $db->get_results($db->prepare("

SELECT
    `uguid` AS `guid`,
    `fname`,
    `sname`,
    `fname` AS `first_name`,
    `sname` AS `last_name`,
    `email`,
    `pword`,
    `cashiercode` AS `cashier_code`,
    `cashierpin`,
    `cashierpin` AS `cashier_pin`,
    `basket`,
    `move`,
    `reports`,
    `users`,
    `community`,
    `products`,
    `general`,
    `live`

FROM
	`users`
WHERE
	`cguid` = %s
    AND
    `uguid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s)
	
	", $user->cguid, $user->deviceGUID));




if(empty($users)){
    $out['message'] = 'No Users Available';
    $out['confirm_guid'] = null;
    $out['version'] = '1.1';
    $out['total'] = count($users);
    $out['users'] = $users; 
} else {
    $out['message'] = 'OK';
    $out['confirm_guid'] = null;
    $out['version'] = '1.1';
    $out['total'] = count($users);
    $out['users'] = $users;
    $out['confirm_guid'] = set_cached_object($out['users']);    
}
return $out;


?>