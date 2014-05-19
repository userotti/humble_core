<?php
	$siteGUID = $user->siteguid;
	$deviceGUID = $user->deviceGUID;

	$ret = array(
		'message' => 'OK',
		'siteguid' => $siteGUID,
		'deviceguid' => $deviceGUID,
	);

	$tempSH = $db->get_results("select * from tempSH where siteguid = '".$siteGUID."' and guid not in (select itemguid from confirmed where deviceguid = '".$deviceGUID."')");
	if (!empty($tempSH)) {
		$ret['TempSH'] = $tempSH;
		$ret['TempSH_confirm_guid'] = set_cached_object($tempSH);
	}

	$tempSL = $db->get_results("select * from tempSL where siteguid = '".$siteGUID."' and guid not in (select itemguid from confirmed where deviceguid = '".$deviceGUID."')");
	if (!empty($tempSL)) {
		$ret['TempSL'] = $tempSL;
		$ret['TempSL_confirm_guid'] = set_cached_object($tempSL);
	}

	$tempBaskets = $db->get_results("select * from tempBaskets where siteguid = '".$siteGUID."' and guid not in (select itemguid from confirmed where deviceguid = '".$deviceGUID."')");
	if (!empty($tempBaskets)) {
		$ret['TempBaskets'] = $tempBaskets;
		$ret['TempBaskets_confirm_guid'] = set_cached_object($tempBaskets);
	}

	$basketNotes = $db->get_results("select * from basketnotes where siteguid = '".$siteGUID."' and guid not in (select itemguid from confirmed where deviceguid = '".$deviceGUID."')");
	if (!empty($basketNotes)) {
		$ret['BasketNotes'] = $basketNotes;
		$ret['BasketNotes_confirm_guid'] = set_cached_object($basketNotes);
	}

	return $ret;
?>