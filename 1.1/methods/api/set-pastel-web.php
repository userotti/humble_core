<?php
/*

Caldoza Engine ------------------------

File	:	api/set-site.php
Created	: 	2013-12-19

*/



if(!empty($_POST['pastelid']) && !empty($_POST['company'])){

	$update = array('pastelid'=>$_POST['pastelid'], 'pastelcompanyname' => $_POST['company']);

	if(!empty($_POST['hash'])){
		$update['pastelhash'] = $_POST['hash'];
	}

	$db->update('sites', $update, array('guid'=>$user->siteguid));
	
	return array('message' => 'OK');


}

return array('error' => 'Missing some data are we?');
