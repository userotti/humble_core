<?php
/*

Caldoza Engine ------------------------

File	:	support/lost-pass.php
Created	: 	2014-01-15

*/

if(!empty($_POST)){

	if(!empty($_POST['email'])){
		// checks a user exists
		$user = $db->get_row( $db->prepare( 'SELECT * FROM `users` WHERE `email` = %s', $_POST['email'] ) );
		if(empty($user)){
			return array('error' => 'User not found.');
		}else{


			$mail = new PHPMailer;

			$mail->isSMTP();
			$mail->Host = '127.0.0.1';
			$mail->From = 'noreply@humble.co.za';
			$mail->FromName = 'humble Agent';
			$mail->addAddress($user->email, $user->fname.' '.$user->sname);

			//$mail->addStringAttachment($file, $key . '.txt');

			$mail->Subject = '[humble] Password Recovery';
			$mail->Body    = "Hi ".$user->fname."\r\n";
			$mail->Body    .= "Your login details as requested.\r\n";
			$mail->Body    .= "Email: ".$user->email."\r\n";
			$mail->Body    .= "Password: ".$user->pword."\r\n";
			$mail->Body    .= "Cashier PIN: ".$user->cashierpin."\r\n";
			$mail->Body    .= "\r\n";
			$mail->Body    .= "\r\n";
			$mail->Body    .= "~ humble Support\r\n";

			if($mail->send()) {
				// no send error

				return array('message' => 'OK');
			}
		}
	}

}
