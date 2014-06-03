<?php




$site = $db->get_row( "SELECT * FROM `sites` WHERE `guid` = '".$user->siteguid."';" );

$h_suppliers = $db->get_results( "SELECT * FROM `community` WHERE `companyguid` = '".$user->cguid."' ;" );

if( empty( $site->pastelid ) ){
	return array( 'error' => 'pastel is not setup' );
}

$args = array( 
	'companyid'	=> $site->pastelid
);

$p_suppliers = do_pastel_call('supplier/get', $args );


$p_customers = do_pastel_call('customer/get', $args );
//dump($p_customers);



$master = array();
$paste_ids = array();
// List Pastel Suppliers
foreach($p_suppliers['Results'] as $index=>$supplier){
	// catch all pastel ID's to check the when an id is found on till- to delete or not.
	$paste_ids[$supplier['ID']] = true;
	$master['p'][$index] = strtolower( trim( $supplier['Name'] ) );
}
// List Humble Suppliers
foreach( $h_suppliers as $index=>$supplier ){
	$master['h'][$index] = strtolower( trim( $supplier->descr ) );
}

// Loop Through
$pastel = array();
$humble = array();
// Humble list
if(!empty($master['h'])){
	foreach($master['h'] as $key=>&$value){
		if(!in_array($value, $master['p'])){
			$pastel[] = $h_suppliers[$key];
		}
	}
}
// Pastel List
if(!empty($master['p'])){
	foreach($master['p'] as $key=>&$value){
		if(!in_array($value, $master['h'])){
			$humble[] = $p_suppliers['Results'][$key];
		}
	}
}

// PREPARE UPDATE OF PASTEL
if(!empty($pastel)){
	foreach($pastel as $supplier){
		if(!empty($supplier->pastelid)){
			// already linked skip
			if(!isset($paste_ids[$supplier->pastelid])){
				//dump($supplier,0);
			}
			continue;
		}

		$newsupplier = array(
			"Name"				=>	$supplier->descr,
			"Email"				=>	$supplier->email,
			"Active"			=>	($supplier->live == 1 ? true : false ),
			"Balance"			=>	(float)$supplier->current_balance * 100,
			"CreditLimit"		=>	(float)$supplier->community_limit * 100,
			"PostalAddress01"	=>	$supplier->address_line1,
			"PostalAddress02"	=>	$supplier->address_line2,
			"PostalAddress03"	=>	$supplier->suburb,
			"PostalAddress04"	=>	$supplier->city,
			"PostalAddress05"	=>	$supplier->postal_code,
			"DeliveryAddress01"	=>	$supplier->billing_address_line1,
			"DeliveryAddress02"	=>	$supplier->billing_address_line2,
			"DeliveryAddress03"	=>	$supplier->billing_suburb,
			"DeliveryAddress04"	=>	$supplier->billing_city,
			"DeliveryAddress05"	=>	$supplier->billing_postal_code
		);

		$args = array( 
			'companyid'	=> $site->pastelid
		);
		if(empty($supplier->communitytype)){
			if(!empty($supplier->email)){
				$newsupplier['CommunicationMethod'] = 1;
			}else{
				$newsupplier['CommunicationMethod'] = 2;
			}

			$result = do_pastel_call('customer/save', $args, $newsupplier);
		}else{
			$result = do_pastel_call('supplier/save', $args, $newsupplier);
		}
		
		if(!empty($result['ID'])){
			$db->update('community', array('pastelid' => $result['ID']), array('guid' => $supplier->guid));
		}
		//return $newsupplier;
	}
}
if(!empty($humble)){
	foreach($humble as $supplier){

		$newsupplier = array(
			"guid"					=> gen_uuid(),
			"companyguid"			=> $user->cguid,
			"descr"					=> $supplier['Name'],
			"communitytype"			=> 1,
			"live"					=> $supplier['Active'],
			"current_balance"		=> (int)$supplier['Balance'] / 100,
			"community_limit"		=> (int)$supplier['CreditLimit'] / 100,
			"email"					=> $supplier['Email'],
			"address_line1"			=> $supplier['PostalAddress01'],
			"address_line2"			=> $supplier['PostalAddress02'],
			"suburb"				=> $supplier['PostalAddress03'],
			"city"					=> $supplier['PostalAddress04'],
			"postal_code"			=> $supplier['PostalAddress05'],
			"billing_address_line1"	=> $supplier['DeliveryAddress01'],
			"billing_address_line2"	=> $supplier['DeliveryAddress02'],
			"billing_suburb"		=> $supplier['DeliveryAddress03'],
			"billing_city"			=> $supplier['DeliveryAddress04'],
			"billing_postal_code"	=> $supplier['DeliveryAddress05'],
			"pastelid"				=> $supplier['ID'],
		);
		$db->insert('community', $newsupplier);
	}
}

//return $master;
