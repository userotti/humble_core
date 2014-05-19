<?php

	$out['deviceGUID'] = $user->deviceGUID;

	if ($db->delete('confirmed', array('deviceGUID'=>$out['deviceGUID']))) {
		$out['message'] = 'OK';
	} else {
		$out['message'] = 'Nothing to clear OK';
	}
	return $out;
?>