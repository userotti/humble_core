<?php
/*

Caldoza Engine ------------------------

File	:	api/set-site.php
Created	: 	2013-12-19

*/

$params['pastelcompanyname'] = str_replace('%20', ' ', $params['pastelcompanyname']);


if($db->update('sites', array('pastelcompanyname'=>$params['pastelcompanyname']), array('guid'=>$params['siteguid']))){
	return array('message' => 'OK');
}else{
	return array('message' => 'nothing to change');
}


