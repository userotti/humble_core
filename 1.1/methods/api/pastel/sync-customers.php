<?php

global $user;

$user = new stdClass;
// FOR PROCESSING communities SYNC


// customer builder
$translate_field = array(
	'descr' 					=> 'Name',
	'live' 						=> 'Active',
	//'pastelid' 					=> 'ID',
);

$translate_field_types = array(
	'descr' 					=> 'string',
	'live' 						=> 'boolean',
	'pastelid' 					=> 'int',
);


$sites = $db->get_results( "SELECT * FROM `sites` WHERE `pasteluser` != '' AND `pastelpass` != '' AND `pastelid` != ''; " );

foreach( $sites as $site){
	
	// set user & pass
	$user->pastel_user = $site->pasteluser;
	$user->pastel_pass = $site->pastelpass;

	// get site's communities

	$query = "SELECT
		`community`.*,
		`pasteltranslate`.`pastelid`

	FROM `community`
	LEFT JOIN `pasteltranslate` ON (`community`.`guid` = `pasteltranslate`.`itemguid` AND `pasteltranslate`.`siteguid` = '".$site->guid."')

	WHERE `community`.`communitytype` = 0 AND `community`.`companyguid` = '".$site->coguid."';";

	// humble communities
	$communities = $db->get_results( $query, ARRAY_A);

	// pastel customers
	$args = array( 
		'companyid'	=> $site->pastelid,

	);
	$pre_customers = do_pastel_call( 'customer/get', $args );
	$customers = array();
	foreach($pre_customers['Results'] as &$customer){
		if(isset($customer['Category'])){
			unset($customer['Category']);
		}

		$customers[$customer['ID']] = $customer;
	}


	foreach($communities as &$community){
		//dump($community);
		if(!empty($community['pastelid'])){

			if(!isset($customers[$community['pastelid']])){
				// community not in pastel but has an id - assume its been removed from pastel so set it inactive
				$db->update('communities', array('live' => 0), array('guid'=>$community['guid'], 'companyguid' => $site->coguid));
			}else{
				// check if humble is different from pastel
				if(!empty($community['live'])){
					
					// reset the update object
					$update = array();

					// check differences
					$customer = $customers[$community['pastelid']];
					
					// uses the traslate_fiel array for usable fields
					foreach($translate_field as $cfield=>$sfield){

						if($community[$cfield] != $customer[$sfield]){
							// different - add to update
							//dump(,0);
							settype( $community[$cfield], gettype($customer[$sfield]));
							$update[$sfield] = $community[$cfield];
						}

					}

					
					// changes - update
					if(!empty($update)){
						// FIX FOR STUPID PEOPLE!!!
						$insert['CommunicationMethod'] = 0;

						// merge changes with object
						$update = array_merge($customer, $update);

						$update_result = do_pastel_call( 'customer/save', $args, $update );

					}
				}

			}

		}else{
				
			if(empty($community['live'])){
				continue; // ignore disabled communities.
			}
			// SEE IF ITS in the pastil customers

			$found = false;
			//dump($community['stockcode'],0);
			//echo '----------------<br>';
			foreach($customers as $pastelid=>&$customer){

				// reduce to base values and compare
				if(reduce_value($customer['Name']) == reduce_value( $community['descr'] )){
					// found match
					$found = $pastelid;
					break;
				}
			}
			
			if(!empty($found)){
				// found - update translate
				$translate = array(
					'siteguid'	=>	$site->guid,
					'itemguid'	=>	$community['guid'],
					'pastelid'	=>	$found
				);
				
				$db->insert('pasteltranslate', $translate);

			}else{
				// does not exist = add to pastel				
				// reset insert object
				$insert = array();

				// uses the traslate_fiel array for usable fields
				foreach($translate_field as $cfield=>$sfield){

					// create customer object to send
					settype($community[$cfield], $translate_field_types[$cfield]);
					$insert[$sfield] = $community[$cfield];

				}
				// FIX FOR STUPID PEOPLE!!!
				$insert['CommunicationMethod'] = 0;
				

				// send to pastel
				$insert_result = do_pastel_call( 'customer/save', $args, $insert );
				
				// Add community to translate table.
				$translate = array(
					'siteguid'	=>	$site->guid,
					'itemguid'	=>	$community['guid'],
					'pastelid'	=>	$insert_result['ID']
				);			
				$db->insert('pasteltranslate', $translate);

			}

		}

	}


	// Remove the customers that are now linked.
	
	foreach ($communities as &$community) {
		if(!empty($community['pastelid'])){
			unset($customers[$community['pastelid']]);
		}
	}

	// Create communities on humble
	if(!empty($customers)){

		foreach($customers as &$customer){
			
			// reset insert
			$insert = array();
			// uses the traslate_fiel array for usable fields
			foreach($translate_field as $cfield=>$sfield){

				// create customer object to send
				settype($customer[$sfield], $translate_field_types[$cfield]);

				$insert[$cfield] = $customer[$sfield];

			}

			if(!empty($insert)){
				$newCommunity = array(
					"guid"			=>	gen_uuid(),
					"companyguid"	=>	$site->coguid,
					"communitytype"	=>	0
				);
				$insert = array_merge($newCommunity, $insert);

				// add to community
				$db->insert('community', $insert);

				//add to translate
				$translate = array(
					'siteguid'	=>	$site->guid,
					'itemguid'	=>	$insert['guid'],
					'pastelid'	=>	$customer['ID']
				);
				$db->insert('pasteltranslate', $translate);

			}


		}

	}


}

