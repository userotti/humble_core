<?php
/*

Caldoza Engine ------------------------

File	:	site/login.php
Created	: 	2014-01-15

*/


	//get user by key
	$user = $db->get_row( $db->prepare( "
		SELECT 
			*
		FROM
			`users`
		WHERE
			`email` = %s
		LIMIT 1", $_POST['email'] ) );

	if(empty($user)){
		$login['result'] = 'User Does Not Exist';
		$db->insert('login', $login);
		return array('message'=>$login['result']);
	}
	if($user->pword !== $_POST['password']){
		if($user->pword !== sha1($_POST['password'])){
			$login['result'] = 'Your Password Is Not Correct';
			$db->insert('login', $login);
			return array('message'=>$login['result']);
		}
	}
	if((int) $user->live !== 1){
		$login['result'] = 'User Is Not Active';
		$db->insert('login', $login);
		return array('error'=>$login['result']);
	}
	
return $user;

?>