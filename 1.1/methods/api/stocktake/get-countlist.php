<?php

	$proceed = true;



	$siteGUID = $user->siteguid;
	$deviceGUID = $user->deviceGUID;

	$takeGUID = $db->get_var("select * from stocktake_log where siteguid = '".$siteGUID."' and enddate is null order by startdate desc");
	if (empty($takeGUID)) { 
		$proceed = false;
	}

	$result = array(
		'siteGUID'   => $siteGUID,
		'deviceGUID' => $deviceGUID,
		'takeGUID'   => $takeGUID,
	);

	$arr = array();
	$list = $db->get_results("select * from stocktake_counted where takeguid = '".$takeGUID."' and guid not in (select itemguid from confirmed where deviceguid = '".$deviceGUID."') order by insdate desc");
	foreach ($list as $row) {
		$arr[] = array(
			'guid'        => $row->guid,
			'datetime'    => $row->datetime,
			'siteguid'    => $row->siteguid,
			'deviceguid'  => $row->deviceguid,
			'devicename'  => $row->devicename,
			'userguid'    => $row->userguid,
			'productguid' => $row->productguid,
			'qty'         => $row->qty,
			'imei'        => $row->imei,
		);
	}
	$result['list'] = $arr;
	$result['confirm_guid'] = set_cached_object($result['list']);
	console($result);
	return $result;
?>