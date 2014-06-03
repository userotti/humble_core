<?php
/*

Caldoza Engine ------------------------

File	:	api/company/get-site.php
Created	: 	2013-12-04

*/

$site = $db->get_row($db->prepare("

SELECT
	`guid`,
	`sitename`,
	`sitename` AS `site_name`,
	`address1` AS `address_line1`,
	`address2` AS `address_line2`,
	`addr3` AS `address_line3`,
	`sitename`,
	`address1`,
	`address2`,
	`addr3`,
	`fax`,
	`email`,
	`tel`,
	`vatnr`,
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
	`guid` = %s
		
	", $user->cguid, $params['siteguid']), ARRAY_A);

if(empty($site)){
	return array('message'=>'invalid site guid');
}
$site['message'] = 'OK';

return $site;