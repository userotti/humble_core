<?php

if(isset($params['token'])){

	// AUTH TOKEN
	$user = $db->get_row($db->prepare("
		
		SELECT 
			`log`.*,
			`user`.*

		FROM 
			`login` AS `log`
		JOIN
			`users` AS `user` ON (`log`.`userGUID` = `user`.`uguid`)

		WHERE `log`.`tokenGUID` = %s;

		", $params['token']));

	if(empty($user)){
		return array('message'=>'invalid token');
	}

}else{



	// DO LOGIN PROCESS

	if(empty($_POST['email']) || empty($_POST['password'])){
		return array( 'message' => 'email and password not provided' );
	}

	if(empty($_POST['device_name']) || empty($_POST['device_type']) || empty($_POST['longitude']) || empty($_POST['latitude']) || empty($_POST['address']) ){
		return array( 'message' => 'device name, type location and address are required' );
	}


	// generate login attempt
	$login = array(
		'deviceGUID'	=>	$params['deviceGUID'],
		'deviceType'	=>	$_POST['device_type'],
		'deviceName'	=>	$_POST['device_name'],
		'longitude'		=>	$_POST['longitude'],
		'latitude'		=>	$_POST['latitude'],
		'ip'			=>	$_SERVER['REMOTE_ADDR'],	
		'address'		=>	$_POST['address'],
		'email'			=>	$_POST['email'],
		'password'		=>	$_POST['password']
	);

	//get user by key
	$user = $db->get_row( $db->prepare( "
		SELECT 
			*
		FROM
			`users`
		WHERE
			`email` = %s
		LIMIT 1", $_POST['email'] ) );

	if(empty($user)){
		$login['result'] = 'User Does Not Exist';
		$login['tokenGUID']		=	gen_uuid();
		$db->insert('login', $login);
		return array('message'=>$login['result']);
	}
	if($user->pword !== $_POST['password']){
		if($user->pword !== sha1($_POST['password'])){
			$login['tokenGUID']		=	gen_uuid();
			$login['userGUID']		= $user->uguid;
			$login['result'] = 'Your password is incorrect';
			$db->insert('login', $login);
			return array('message'=>$login['result']);
		}
	}
	if((int) $user->live !== 1){
		$login['result'] = 'User Is Not Active';
		$login['userGUID']		= $user->uguid;
		$db->insert('login', $login);
		return array('error'=>$login['result']);
	}

	$login['result'] = 'OK';
	$login['tokenGUID']		=	gen_uuid();
	$out = array(
		'message'			=>	$login['result'],
		'user_guid'			=>	$user->uguid,
		'company_guid'		=>	$user->cguid,
		'token_guid'		=>	$login['tokenGUID'],
		'user_fname'		=> 	$user->fname,
		'user_name'			=> 	sprintf('%s %s',$user->fname,$user->sname),
	);

	if(isset($user->locked)){
		$out['locked'] = $user->locked;
	}

	$login['userGUID']		= $user->uguid;


	// get the last login from the same user - else make a new one.
	/*
	$sprevLogin = $db->get_row("SELECT * FROM `login` WHERE `userGUID` = '".$user->uguid."' AND `deviceName` = '".$user->deviceName."' ORDER BY `insdate` DESC LIMIT 1;");
	if(!empty($prevLogin)){

		console('OLD LOGIN');

		// update locked status
		$db->update('login', array('locked'=>0), array('tokenGUID'=>$prevLogin->tokenGUID));

		//record login
		$db->update('users', array('lastlogin'=>date('Y-m-d H:i:s')), array('uguid'=>$user->uguid));

		// set output
		$out['token_guid'] = $prevLogin->tokenGUID;
		console($out);
		// send output
		return $out;
	}*/

	if($db->insert('login', $login)){
		// last login update
		$db->delete('confirmed', array('deviceGUID' => $params['deviceGUID']));

		$db->update('users', array('lastlogin'=>date('Y-m-d H:i:s')), array('uguid'=>$user->uguid));

		return $out;
	}
	return array('message' => 'login error');
}
