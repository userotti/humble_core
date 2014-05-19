<?php
/*

Caldoza Engine ------------------------

File	:	api/reports/pdf.php
Created	: 	2014-03-27

*/

//console( $_GET );
//console( $params );
if(count($_GET) === 1){
	$key = array_keys( $_GET );
	parse_str( $key[0], $_GET);
}
// GLOBALS
if(!empty($_GET['siteguid'])){
	$siteguid = $_GET['siteguid'];
}else{
	$siteguid = $user->siteguid;
}

$site = $db->get_row( $db->prepare( "SELECT * FROM `sites` WHERE `guid` = %s;", $siteguid));
$company = $db->get_row( $db->prepare( "SELECT * FROM `companies` WHERE `guid` = %s;", $site->coguid));
$channel = $db->get_row( $db->prepare( "SELECT * FROM `channels` WHERE `guid` = %s;", $company->channelguid));
$presaletypes = $db->get_results( $db->prepare( "SELECT * FROM `saleTypes` WHERE `channel` = %s;", $company->channelguid));
foreach($presaletypes as $saletype){
	$saletypes[$saletype->guid] = $saletype;
}
$preproducts = $db->get_results( $db->prepare( "SELECT * FROM `products` WHERE `companyguid` = %s;", $company->guid));
foreach($preproducts as $product){
	$products[$product->guid] = $product;
	$altproducts[$product->guid] = $product;
}
$precategories = $db->get_results( $db->prepare( "SELECT * FROM `categories` WHERE `companyguid` = %s;", $company->guid));
foreach($precategories as $category){
	$categories[$category->cat] = $category;
}
$preusers = $db->get_results( $db->prepare( "SELECT * FROM `users` WHERE `cguid` = %s;", $company->guid));
foreach($preusers as $staff){		
	$users[$staff->uguid] = $staff;
	$altusers[$staff->uguid] = $staff;
}


if(!empty($_GET['accguid'])){
	$supplier = $db->get_var( $db->prepare( "SELECT `descr` FROM `community` WHERE `guid` = %s AND `companyguid` = %s; ", $_GET['accguid'], $user->cguid ));
	$presites = $db->get_results( $db->prepare( "SELECT * FROM `sites` WHERE `coguid` = %s;", $user->cguid ) );
	foreach($presites as $site){
		$sites[$site->guid] = $site;
	}
}

// Get movement types 
$premove = $db->get_results("SELECT * FROM `movetypes`");
foreach($premove as $movement){
	$movetypes[$movement->movecode] = $movement;
}

//dump($categories);
if(!empty($_GET['start_date'])){
	$startdate = $_GET['start_date'];
}
if(!empty($_GET['end_date'])){
	$enddate = $_GET['end_date'];
}

if($params['type'] == 'difference-report'){

	$data = include dirname(dirname(__FILE__)) . '/stocktake/get-stocktake.php';

	include '1.1/methods/report-templates/difference-report.php';
	return;
}

$data = include dirname(__FILE__) . '/audit.php';
$users = $altusers;

include '1.1/methods/report-templates/'.$_GET['template'].'.php';


//dump($_GET);


//return $data;
//return 'URL to PDF';