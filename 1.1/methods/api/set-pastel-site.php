<?php
/*

Caldoza Engine ------------------------

File	:	api/set-site.php
Created	: 	2013-12-19

*/

console($_POST);
$pastelcompany = $_POST['pastelcompany'];
$pastelhash = $_POST['pastelhash'];


console("$pastelcompany | $pastelhash");



if($db->update('sites', array('pastelid'=>$params['pastelid'], 'pastelcompanyname' => $pastelcompany, 'pastelhash' => $pastelhash), array('guid'=>$params['siteguid']))){
	return array('message' => 'OK');
}else{
	return array('message' => 'nothing to change');
}


