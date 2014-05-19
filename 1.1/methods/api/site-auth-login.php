<?php
/*

Caldoza Engine ------------------------

File	:	api/site-auth-login.php
Created	: 	2013-12-04

*/
/*
if(!empty($_POST)){
	ob_start();
	dump($_POST,0);
	$debug = ob_get_clean();
	$db->insert('debugnotes', array('message'=>$debug));
}*/
	/// do a pin login
	if(!empty($_POST['pin']) && !empty($_POST['siteguid']) && !empty($_POST['device'])){

		/// get pre user
		// get sites
		$site = $db->get_row($db->prepare("SELECT * FROM `sites` WHERE `guid` = %s;", $_POST['siteguid']));
		$user = $db->get_row($db->prepare("SELECT * FROM `users` WHERE `cguid` = '".$site->coguid."' AND `cashierpin` = %d", $_POST['pin']));		

		if(!empty($user)){

			$channel = $db->get_var("SELECT `channelguid` FROM `companies` WHERE `guid` = '".$user->cguid."';");

			// generate login attempt
			$login = array(
				'deviceGUID'	=>	$_POST['device'],
				'deviceType'	=>	'Web Browser',
				'deviceName'	=>	$_SERVER['HTTP_USER_AGENT'],
				'ip'			=>	$_SERVER['REMOTE_ADDR'],	
				'address'		=>	$_SERVER['HTTP_REFERER'],
				'email'			=>	$user->email,
				'password'		=>	$user->pword,
				'siteguid'		=>	$site->guid
			);

			$login['result'] = 'OK';
			$login['tokenGUID']		=	gen_uuid();
			$out = array(
				'message'			=>	$login['result'],
				'user_guid'			=>	$user->uguid,
				'company_guid'		=>	$user->cguid,
				'token_guid'		=>	$login['tokenGUID'],
				'channel_guid'		=>	$channel,
			);

			$login['userGUID']		= $user->uguid;
		
			if($db->insert('login', $login)){

				$db->delete('confirmed', array('deviceGUID' => $_POST['device']));

				// last login update
				$db->update('users', array('lastlogin'=>date('Y-m-d H:i:s')), array('uguid'=>$user->uguid));

				return $out;
			}			

		}else{

			return array('message'=>'Your Cashier PIN Is Incorrect');

		}

	}


	if(empty($_POST['email']) || empty($_POST['password'])){
		return array( 'message' => 'email and password not provided' );
	}
//$deviceGUID

	if(!empty($_POST['device'])){
		$deviceGUID = $_POST['device'];
	}else{
		$deviceGUID = gen_uuid();
	}

	// generate login attempt
	$login = array(
		'deviceGUID'	=>	$deviceGUID,
		'deviceType'	=>	'Web Browser',
		'deviceName'	=>	$_SERVER['HTTP_USER_AGENT'],
		//'longitude'		=>	$_POST['longitude'],
		//'latitude'		=>	$_POST['latitude'],
		'ip'			=>	$_SERVER['REMOTE_ADDR'],	
		'address'		=>	$_SERVER['HTTP_REFERER'],
		'email'			=>	$_POST['email'],
		'password'		=>	$_POST['password'],

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
		$db->insert('login', $login);
		return array('message'=>$login['result']);
	}
	if($user->pword !== $_POST['password']){
		if($user->pword !== strtoupper($_POST['password'])){
			if($user->pword !== sha1($_POST['password'])){
				if($user->pword !== sha1(strtoupper( $_POST['password']))){
					$login['result'] = 'Your Password Is Not Correct';
					$db->insert('login', $login);
					return array('message'=>$login['result']);
				}
			}
		}
	}
	if((int) $user->live !== 1){
		$login['result'] = 'User Is Not Active';
		$db->insert('login', $login);
		return array('error'=>$login['result']);
	}
	//channel
	$channel = $db->get_var("SELECT `channelguid` FROM `companies` WHERE `guid` = '".$user->cguid."';");

	if(!empty($_POST['siteguid'])){
		$sites = $db->get_results($db->prepare("SELECT `guid` FROM `sites` WHERE `coguid` = '".$user->cguid."' && `guid` = %s;", $_POST['siteguid']));		
	}
	if(empty($sites)){
		$sites = $db->get_results("SELECT `guid` FROM `sites` WHERE `coguid` = '".$user->cguid."';");
	}

	if(count($sites) == 1){
		$login['siteguid'] = $sites[0]->guid;
	}
	$login['result'] = 'OK';
	$login['tokenGUID']		=	gen_uuid();
	$out = array(
		'message'			=>	$login['result'],
		'user_guid'			=>	$user->uguid,
		'company_guid'		=>	$user->cguid,
		'token_guid'		=>	$login['tokenGUID'],
		'channel_guid'		=>	$channel,
	);

	$login['userGUID']		= $user->uguid;

	if($db->insert('login', $login)){

		$db->delete('confirmed', array('deviceGUID' => $deviceGUID));

		// last login update
		$db->update('users', array('lastlogin'=>date('Y-m-d H:i:s')), array('uguid'=>$user->uguid));

		return $out;
	}
	return array('message' => 'login error');



?>