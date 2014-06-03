<?php

	//console($_POST);

	$payload = json_decode($_POST['payload']);
	$status = $payload->status;

	if ($status == 'completed') {
		$ins = array(
			'guid' => gen_uuid(),
			'siteguid' => $params['siteguid'],
			'id' => $payload->id,
			'auth_code' => $payload->auth_code,
			'required_amount' => $payload->required_amount,
			'timestamp' => $payload->timestamp,
			'status' => $payload->status,
			'total_amount' => $payload->total_amount,
		);
		$db->insert("snapscanpayments",$ins);
	}	
	return array("message","OK");
?>