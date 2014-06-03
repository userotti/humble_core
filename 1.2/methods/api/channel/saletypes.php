<?php

	$result = array(
		'message' => 'OK',
		'guid'    => $_POST['guid'],
	);

	$count = $db->get_var("select guid from saleTypes where guid = '".$_POST['guid']."'");
	if (empty($count)) {
		console("is not present");

		$ins = array(
			'guid' => $_POST->guid,

		);
		console($ins);

	} else {
		$upd = array(
			'channel'   => $params['channelguid'],
			'ord'       => $_POST['ord'],
			'title'     => $_POST['title'],
			'color'     => $_POST['color'],
			'askTariff' => $_POST['asktariff'],
			'askMSISDN' => $_POST['askmsisdn'],
			'askEmail'  => $_POST['askemail'],
			'live'      => $_POST['live'],
		);
		$db->update("saleTypes",$upd,array('guid' => $_POST['guid']));
	}
	return $result;

?>