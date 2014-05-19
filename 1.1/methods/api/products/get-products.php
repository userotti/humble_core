<?php
/*

Caldoza Engine ------------------------

File	:	api/products/get-products.php
Created	: 	2013-12-04

*/

if(empty($user->siteguid)){
	//return array('message' => 'no site set');
}


$selection = '*';

if(!empty($_GET['summary'])){
	$selection = "
	`guid`,
	`companyguid`,
	`stockcode`,
	`descr`,
	`sell`
	";
}

$filter = null;
$limit = null;
if(!empty($_GET['call'])){
	// ADD LIMIT for web use.
	$limit = " LIMIT 500 ";
}
if(!empty($user->cguid)){
	$filter = $db->prepare( "	,(SELECT
		SUM(`qty`*`movedir`) AS `total`
	FROM `movement`
	WHERE
		`productguid` = `products`.`guid`
	AND
		`siteguid` = %s
	) AS `on_hand` ", $user->siteguid);
}

$products = $db->get_results($db->prepare("

SELECT
	".$selection."
	".$filter."
FROM
	`products`
WHERE
	`companyguid` = %s
AND `guid` NOT IN (SELECT `itemGUID` FROM `confirmed` WHERE `deviceGUID` = %s)

ORDER BY `weight` DESC
" . $limit . "

	", $user->cguid,  $user->deviceGUID));

if(empty($products)){
	$log = array(
		'guid'       => gen_uuid(),
		'siteguid'   => $user->siteguid,
		'deviceguid' => $user->deviceGUID,
		'synctype'   => 'get-products.php',
		'message'    => 'No Products',
	);
	$db->insert("cloudsync",$log);
	return array('message'=>'no products available');
}
$stock_total = 0;
foreach($products as &$product){
	$product->on_hand = (int) $product->on_hand;
	$stock_total += $product->on_hand;
}
$out['message'] = 'OK';
$out['confirm_guid'] = null;
$out['version'] = '1.1';
$out['products'] = $products;
$out['total'] = count($products);
$out['on_hand_total'] = $stock_total;
$out['confirm_guid'] = set_cached_object($out['products']);


$log = array(
	'guid'       => gen_uuid(),
	'siteguid'   => $user->siteguid,
	'deviceguid' => $user->deviceGUID,
	'synctype'   => 'get-products.php',
	'message'    => 'Returned Products',
);
$db->insert("cloudsync",$log);




return $out;






