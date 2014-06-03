<?php


function do_pastel_call( $call, $args = array(), $payload = null){
	global $app, $user;

	if(empty($user->pastel_user) || empty($user->pastel_pass)){
		return array('error' => 'pastel not set up for this user');
	}

	if(empty($call)){
		return array('error' => 'no call specified' );
	}

	$auth = 'Basic ' . base64_encode($user->pastel_user . ':' . $user->pastel_pass);

	$args = array_merge( array(
				'apikey'	=> '{'.$app['pastel']['api_key'].'}'
			), $args );
	/// SERIOUSLY?!!!! - SIGH!
	$query = str_replace( '%7D', '}', str_replace('%7B', '{', http_build_query( $args ) ) );
	
	$request = new RemoteCall($app['pastel']['endpoint'] . $call, $query, $app['debug'] );
	if(!empty($payload)){
		$request->setMethod( 'POST' );
		$request->format = 'json_encode';
		$request->contentType = 'application/json';

	}else{
		$request->setMethod( 'GET' );
	}

	$result = $request->callServer($payload , array( 'Authorization' => $auth ) );
	console($result);

	$data = json_decode( $result['body'], true );

	return $data;
}