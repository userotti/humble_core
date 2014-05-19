<?php



if(!empty($_POST['email']) && !empty($_POST['pass'])){
	$auth = base64_encode($_POST['email'] . ':' . $_POST['pass']);
	$return = do_pastel_call( 'company/get', array(), null, $auth );

	if(!empty($return)){
		$return['hash'] = $auth;
		return $return;
	}else{
		return array('error'=>'Invalid Details');
	}
}


return do_pastel_call( 'company/get' );
