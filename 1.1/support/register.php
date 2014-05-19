<?php
/*

Caldoza Engine ------------------------

File	:	support/register.php
Created	: 	2014-01-15

*/



if(empty($_POST)){

	if(!empty($_GET['check']) && !empty($_GET['email'])){
		// checks a user exists

		
		$user = $db->get_row( $db->prepare( 'SELECT * FROM `users` WHERE `email` = %s', $_GET['email'] ) );
		if(!empty($user)){
			return array('error'=>'User already exists. <a href="lostpassword.php?email='.$_GET['email'].'">Forgotten Password?</a>');
		}else{
			return array('ok' => 'available');
		}
	}

	if(!empty($_GET['guid'])){
		// company already there
		// add device to out
		return array('guid' => $_GET['guid'], 'device' => $_GET['device']);
	}

	// new
	return array('new' => true);
}

if(!empty($_POST['guid'])){

	// USER DETAILS
	$user = $db->get_row( $db->prepare("

		SELECT 
			`users`.`uguid`,
			`users`.`email`,
			`users`.`pword`,
			`sites`.`guid` AS `site_guid`,
			`companies`.`guid` AS `company_guid`,
			`companies`.`company` AS `company_name`,
			`companies`.`channelguid` AS `channel`,
			`companies`.`packageguid` AS `package_guid`

		FROM `users`
		LEFT JOIN `sites` ON (`users`.`cguid` = `sites`.`coguid`)
		LEFT JOIN `companies` ON (`users`.`cguid` = `companies`.`guid`)

		WHERE 

			`uguid` = %s

	", $_POST['guid'] ) );

	if(!empty($_POST['backto'])){
		$user->stage = $_POST['backto'];
		$user->guid = $user->uguid;

		return $user;
	}

	if(!empty($_POST['package'])){
		//update package

		$updateCompany = array(
			'packageguid'	=> $_POST['package']
		);
		$db->update('companies', $updateCompany, array('guid' => $user->company_guid));

		// MAKE humble CUSTOMER
		// add to customer
		//$humbleguid = '6a8c0b30-616e-11e3-a8b0-005056ba42b8';
		$humbleguid = 'A2CD6180-9A7A-4A83-8EB5773DB278844D';

		$thisUser = $db->get_row("select * from users where uguid = '".$user->uguid."'");
		$contactname = sprintf("$thisUser->fname $thisUser->sname");
		$contactnumber = $thisUser->cellnr;

		// new customer
		$newCustomer = array(
			'guid'          =>	gen_uuid(),
			'companyguid'   =>	$humbleguid,
			'descr'         =>	$user->company_name,
			'communitytype' => 	0,
			'live'          =>	1,
			'email'         =>	$user->email,
			'tillcustomer'  => 1,	
			'contactname'   => $contactname,
			'contactnumber' => $contactnumber,		
		);

		$db->insert('community', $newCustomer);
		//checkCustomers("FEBE2730-081E-417B-A5F2C84B8728DBBA");

		// get package
		$package = $db->get_row( $db->prepare("SELECT * FROM `packages` WHERE `guid` = %s", $_POST['package']));
		if($package->monthlycost !== '0.00'){

			// return payment - bacnk form
			return array('guid' => $_POST['guid'], 'stage' => 'banking');
		}else{
			// update user to live
			$db->update('users', array('live'=>1), array('uguid'=>$user->uguid));
			// generate login attempt
			console("hier kom die post");
			console($_POST);
			syntaxerror


			$login = array(
				'deviceGUID'	=>	$_POST['device'],
				'deviceType'	=>	'Web Browser',
				'deviceName'	=>	$_SERVER['HTTP_USER_AGENT'],
				'ip'			=>	$_SERVER['REMOTE_ADDR'],	
				'address'		=>	$_SERVER['HTTP_REFERER'],
				'email'			=>	$user->email,
				'password'		=>	$user->pword,
				'result'		=>	'OK',
				'userGUID'		=>	$user->uguid,
				'tokenGUID'		=>	gen_uuid(),
				'siteguid'		=>	$user->site_guid,
			);
			if($db->insert('login', $login)){

				$out = array(
					'message'			=>	$login['result'],
					'user_guid'			=>	$user->uguid,
					'company_guid'		=>	$user->company_guid,
					'token_guid'		=>	$login['tokenGUID'],
					'channel_guid'		=>	$user->channel,
					'stage' 			=> 'complete'
				);

				return $out;
			}else{
				return array('error'=>'Sorry, there was a problem processing, please reload the page. Your progress has been saved.');
			}

		}

		
		
	}

	if(!empty($_POST['acctype'])){
		if(empty($_POST['accname']) || empty($_POST['acctype']) || empty($_POST['accnr']) || empty($_POST['branchcode']) ){
			return array('error'=>'All Fields Are Required');
		}
		$newBanking = array(
			'companyguid'	=>	$user->company_guid,
			'accname'		=>	$_POST['accname'],
			'acctype'		=>	$_POST['acctype'],
			'accnr'			=>	$_POST['accnr'],
			'branchcode'	=>	$_POST['branchcode'],
		);
		// ADD TO DB
		$db->insert('bankdetails', $newBanking);

		$package = $db->get_row("SELECT * FROM `packages` WHERE `guid` = '".$user->package_guid."';");

		$inmonth = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
		$remaining = $inmonth - date('d');

		$tobill = ( $package->monthlycost / $inmonth ) * $remaining;

		$newSagePay = array(
			'guid'			=>	gen_uuid(),
			'accref'		=>	substr( reduce_value( $user->company_name ) ,0 , 5 ) . rand(1000,9999),
			'accname'		=>	$user->company_name,			
			'bankaccname'	=>	$_POST['accname'],
			'acctype'		=>	$_POST['acctype'],			
			'branchcode'	=>	$_POST['branchcode'],
			'accnr'			=>	$_POST['accnr'],
			'contractamount'=>	$package->monthlycost * 1000,
			'batchamount'	=>	$tobill*1000,
			'emailaddress'	=>	$user->email,
			'extra1'		=>	$user->company_guid,
			'extra2'		=>	$package->guid,
			'extra3'		=>	$user->uguid,
			'stateref'		=>	'humble Software'
		);

		$db->insert('sagepay', $newSagePay);
		//dump($newSagePay);

		/*
		// MAKE CUSTOMER _ PAID PACKAGE
		// add to customer
		$humbleguid = '6a8c0b30-616e-11e3-a8b0-005056ba42b8';

		// new customer
		$newCustomer = array(
			'guid'			=>	gen_uuid(),
			'companyguid'	=>	$humbleguid,
			'descr'			=>	$user->company_name,
			'communitytype' => 	0,
			'live'			=>	1,
			'email'			=>	$user->email			
		);

		$db->insert('community', $newCustomer);*/


		// update user to live
		$db->update('users', array('live'=>1), array('uguid'=>$user->uguid));

		// generate login attempt
		$login = array(
			'deviceGUID'	=>	$_POST['device'],
			'deviceType'	=>	'Web Browser',
			'deviceName'	=>	$_SERVER['HTTP_USER_AGENT'],
			'ip'			=>	$_SERVER['REMOTE_ADDR'],	
			'address'		=>	$_SERVER['HTTP_REFERER'],
			'email'			=>	$user->email,
			'password'		=>	$user->pword,
			'result'		=>	'OK',
			'userGUID'		=>	$user->uguid,
			'tokenGUID'		=>	gen_uuid(),
			'siteguid'		=>	$user->site_guid,
		);
		if($db->insert('login', $login)){

			$out = array(
				'message'			=>	$login['result'],
				'user_guid'			=>	$user->uguid,
				'company_guid'		=>	$user->company_guid,
				'token_guid'		=>	$login['tokenGUID'],
				'channel_guid'		=>	$user->channel,
				'stage' 			=> 'complete'
			);

			return $out;
		}else{
			return array('error'=>'Sorry, there was a problem processing, please reload the page. Your progress has been saved.');
		}

		//sagepay
		
		// CALCULATE PRORATA


	}

	/// UPDATE - ADD TO REGISTRATION
	if(!empty($_POST['company'])){


		// NEW Company DETAILS
		$updateCompany = array(
			'company'	=> $_POST['company'],
			'vatnr'		=> $_POST['vatnr']
		);
		$db->update('companies', $updateCompany, array('guid' => $user->company_guid));

		// NEW SITE DETAILS
		$updateSite = array(
			'sitename'	=>	$_POST['sitename'],
			'regnr'		=>	$_POST['regnr']
		);
		$db->update('sites', $updateSite, array('guid' => $user->site_guid, 'coguid' => $user->company_guid));

		
		return array('guid' => $_POST['guid'], 'stage' => 'package');

	}

	return $user;

	return array('guid' => $_POST['guid']);

}

// FALL TO NEW REGISTRATION
$newCompany = array(
	'guid'			=> gen_uuid(),
	'channelguid'	=> 'aced8d4e-5b2b-11e3-8696-005056a5104a', // generic

);

$newSite = array(
	'guid'		=>	gen_uuid(),
	'coguid'	=>	$newCompany['guid'],
);

$newUser = array(
	'uguid'			=>	gen_uuid(),
	'cguid'			=>	$newCompany['guid'],
	'fname'			=>	$_POST['firstname'],
	'sname'			=>	$_POST['lastname'],
	'email'			=>	strtoupper( $_POST['email'] ), /// SIGH!
	'pword'			=>	$_POST['pass'],
	'cashierpin' 	=>  rand(1000, 9999),
	'cellnr'		=> $_POST['cell'],
	'lastsite'		=>	$newSite['guid'],
	'live'			=>	0,
);

$db->insert('companies', $newCompany);
$db->insert('sites', $newSite);
$db->insert('users', $newUser);

return array('guid' => $newUser['uguid'], 'stage' => 'company');

/*

?><div style="display: block;" class="modal hide in" id="modal_support" aria-hidden="false">
		<div class="modal-header">
			<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
			<h3 id="modal_support_title">Thank you. <?php echo $_POST['name']; ?></h3>
		</div>
		<div style="max-height:800px" class="modal-body">
		<p>Your registration has been recived and will be reviewed. We will contact you shortly.</p>
		</div>
		<div class="modal-footer" id="modal_support_footer">
			<button aria-hidden="true" data-dismiss="modal" class="btn btn-success" type="button">Close</button>
		</div>
	</div>




CREATE TABLE `sagepay` (
  `guid` varchar(40) NOT NULL,
  `insdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `accref` varchar(22) NOT NULL,
  `accname` varchar(50) NOT NULL,
  `bankaccname` varchar(50) NOT NULL,
  `acctype` int(11) NOT NULL,
  `branchcode` varchar(6) NOT NULL,
  `accnr` varchar(11) NOT NULL,
  `contractamount` int(11) NOT NULL,
  `batchamount` int(11) NOT NULL,
  `emailaddress` varchar(100) NOT NULL DEFAULT '',
  `ccname` varchar(50) NOT NULL DEFAULT '',
  `cctoken` varchar(36) NOT NULL DEFAULT '0',
  `expmonth` int(11) NOT NULL DEFAULT '0',
  `expyear` int(11) NOT NULL DEFAULT '0',
  `ccmasknr` varchar(16) NOT NULL DEFAULT '0',
  `iscc` int(11) NOT NULL DEFAULT '0',
  `extra1` varchar(50) NOT NULL DEFAULT '',
  `extra2` varchar(50) NOT NULL DEFAULT '',
  `extra3` varchar(50) NOT NULL DEFAULT '',
  `stateref` varchar(20) NOT NULL DEFAULT 'humble Software',
  `naedo` int(11) NOT NULL DEFAULT '0',
  `batchnr` varchar(50) NOT NULL DEFAULT '',
  `actiondate` datetime DEFAULT NULL,
  `sent` int(11) NOT NULL DEFAULT '0',
  `confirmed` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




*/