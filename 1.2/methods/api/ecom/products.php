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
				$companyguid = $db->get_var("select coguid from sites where ecomguid = '".$ecomguid."' and useecom = 1 and live = 1");
				console($_GET);

				$crit = 'and live = 1 and producttype != 7';
				foreach ($_GET as $key => $value) {
					$key = strtoupper($key);
					$value = strtoupper($value);
					if ($key == 'DESCR') { $crit = sprintf("$crit and descr like '%%$value%%'"); }
					if ($key == 'CAT') { $crit = sprintf("%s and cat = %s",$crit,$value); }
					if ($key == 'BRAND') { $crit = sprintf("%s and brand = '%s'",$crit,$value); }
					if ($key == 'SUBTYPE') { $crit = sprintf("%s and subtype = '%s'",$crit,$value); }
				}

				$products = $db->get_results("select guid,stockcode,descr description,cat,subtype,brand,sell,vat from products where companyguid = '".$companyguid."' $crit order by weight desc limit 20");
				
				$result['data'] = $products;








    		}
		} else {
			$result['message'] = 'No Authorization Received';
		}
	} else {
		$result['message'] = 'Not allowed through unsecure traffic';
	}
	return $result;