<?php
/*

Caldoza Engine ------------------------

File	:	api/products/search-product.php
Created	: 	2014-05-03

*/


$selection = '*';

$filter = null;
if(!empty($_GET['q'])){
	$str = $_GET['q']; 
	$filter = " AND ( `stockcode` LIKE '%".$str."%' OR `descr` LIKE '%".$str."%' ) ";
}

$product = $db->get_row("

SELECT
	".$selection."
FROM
	`products`
WHERE
	`companyguid` = '".$user->cguid."'
".$filter."");

if(empty($product)){
	return array('message'=>'no products found');
}


return $product;