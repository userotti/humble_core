<?php
	$heartbeat = json_decode($_POST['heartbeat']); 
	$ins = array(
		'guid' => gen_uuid(),
	);
	foreach ($heartbeat as $key => $value) {
		$ins[$key] = $value;
	}
	$sitename = $db->get_var("select sitename from sites where guid = '".$ins['siteguid']."'");
	$channelguid = $db->get_var("select channelguid from companies where guid = '".$ins['companyguid']."'");
	$ins['sitename'] = $sitename;
	$db->insert("heartbeats",$ins);
	
	$result = array(
		'message'    => "OK",
		'deviceguid' => $user->deviceGUID,
	);

	$tbls = array(
		'categories',
		'community',
 		'ean',
 		'products',
 		'saleTypes',
 		'sites',
 		'tariffs',
 		'users',
	);

	foreach ($tbls as $tbl) {
		$guidcol = 'guid';
		$companycol = 'companyguid';
		$companyval = $heartbeat->companyguid;
		if ($tbl == 'users') { 
			$guidcol = 'uguid';
			$companycol = 'cguid'; 
		}
		if ($tbl == 'sites') { 
			$companycol = 'coguid'; 
		}
		if ($tbl == 'saleTypes') {
			$companycol = 'channel';
			$companyval = $channelguid;
		}
		if ($tbl == 'tariffs') {
			$companycol = 'channelguid';
			$companyval = $channelguid;
		}

		$qty = $db->get_var("select count(*) from ".$tbl." where ".$companycol." = '".$heartbeat->companyguid."' and ".$guidcol." not in (select itemguid from confirmed where deviceguid = '".$heartbeat->deviceguid."')");
		$result[$tbl] = $qty;
	}

	$full_reset = $db->get_var("select fullreset from login where deviceguid = '".$user->deviceGUID."' order by insdate desc");
	if ($full_reset == 1) {
		$db->update("login",array('fullreset' => 0),array('deviceguid' => $result['deviceguid']));
	}
	$result['full_reset'] = $full_reset;

	return $result;
?>