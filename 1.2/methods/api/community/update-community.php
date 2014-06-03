<?php
/*

Caldoza Engine ------------------------

File	:	api/community/update-community.php
Created	: 	2013-12-17

*/




$communityfields = array(
  'descr',
  'communitytype',
  'live',
  'current_balance',
  'community_limit',
  'email',
  'address_line1',
  'address_line2',
  'suburb',
  'city',
  'province',
  'postal_code',
  'billing_descr',
  'billing_email',
  'billing_address_line1',
  'billing_address_line2',
  'billing_suburb',
  'billing_city',
  'billing_province',
  'billing_postal_code',
);

$siteGUID = $user->siteguid;

$site = $db->get_row( "SELECT * FROM `sites` WHERE `guid` = '".$user->siteguid."';" );
if(!empty($site->pastelid)){
 	$args = array( 
    	'companyid' => $site->pastelid
  	);
}   

if(!empty($params['communityguid'])){
    $community = $db->get_row($db->prepare("SELECT * FROM `community` WHERE `companyguid` = %s AND `guid` = %s ", $user->cguid,  $params['communityguid']), ARRAY_A);
    if(!empty($community)){
    	foreach($communityfields as $field){
    		if(isset($_POST[$field])){
    			$community[$field] = $_POST[$field];
    		}
    	}
    	// update 
    	$db->update('community', $community, array('guid'=>$params['communityguid'], 'companyguid' => $user->cguid));
    	$community['message'] = 'OK';
        $is_customer = $db->get_row( $db->prepare("SELECT * FROM `community` WHERE `guid` = %s ", $params['communityguid']));
        $is_customer->pastelid = get_pastel_id($user->siteguid,$params['communityguid']);
         // create pastel customer array
        $pastelcustomer = array(
          	"Name"        =>  $is_customer->descr,
          	"Email"       =>  $is_customer->email,
          	"CommunicationMethod" => 1,
          	"Active"      =>  ($is_customer->live == 1 ? true : false ),
          	"Balance"     =>  (float)$is_customer->current_balance * 100,
          	"CreditLimit"   =>  (float)$is_customer->community_limit * 100,
          	"PostalAddress01" =>  $is_customer->address_line1,
          	"PostalAddress02" =>  $is_customer->address_line2,
          	"PostalAddress03" =>  $is_customer->suburb,
          	"PostalAddress04" =>  $is_customer->city,
          	"PostalAddress05" =>  $is_customer->postal_code,
          	"DeliveryAddress01" =>  $is_customer->billing_address_line1,
          	"DeliveryAddress02" =>  $is_customer->billing_address_line2,
          	"DeliveryAddress03" =>  $is_customer->billing_suburb,
	        "DeliveryAddress04" =>  $is_customer->billing_city,
          	"DeliveryAddress05" =>  $is_customer->billing_postal_code
        );

        $cType = $is_customer->communitytype;
        if ($community['communitytype'] == 0) {
			updateCustomerOnPastel($siteGUID,$community['guid']);			
		} else if ($community['communitytype'] == 1) {
			updateSupplierOnPastel($siteGUID,$community['guid']);		
		}

      	if(empty($is_customer->pastelid)){
        	//console($is_customer);
        	// is pastel customer - update 
        	if(!empty($site->pastelid)){
          		//$result = do_pastel_call($meth, $args, $pastelcustomer);
        	}
        	
        	if( !empty( $result['ID'] )){
          		// update customer on humble
          		$db->update('community', array('pastelid'=>$result['ID']), array('guid'=>$is_customer->guid));
          		$is_customer->pastelid = $result['ID'];
        	}  
      	}else{
        	$pastelcustomer['ID'] = $is_customer->pastelid;
        	if(!empty($site->pastelid)){
          		//$result = do_pastel_call($meth, $args, $pastelcustomer);
          		//checkSuppliers($siteGUID);
        	}
      	}
    	return $community;
    }
}
if(!empty($_POST)){
  	// fall through to insert
	// new community

  	// search if an email address is provided first
  	if(!empty($_POST['email'])){
    	$is_customer = $db->get_row( $db->prepare("SELECT * FROM `community` WHERE `email` = %s AND `companyguid` = %s", $_POST['email'], $user->cguid));
    	if(!empty($is_customer)){
        	//update customer
      		$updatecommunity = array();
        	foreach($communityfields as $field){
          		if(isset($_POST[$field])){
            		$updatecommunity[$field] = $_POST[$field];
          		}
        	}

        	$db->update('community', $updatecommunity, array('guid'=>$is_customer->guid));
        	// refresh result
        	$is_customer = $db->get_row( $db->prepare("SELECT * FROM `community` WHERE `guid` = %s ", $is_customer->guid));
         	// create pastel customer array
        	$pastelcustomer = array(
          		"Name"        =>  $is_customer->descr,
          		"Email"       =>  $is_customer->email,
          		"CommunicationMethod" => 1,
          		"Active"      =>  ($is_customer->live == 1 ? true : false ),
          		"Balance"     =>  (float)$is_customer->current_balance * 100,
          		"CreditLimit"   =>  (float)$is_customer->community_limit * 100,
          		"PostalAddress01" =>  $is_customer->address_line1,
          		"PostalAddress02" =>  $is_customer->address_line2,
          		"PostalAddress03" =>  $is_customer->suburb,
          		"PostalAddress04" =>  $is_customer->city,
          		"PostalAddress05" =>  $is_customer->postal_code,
          		"DeliveryAddress01" =>  $is_customer->billing_address_line1,
          		"DeliveryAddress02" =>  $is_customer->billing_address_line2,
          		"DeliveryAddress03" =>  $is_customer->billing_suburb,
          		"DeliveryAddress04" =>  $is_customer->billing_city,
          		"DeliveryAddress05" =>  $is_customer->billing_postal_code
        	);

        	$cType = $is_customer->communitytype;
        	$meth = "Customer/Save";
        	if ($cType == 1) {
          		$meth = "Supplier/Save";
        	}

        	if(empty($is_customer->pastelid)){
        		//console($is_customer);
        		// is pastel customer - update 
          		if(!empty($site->pastelid)){
            		//$result = do_pastel_call($meth, $args, $pastelcustomer);
            		//checkSuppliers($siteGUID);
          		}
        		//console($result);
          		if( !empty( $result['ID'] )){
          			// update customer on humble
            		$db->update('community', array('pastelid'=>$result['ID']), array('guid'=>$is_customer->guid));
            		$is_customer->pastelid = $result['ID'];
          		}  
        	}else{
          		$pastelcustomer['ID'] = $is_customer->pastelid;
          		if(!empty($site->pastelid)){
            		//$result = do_pastel_call($meth, $args, $pastelcustomer);
            		//checkSuppliers($siteGUID);
          		}
        	}
        	$is_customer->message = 'OK';
        	return (array) $is_customer;
      	}
    }

    $newcommunity = array(
		'guid'		=>	$params['communityguid'],
		'companyguid'		=>	$user->cguid,
	);
    foreach($communityfields as $field){
      	if(isset($_POST[$field])){
        	$newcommunity[$field] = $_POST[$field];
      	}
    }
    $db->insert('community', $newcommunity);
	$newcommunity['message'] = 'OK';
	if ($newcommunity['communitytype'] == 0) {
		updateCustomerOnPastel($siteGUID,$newcommunity['guid']);			
	} else if ($newcommunity['communitytype'] == 1) {
		updateSupplierOnPastel($siteGUID,$newcommunity['guid']);		
	}
    /*$customer = $db->get_row( $db->prepare("SELECT * FROM `community` WHERE `guid` = %s", $params['communityguid']));
    $newpastelcustomer = array(
      	"Name"        =>  $customer->descr,
      	"Email"       =>  $customer->email,
      	"CommunicationMethod" => 1,
      	"Active"      =>  ($customer->live == 1 ? true : false ),
      	"Balance"     =>  (float)$customer->current_balance * 100,
      	"CreditLimit"   =>  (float)$customer->community_limit * 100,
      	"PostalAddress01" =>  $customer->address_line1,
      	"PostalAddress02" =>  $customer->address_line2,
      	"PostalAddress03" =>  $customer->suburb,
      	"PostalAddress04" =>  $customer->city,
      	"PostalAddress05" =>  $customer->postal_code,
      	"DeliveryAddress01" =>  $customer->billing_address_line1,
      	"DeliveryAddress02" =>  $customer->billing_address_line2,
      	"DeliveryAddress03" =>  $customer->billing_suburb,
      	"DeliveryAddress04" =>  $customer->billing_city,
      	"DeliveryAddress05" =>  $customer->billing_postal_code
    );
  	if(!empty($site->pastelid)){
    	$meth = "Customer/Save";
    	if ($customer->communitytype == 1) {
      		$meth = "Supplier/Save";
    	}
    	//$result = do_pastel_call($meth, $args, $newpastelcustomer);
    	//checkSuppliers($siteGUID);
    	if( !empty( $result['ID'] )){
    		// update customer on humble
      		$db->update('community', array('pastelid'=>$result['ID']), array('guid'=>$params['communityguid']));
    	}  
  	}*/
	return $newcommunity;
}
return array('message'=>'no data provided');