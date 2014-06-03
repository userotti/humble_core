<?php

function console($a, $trace = false){
    //$trace = true;
    ob_start();
    print_r($a);    
    if(!empty($trace)){
        $trace = debug_backtrace();
        echo "\nTrace:\n";
        foreach($trace as $step){
            echo "File: ". str_replace(ABSPATH, '', $step['file'])."\r\n";
            echo "Line: ". $step['line'] ."\r\n";
            echo "Function: ". $step['function'] ."\r\n";
        }

    }
    error_log( ob_get_clean() );

}

function dump($a, $d = true){
	echo '<pre id="varDump">';
    if(is_array($a) || is_object($a)){
	   print_r($a);
    }else{
        print_r($a);
    }
	echo '</pre>';
	if(!empty($d))
		die;
}

function sanitize_file_name( $filename ) {
	$filename_raw = $filename;
	$special_chars = array("?", "[", "]", "./", "../", "/..", "/.", "..", ".", "//", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
	$filename = str_replace($special_chars, '', $filename);
	$filename = preg_replace('/[\s-]+/', '-', $filename);
	$filename = trim($filename, '.-_');

	// Split the filename into a base and extension[s]
	$parts = explode('.', $filename);

	// Process multiple extensions
	$filename = array_shift($parts);
	$extension = array_pop($parts);

	return $filename;
}

function sanitize_key( $key ) {
	$raw_key = $key;
	$key = strtolower( $key );
	$key = preg_replace( '/[^a-z0-9_\-]/', '_', $key );
	return $key;
}
function reduce_value( $key ) {
    $raw_key = $key;
    $key = strtolower( $key );
    $key = preg_replace( '/[^a-z0-9_\-]/', '', $key );
    return $key;
}

function gen_uuid($type='long') {
    if($type == 'long'){
    return strtoupper(sprintf( '%04x%04x-%04x-%04x-%04x%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    ));    
    }else{
        return strtoupper(sprintf( '%04x%04x',

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x0fff ) | 0x8000
    ));
    }
}

function get_cached_object($object_key){
    global $db;

    $object = $db->get_var("SELECT `data` FROM `cache_objects` WHERE `cacheguid` = '".$object_key."';");

    if(empty($object)){
        error_log('"'.$object_key.'" is not a valid cache GUID on '. __FILE__);
        return false;
    }
    if( $data = @unserialize($object)){
        return $data;
    }
    
    return $object;
}
function delete_cached_object($object_key){
    global $db;
    $db->delete( 'cache_objects', array( 'cacheguid' => $object_key ) );

    return;
}

function set_cached_object($data){
    global $user, $db;

    // remove old objects
    $db->query( "DELETE FROM `cache_objects` WHERE `date_set` < '".date('Y-m-d H:i:s', strtotime('yesterday'))."';" );

    $object_key = gen_uuid();
    if(is_array($data) || is_object($data)){
        $data = serialize($data);
    }
    $db->insert( 'cache_objects', array( 'cacheguid' => $object_key, 'data'=> $data ) );
    return $object_key;
}


function do_pastel_call( $call, $args = array(), $payload = null, $auth = null){
    global $app, $user, $db;

    if(empty($auth)){
        if (empty($user->siteguid) && !empty($user->lastsite)) {

            $user->siteguid = $user->lastsite;
        }
        $auth = $db->get_var("SELECT `pastelhash` FROM `sites` WHERE `guid` = '".$user->siteguid."' AND `coguid` = '".$user->cguid."'; ");
        if(empty($auth)){
            if(empty($user->pastel_user) || empty($user->pastel_pass)){
                return array('error' => 'pastel not set up for this user');
            }
            
            $auth = base64_encode($user->pastel_user . ':' . $user->pastel_pass);
        }
    }

    if(empty($call)){
        return array('error' => 'no call specified' );
    }

    $auth = 'Basic ' . $auth;

    $args = array_merge( array(
                'apikey'    => '{'.$app['pastel']['api_key'].'}'
            ), $args );
    /// SERIOUSLY?!!!! - SIGH!
    $query = str_replace('%24', '$', str_replace( '%7D', '}', str_replace('%7B', '{', http_build_query( $args ) ) ) );
    
    $request = new RemoteCall($app['pastel']['endpoint'] . $call, $query, $app['debug'] );
    if(!empty($payload)){
        $request->setMethod( 'POST' );
        $request->format = 'json_encode';
        $request->contentType = 'application/json';

    }else{
        $request->setMethod( 'GET' );
    }
    //console($payload);
    $result = $request->callServer($payload , array( 'Authorization' => $auth ) );
    //console($result);

    $data = json_decode( $result['body'], true );
    if(empty($data)){        
        return $result['body'];
    }
    return $data;
}

function do_desk_call($call, $args = array(), $payload = null, $auth = null){
    global $app, $user, $db;

    if(empty($auth)){
        $auth = base64_encode($app['desk']['deskuser'].':'.$app['desk']['deskpass']);
    }
    if(empty($call)){
        return array('error' => 'no call specified' );
    }
    $auth = 'Basic ' . $auth;

    //$args = array_merge(array('apikey' => '{'.$app['desk']['api_key'].'}'),$args);
    $query = str_replace('%24', '$', str_replace( '%7D', '}', str_replace('%7B', '{', http_build_query( $args ) ) ) );
    
    $request = new RemoteCall($app['desk']['endpoint'] . $call, $query, $app['debug'] );
    if(!empty($payload)){
        $request->setMethod( 'POST' );
        $request->format = 'json_encode';
        $request->contentType = 'application/json';
    }else{
        $request->setMethod( 'GET' );
    }
    $result = $request->callServer($payload,array('Authorization' => $auth));
    $data = json_decode( $result['body'], true );
    if(empty($data)){        
        return $result['body'];
    }
    return $data;
}








?>