<?php
/*

Caldoza Engine ------------------------

File	:	api/items/confirm.php
Created	: 	2013-12-04

*/

/*
ob_start();
dump($_POST,0);
$debug = ob_get_clean();
$db->insert('debugnotes', array('message'=>$debug));
*/

$cache_object = get_cached_object($params['syncguid']);

if(!empty($cache_object)){
	//console($cache_object);
	$compare = array();
	foreach ( $cache_object as $item){
		//$guid = $item['guid'];
		$guid = $item->guid;
		$compare[$guid] = "'".$guid."'";
	}
	

	$current = $db->get_results(
		"SELECT
		 `itemGUID`
		 FROM `confirmed`
		 WHERE
		 `itemGUID` IN (".implode(',', $compare).") AND 
		 `deviceGUID` = '".$user->deviceGUID."';
		 " );

	$existing = array();
	if(!empty($current)){
		foreach ($current as $existingitem) {
			$existing[] = $existingitem->itemGUID;
		}
	}
	foreach ( $compare as $itemGUID=>$clenead){
		// check if already exists
		if( !in_array( $itemGUID, $existing ) ){
			$db->insert('confirmed', array( 'itemGUID' => $itemGUID, 'deviceGUID' => $user->deviceGUID ) );
			//echo 'inserting '.$itemGUID.' <br>';
		}
	}
	delete_cached_object($params['syncguid']);
	return array( 'message' => 'OK' );
}
if($params['syncguid'] != 'item'){
	return array('message' => 'guid has expired');	
}
error_log('old confirm instance - reverting to legacy method. on file : '. basename( __FILE__) );



if(empty($_POST['item_guid'])){
	return array('message' => 'no item guid provided');
}
$compare = array();
foreach ( (array) $_POST['item_guid'] as $itemGUID){
	$compare[$itemGUID] = "'".$itemGUID."'";
}
$current = $db->get_results( $db->prepare(
	"SELECT
	 `itemGUID`
	 FROM `confirmed`
	 WHERE
	 `itemGUID` IN (".implode(',', $compare).") AND 
	 `deviceGUID` = %s;
	 ", $user->deviceGUID ) );

$existing = array();
if(!empty($current)){
	foreach ($current as $existingitem) {
		$existing[] = $existingitem->itemGUID;
	}
}
foreach ( $compare as $itemGUID=>$clenead){
	// check if already exists
	if( !in_array( $itemGUID, $existing ) ){
		$db->insert('confirmed', array( 'itemGUID' => $itemGUID, 'deviceGUID' => $user->deviceGUID ) );
	}
}
return array( 'message' => 'OK', 'confirmed' => array_keys($compare) );












