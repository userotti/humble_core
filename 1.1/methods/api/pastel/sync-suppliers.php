<?php

global $user;

$user = new stdClass;
// FOR PROCESSING communities SYNC


// supplier builder
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

	WHERE `community`.`communitytype` = 1 AND `community`.`companyguid` = '".$site->coguid."';";

	// humble communities
	$communities = $db->get_results( $query, ARRAY_A);

	// pastel suppliers
	$args = array( 
		'companyid'	=> $site->pastelid,

	);
	$pre_suppliers = do_pastel_call( 'supplier/get', $args );
	$suppliers = array();
	foreach($pre_suppliers['Results'] as &$supplier){
		if(isset($supplier['Category'])){
			unset($supplier['Category']);
		}

		$suppliers[$supplier['ID']] = $supplier;
	}



	foreach($communities as &$community){

		if(!empty($community['pastelid'])){

			if(!isset($suppliers[$community['pastelid']])){
				// community not in pastel but has an id - assume its been removed from pastel so set it inactive
				$db->update('communities', array('live' => 0), array('guid'=>$community['guid'], 'companyguid' => $site->coguid));
			}else{
				// check if humble is different from pastel
				if(!empty($community['live'])){
					
					// reset the update object
					$update = array();

					// check differences
					$supplier = $suppliers[$community['pastelid']];
					
					// uses the traslate_fiel array for usable fields
					foreach($translate_field as $cfield=>$sfield){

						if($community[$cfield] != $supplier[$sfield]){
							// different - add to update
							//dump(,0);
							settype( $community[$cfield], gettype($supplier[$sfield]));
							$update[$sfield] = $community[$cfield];
						}

					}

					
					// changes - update
					if(!empty($update)){

						// merge changes with object
						$update = array_merge($supplier, $update);

						$update_result = do_pastel_call( 'supplier/save', $args, $update );

					}
				}

			}

		}else{
				
			if(empty($community['live'])){
				continue; // ignore disabled communities.
			}
			// SEE IF ITS in the pastil suppliers

			$found = false;
			//dump($community['stockcode'],0);
			//echo '----------------<br>';
			foreach($suppliers as $pastelid=>&$supplier){

				// reduce to base values and compare
				if(reduce_value($supplier['Name']) == reduce_value( $community['descr'] )){
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
				//dump($translate);
				$db->insert('pasteltranslate', $translate);

			}else{
				// does not exist = add to pastel
				
				// reset insert object
				$insert = array();

				// uses the traslate_fiel array for usable fields
				foreach($translate_field as $cfield=>$sfield){

					// create supplier object to send
					settype($community[$cfield], $translate_field_types[$cfield]);

					$insert[$sfield] = $community[$cfield];

				}

				// send to pastel
				$insert_result = do_pastel_call( 'supplier/save', $args, $insert );

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


	// Remove the suppliers that are now linked.
	
	foreach ($communities as &$community) {
		if(!empty($community['pastelid'])){
			unset($suppliers[$community['pastelid']]);
		}
	}

	// Create communities on humble
	if(!empty($suppliers)){

		foreach($suppliers as &$supplier){
			
			// reset insert
			$insert = array();
			// uses the traslate_fiel array for usable fields
			foreach($translate_field as $cfield=>$sfield){

				// create supplier object to send
				settype($supplier[$sfield], $translate_field_types[$cfield]);

				$insert[$cfield] = $supplier[$sfield];

			}

			if(!empty($insert)){
				$newCommunity = array(
					"guid"			=>	gen_uuid(),
					"companyguid"	=>	$site->coguid,
					"communitytype"	=>	1
				);
				$insert = array_merge($newCommunity, $insert);

				// add to community
				$db->insert('community', $insert);

				//add to translate
				$translate = array(
					'siteguid'	=>	$site->guid,
					'itemguid'	=>	$insert['guid'],
					'pastelid'	=>	$supplier['ID']
				);
				$db->insert('pasteltranslate', $translate);

			}


		}

	}


}

