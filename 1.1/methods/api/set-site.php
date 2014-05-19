<?php
/*

Caldoza Engine ------------------------

File	:	api/set-site.php
Created	: 	2013-12-19

*/

if($db->update('login', array('siteguid'=>$params['siteguid']), array('tokenGUID'=>$params['token']))){
	return array('message' => 'OK');
}else{
	return array('message' => 'nothing to change');
}


?>