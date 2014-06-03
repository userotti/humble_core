<?php
	$db->update("mh",array('movestate' => 2),array('guid' => $params['moveguid']));
	return array('message' => 'OK');
?>