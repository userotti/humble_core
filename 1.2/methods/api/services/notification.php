<?php

	

	if (!empty($user)) {
		$siteguid = $user->siteguid;
		$companyguid = $user->cguid;
	} else {
		$siteguid = '';
		$companyguid = '';
	}
	
	if(!empty($user->siteguid)){
		$site = $db->get_row("SELECT * FROM `sites` WHERE `guid` = '".$user->siteguid."';");
	}

	if(!empty($_POST['community'])){
		$customer = $db->get_row( $db->prepare("SELECT * FROM `community` WHERE `guid` = %s ;", $_POST['community']), ARRAY_A);
		if(!empty($customer)){
			if(empty($_POST['recipient_email'])){
				$_POST['recipient_email'] = $customer['email'];
			}
			if(empty($_POST['recipient_name'])){
				$_POST['recipient_name'] = $customer['descr'];
			}
			$bcc = true;
		}
		
	}
	
	$email = strtoupper($_POST['recipient_email']);
	if (empty($email)) { $email = ''; }
	$emailname = strtoupper($_POST['recipient_name']);
	if (empty($emailname)) { $emailname = $email; }
	if (!empty($_POST['invoice'])) {
		$saleguid = $_POST['invoice'];
	} else {
		$saleGUID = '';
	}
	if (empty($saleguid)) { $saleguid = ''; }
	$communityguid = $db->get_var("select guid from community where upper(email) = '".$email."' and companyguid = '".$companyguid."'");
	if (empty($communityguid)) {
		$communityguid = gen_uuid();
		$community = array(
			'guid'          => $communityguid,
			'companyguid'   => $companyguid,
			'descr'         => $emailname,
			'communitytype' => 0,
			'email'         => $email,
		);
		if (!empty($companyguid)) {
			$db->insert("community",$community);
		}
	}
	$rec = array(
		'guid'          => gen_uuid(),
		'saleguid'      => $saleguid,
		'email'         => $email,
		'emailname'     => $emailname,
		'communityguid' => $communityguid,
	);
	$db->insert('email_receipts',$rec);



	if(empty($_POST['recipient_email'])){
		return array('message'=>'recipient email address is required');
	}
	if(empty($_POST['recipient_name'])){
		return array('message'=>'recipient name is required');
	}
	if(empty($_POST['message'])){
		return array('message'=>'a message is required');
	}

	$defaults = array(
		'sender_email'		=>	'noreply@humble.co.za',
		'sender_name'		=>	(!empty($site->sitename) ? $site->sitename : 'humble notifications'),
		'subject'			=>	'notification'
	);

	$email = array_merge($defaults, $_POST);

	// create mail object
	$mail = new PHPMailer;

	$mail->isSMTP();
	$mail->Host = '127.0.0.1';
	$mail->From = $email['sender_email'];
	$mail->FromName = $email['sender_name'];
	$mail->addAddress($email['recipient_email'], $email['recipient_name']);

	//$mail->addAddress('ellen@example.com');               // Name is optional
	//$mail->addReplyTo('info@example.com', 'Information');
	//$mail->addCC('cc@example.com');
	
	if(!empty($bcc)){
		$mail->addBCC($site->email, $site->sitename);
	}

	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters

	//addStringAttachment
	//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $email['subject'];
	$mail->Body    = $email['message'];
	
	if(!empty($email['text'])){
		$mail->AltBody = $email['message'];
	}

	if(!empty($_POST['invoice'])){		
		$mail->addStringAttachment(file_get_contents("http://api.humble.co.za/1.1/service/invoice-pdf/".$_POST['invoice']), 'invoice.pdf');
	}
	if(!empty($_POST['order'])){		
		$mail->addStringAttachment(file_get_contents("http://api.humble.co.za/1.1/service/order-pdf/".$_POST['order']), 'order.pdf');
	}
	if(!empty($_POST['file_data'])){
		$mail->addStringAttachment($_POST['file_data'], 'file.txt');
	}

	if(!empty($_FILES)){
		foreach($_FILES as $file){
			if(is_array($file['name'])){
				// multiple attachements
				foreach($file['name'] as $filekey=>$filename){
					$mail->addAttachment($file['tmp_name'], $filename);
				}
			}else{
				// single attachment
				$mail->addAttachment($file['tmp_name'], $file['name']);
			}
		}
	}


	if(!$mail->send()) {
		// no send error
		return array('message' => 'ERROR', 'error' => $mail->ErrorInfo);
	}else{
		// completed send	
		return array('message' => 'OK');
	}


	




