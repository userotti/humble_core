<?php

	

	$fields = array(
		'email',
		'userguid',
		'fname',
		'sname',
		'cellnr',
		'pword',
		'companyguid',
		'companyname',
		'channelguid',
		'siteguid',
		'sitename',
		'storetype',
		'packageguid',
		'live',
		'message',
	);

	foreach ($fields as $field) {
		if (!empty($_POST[$field])) {
			if ($_POST[$field] == "null" || strtoupper($_POST[$field]) == 'NULL') {
				$_POST[$field] = '';
			}
		} else {
			$_POST[$field] = '';
		}
	}

	$_POST['email'] = strtoupper($_POST['email']);
	if ($_POST['email'] == "" || $_POST['email'] == 'NULL' || empty($_POST['email'])) {

	}

	console($_POST);
	
	$result = array(
		'email'       => strtoupper($_POST['email']),
		'userguid'    => $_POST['userguid'],
		'fname'       => $_POST['fname'],
		'sname'       => $_POST['sname'],
		'cellnr'      => $_POST['cellnr'],
		'pword'       => $_POST['pword'],
		'companyguid' => $_POST['companyguid'],
		'companyname' => $_POST['companyname'],
		'channelguid' => $_POST['channelguid'],
		'siteguid'    => $_POST['siteguid'],
		'sitename'    => $_POST['sitename'],
		'storetype'   => $_POST['storetype'],
		'packageguid' => $_POST['packageguid'],
		'live'        => $_POST['live'],
		'message'     => 'Not OK',
	);

	$createUser = false;
	$createCompany = false;
	$createSite = false;

	if (empty($result['userguid'])) {
		$exist = $db->get_var("select uguid from users where email = '".$result['email']."'");
		if (empty($exist)) {
			$createUser = true;
			$result['live'] = -1;
		} else {
			$usr = $db->get_row("select * from users where uguid = '".$exist."'");
			$result['userguid'] = $usr->uguid;
			$result['live'] = $usr->live;
		}
	} else {
		$usr = $db->get_row("select * from users where uguid = '".$result['userguid']."'");
		if (empty($usr)) {
			$createUser = true;
			$result['live'] = -1;
		} else {
			if ($result['email'] != $usr->email && !empty($result['email'])) {
				$usr = $db->get_row("select * from users where email = '".$result['email']."'");
				if ($usr) { 
					$result['userguid'] = $usr->uguid;
					$result['live'] = $usr->live;
				} else {
					$createUser = true;
					$result['userguid'] = '';
					$result['live'] = -1;
				}
			} else {
				$result['userguid'] = $usr->uguid;
				$result['live'] = $usr->live;	
			}
			
		}
	}

	if ($result['live'] == -1 && $createUser && !empty($result['email'])) {
		$userguid = gen_uuid();
		$companyguid = gen_uuid();
		$siteguid = gen_uuid();
		$channelguid = 'aced8d4e-5b2b-11e3-8696-005056a5104a';

		$company = array(
			'guid'        => $companyguid,
			'company'     => '',
			'channelguid' => $channelguid,
			'tradingas'   => '',
			'packageguid' => '',
			'masteruser'  => $userguid,
		);
		$db->insert("companies",$company);
		$site = array(
			'guid'      => $siteguid,
			'coguid'    => $companyguid,
			'sitename'  => '',
			'storetype' => '',
		);
		$db->insert("sites",$site);
		$usr = array(
			'uguid'      => $userguid,
			'cguid'      => $companyguid,
			'fname'      => '',
			'sname'      => '',
			'email'      => $result['email'],
			'pword'      => rand(100000,999999),
			'cashierpin' => rand(1000,9999),
			'cellnr'     => '',
			'lastsite'   => $siteguid,
			'live'       => -1,
		);
		$db->insert("users",$usr);

		$result['userguid'] = $userguid;
	}

	if ($result['live'] == -1 && $createUser == false && !empty($result['email']) && !empty($result['userguid'])) {
		if (!empty($result['fname'])) {
			$db->update("users",array('fname' => $result['fname']),array('uguid' => $result['userguid']));
		}
		if (!empty($result['sname'])) {
			$db->update("users",array('sname' => $result['sname']),array('uguid' => $result['userguid']));
		}
		if (!empty($result['pword'])) {
			$db->update("users",array('pword' => $result['pword']),array('uguid' => $result['userguid']));
		}
		if (!empty($result['cellnr'])) {
			$db->update("users",array('cellnr' => $result['cellnr']),array('uguid' => $result['userguid']));
		}
		if (!empty($result['email'])) {
			$db->update("users",array('email' => $result['email']),array('uguid' => $result['userguid']));
		}
		if (!empty($result['companyname'])) {
			$db->update("companies",array('company' => $result['companyname']),array('guid' => $result['companyguid']));
			$db->update("companies",array('tradingas' => $result['companyname']),array('guid' => $result['companyguid']));
		}
		if (!empty($result['packageguid'])) {
			$db->update("companies",array('packageguid' => $result['packageguid']),array('guid' => $result['companyguid']));
		}
		if (!empty($result['sitename'])) {
			$db->update("sites",array('sitename' => $result['sitename']),array('guid' => $result['siteguid']));
		}
		if (!empty($result['storetype'])) {
			$db->update("sites",array('storetype' => $result['storetype']),array('guid' => $result['siteguid']));
		}
	}

	if (!empty($result['userguid']) && $result['live'] == -1) {
		$usr = $db->get_row("select * from users where uguid = '".$result['userguid']."'");
		if (!empty($usr)) {
			$result['fname'] = $usr->fname;
			$result['sname'] = $usr->sname;
			$result['cellnr'] = $usr->cellnr;
			$result['companyguid'] = $usr->cguid;
			$result['companyname'] = $db->get_var("select company from companies where guid = '".$result['companyguid']."'");
			$result['channelguid'] = $db->get_var("select channelguid from companies where guid = '".$result['companyguid']."'");
			$result['siteguid'] = $usr->lastsite;
			$result['sitename'] = $db->get_var("select sitename from sites where guid = '".$result['siteguid']."'");
			$result['storetype'] = $db->get_var("select storetype from sites where guid = '".$result['siteguid']."'");
			$result['packageguid'] = $db->get_var("select packageguid from companies where guid = '".$result['companyguid']."'");
		}
	}

	$cleanfields = array(
		'email',
		'userguid',
		'fname',
		'sname',
		'cellnr',
		'pword',
		'companyguid',
		'companyname',
		'channelguid',
		'siteguid',
		'sitename',
		'storetype',
		'packageguid',
	);

	if ($result['live'] == '') {
		$result['message'] = 'Not Ok';
	} elseif ($result['live'] == -1) {
		$result['message'] = 'Register';
		$takeLive = true;
		foreach ($fields as $field) {
			if (empty($result[$field])) { $takeLive = false; }
		}
		if ($takeLive) {
			$db->update("users",array('live' => 1),array('uguid' => $result['userguid']));
			$result['live'] = 1;
			$result['message'] = 'Active';	
		}
	} elseif ($result['live'] == 0) {
		$result['message'] = 'Inactive';



	} elseif ($result['live'] == 1) {
		$result['message'] = 'Active';
	}
	unset($result['pword']);

	if ($result['live'] == 0 || $result['live'] == 1) {
		foreach ($cleanfields as $field) {
			$result[$field] = '';
		}
	}
	console($result);

	return $result;
?>