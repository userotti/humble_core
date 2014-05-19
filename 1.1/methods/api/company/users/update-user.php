<?php
/*

Caldoza Engine ------------------------

File	:	api/company/users/update-user.php
Created	: 	2013-12-17

*/


console($_POST);

if (!empty($_POST['cashierpin']) && !empty($params['userguid'])) {
	$cashierpin = $_POST['cashierpin'];
	$uguid = $params['userguid'];
}



$userfields = array(
	'fname',
	'sname',
	'email',
	'pword',
	'cashiercode',
	'cashierpin',
	'lastsite',
	'lastsystem',
	'lastlogin',
	'tickets',
	'community',
	'products',
	'reports',
	'settings',
	'claims',
	'cellnr',
	'live',
	'basket',
	'move',
	'users',
	'general',
);

if(!empty($params['userguid'])){


    $selectuser = $db->get_row($db->prepare("

    SELECT
        *
    FROM
        `users`
    WHERE
        `cguid` = %s
    AND `uguid` = %s
        ", $user->cguid,  $params['userguid']), ARRAY_A);

    if(!empty($selectuser)){
    	foreach($userfields as $field){
    		if(isset($_POST[$field])){
    			$selectuser[$field] = $_POST[$field];
    		}
    	}

    	// update 
    	$db->update('users', $selectuser, array('uguid'=>$params['userguid'], 'cguid' => $user->cguid));
    	$db->delete('confirmed', array('itemGUID'=>$params['userguid']));

    	$selectuser['message'] = 'OK';
    	$selectuser['guid'] = $params['userguid'];
    	return $selectuser;
    }
}
if(!empty($_POST)){
// fall through to insert
	// new user
	if($params['userguid'] == 'new'){
		$params['userguid'] = gen_uuid();
	}
	$newuser = array(
		'uguid'		=>	$params['userguid'],
		'cguid'		=>	$user->cguid,
	);
	foreach($userfields as $field){
		if(isset($_POST[$field])){
			$newuser[$field] = $_POST[$field];
		}
	}

	$db->insert('users', $newuser);
	$newuser['message'] = 'OK';
	$newuser['guid'] = $params['userguid'];


	return $newuser;
}
