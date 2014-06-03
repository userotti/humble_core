<?php
/*

Caldoza Engine ------------------------

File	:	api/products/product-ean.php
Created	: 	2013-12-06

*/



$ean = $db->get_results($db->prepare("

SELECT
	`guid`,
	`productguid` AS `product_guid`,
	`ean`,
	`live`
FROM
	`ean`
WHERE
	`companyguid` = %s
AND
	`productguid` = %s
	", $user->cguid, $params['productguid']));

if(empty($ean)){
	return array('message'=>'no ean available');
}
$ean->message = 'OK';

return $ean;