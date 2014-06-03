<?php
/*

Caldoza Engine ------------------------

File	:	templates/lock-till.php
Created	: 	2013-12-04

*/

if(!empty($_GET['pin'])){

	if($user->cashierpin === $_GET['pin']){
		$db->update('login', array('locked' => 0), array('tokenGUID' => $params['token']));
		return array('message'=>'OK');
	}

	return array('message'=>'Your Cashier PIN Is Incorrect');
}


$db->update('login', array('locked' => 1), array('tokenGUID' => $params['token']));

?>