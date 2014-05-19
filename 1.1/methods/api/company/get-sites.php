<?php
/*

Caldoza Engine ------------------------

File	:	api/company/get-sites.php
Created	: 	2013-12-04

*/


$sites = $db->get_results($db->prepare("

SELECT
	`guid`,
	`sitename` AS `site_name`,
	`address1` AS `address_line1`,
	`address2` AS `address_line2`,
	`addr3` AS `address_line3`,
	`fax`,
	`email`,
	`tel`,
	`vatnr` AS `vat_number`,
	`live`,
	`regnr`,
	`slipline1`,
	`slipline2`,
	`slipline3`,
	`pastelid`,
	`pastelcompanyname`,
	`countrycode`,
	`usesnapscan`,
	`snapscanmerchantid`
FROM
	`sites`
WHERE
	`coguid` = %s
	AND
    `guid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s)

	ORDER BY `site_name` ASC
	;
	", $user->cguid, $user->deviceGUID));


if(empty($sites)){
	$out['message'] = 'No Sites Available';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($sites);
	$out['sites'] = $sites;	
	//return array('message'=>'no sites available');

} else {
	$out['message'] = 'OK';
	$out['confirm_guid'] = null;
	$out['version'] = '1.1';
	$out['total'] = count($sites);
	$out['sites'] = $sites;
	$out['confirm_guid'] = set_cached_object($out['sites']);	
}
return $out;


/*return array(
	'message'	=>	'OK',
	'sites'		=>	$sites
	'confirm_guid' => set_cached_object($out['products']);
);*/

?>