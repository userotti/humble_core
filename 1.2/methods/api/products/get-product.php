<?php
/*

Caldoza Engine ------------------------

File	:	api/products/get-product.php
Created	: 	2013-12-06

*/


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
$product = $db->get_row($db->prepare("

SELECT
	".$selection."
FROM
	`products`
WHERE
	`companyguid` = %s
AND `guid` = %s
	", $user->cguid,  $params['productguid']));

if(empty($product)){
	return array('message'=>'invalid product');
}
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

// get product category
$product->category = $db->get_var("SELECT `category` FROM `categories` WHERE `cat` = ".$product->cat." AND `companyguid` = '".$user->cguid."';");
$product->product_type = $db->get_var("SELECT `productdescr` FROM `producttypes` WHERE `producttype` = ".$product->producttype.";");

$product->message = 'OK';
if(!empty($ean)){
	$product->ean = $ean;
}
return $product;