<?php
/*

Caldoza Engine ------------------------

File	:	api/company/get-company.php
Created	: 	2013-12-04

*/

$company = $db->get_row($db->prepare("

SELECT
	`company` AS `company_name`,
	`channelguid` AS `channel_guid`,
	`tradingas` AS `trading_as`,
	`vatnr` AS `vat_number`,
	taxprompt
FROM
	`companies`
WHERE
	
	`guid` = %s;
	
	
	", $params['companyguid']));

if(empty($company)){
	return array('message' => '');
}
$out['message'] = 'OK';
$out['company'] = $company;

return $out;

