<?php
	$translate = array(
		'ecom_guid'          => 'ecomguid',
		'site_name'          => 'sitename',
		'addr1'              => 'address1',
		'addr2'              => 'address2',
		'addr3'              => 'addr3',
		'email'              => 'email',
		'tel'                => 'tel',
		'link_to_inventory'  => 'ecomlinkinventory',
		'allow_customer_reg' => 'ecomallowcustomer',
		'allow_reseller_reg' => 'ecomallowreseller',
		'theme'              => 'ecomtheme',
		'logo_ref'           => 'ecomlogo',
	);

	$result = array(
		'message' => 'Error',
	);
	foreach ($translate as $key => $value) {
		$result[$key] = '';
	}

	header('HTTP/1.0 401 Unauthorized');

	if ($_SERVER['SERVER_PORT'] == 443) {
		if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			if (preg_match('/Basic\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        		list($name, $password) = explode(':', base64_decode($matches[1]));
        		$_SERVER['PHP_AUTH_USER'] = strtoupper(strip_tags($name));
        		$_SERVER['PHP_AUTH_PW'] = strip_tags($password);
    		}	
    		$mayCheck = true;
    		$usr = $db->get_row("select * from api_users where module = 'ECOM API' and uname = '".$_SERVER['PHP_AUTH_USER']."'");
    		if (empty($usr)) { 
    			$mayCheck = false; 
    		} else {
    			if ($usr->active != 1) { $mayCheck = false; }
    			if ($usr->pass != $_SERVER['PHP_AUTH_PW']) { $mayCheck = false; }
    		}

    		if ($mayCheck) {
    			header('HTTP/1.0 200 OK');
    			$db->update("api_users",array('lastaccess' => date('Y-m-d H:i:s'),'accessed' => $usr->accessed+1),array('guid' => $usr->guid));
				$subdomain = strtoupper($params['domain']);
				$row = $db->get_row("select useecom,ecomguid,sitename,address1,address2,addr3,email,tel,ecomdomain,ecomlinkinventory,ecomallowcustomer,ecomallowreseller,ecomtheme,ecomlogo from sites where ecomdomain = '".$subdomain."'");
				if (!empty($row)) {
					if ($row->useecom == 1) {
						$result['message'] = 'OK';
						foreach ($translate as $key => $value) {
							$result[$key] = $row->$value;
						}
					}
				}
    		}
		} else {
			$result['message'] = 'No Authorization Received';
		}
	} else {
		$result['message'] = 'Not allowed through unsecure traffic';
	}
	return $result;
?>