<?php 
	$result = array(
		'message' => 'Error',
		'data' => array(),
	);

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
    			$result['message'] = 'OK';
    			$db->update("api_users",array('lastaccess' => date('Y-m-d H:i:s'),'accessed' => $usr->accessed+1),array('guid' => $usr->guid));
				$ecomguid = $params['ecomguid'];
				
				console($_POST);


    		}
		} else {
			$result['message'] = 'No Authorization Received';
		}
	} else {
		$result['message'] = 'Not allowed through unsecure traffic';
	}
	return $result;