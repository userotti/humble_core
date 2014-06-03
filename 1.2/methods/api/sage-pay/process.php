<?php

/// PROCESSES A SAGE PAY INSTRUCTION
/*
$lines = $db->get_results("SELECT * FROM `sagepay` WHERE `sent` = '0' ;", ARRAY_A);

if(empty($lines)){
	return;
}

// Group Batches
$batches = array();
foreach($lines as &$line){
	
	$index = reduce_value( !empty($line['actiondate']) ? date('Y/m/d', strtotime($line['actiondate'])) : date('Y/m/d') );
	if(!isset($batches[$index])){
		$batches[$index] = array(
			'batch_number'		=>	gen_uuid(),
			'action_date'		=>	!empty($line['actiondate']) ? date('Y/m/d', strtotime($line['actiondate'])) : date('Y/m/d'),
			'batch_total'		=> 	0,
			'lines'				=>	array()
		);
	}
	// add line
	$batches[$index]['lines'][] = $line;


};

foreach ($batches as $key => $batch) {

	// Build HEADER
	$package['head'] = array(
		'username'		=>	'"van555595"',
		'password'		=>	'"357359"',
		'pin'			=>	'"196951"',
		'instruct'		=>	'"D"',
		'forward'		=>	'"1"',
		'jump'			=>	'"1"'
	);

	// Build Batch Line
	$package['batch'] = array(
		'batch_number'	=>	'"'.$batch['batch_number'].'"',
		'action_date'	=>	'"'.$batch['action_date'].'"'
	);
	// Build LINES
	foreach($batch['lines'] as $index=>&$line){
		// Bank Line
		$package['line_' . $index] = array(
			'acc_ref'		=>	'"'.$line['accref'].'"',
			'acc_name'		=>	'"'.$line['accname'].'"',
			'bank_acc_name'	=>	'"'.$line['bankaccname'].'"',			
			'acc_type'		=>	'"'.$line['acctype'].'"',
			'branch_code'	=>	'"'.$line['branchcode'].'"',
			'acc_number'	=>	'"'.$line['accnr'].'"',
			'batch_amount'	=>	'"'.$line['batchamount'].'"',
			'email_address'	=>	'"'.$line['emailaddress'].'"',
			'cc_name'		=>	'"'.$line['ccname'].'"',
			'cc_token'		=>	'"'.$line['cctoken'].'"',
			'exp_month'		=>	'"'.$line['expmonth'].'"',
			'exp_year'		=>	'"'.$line['expyear'].'"',
			'exp_mask'		=>	'"'.$line['ccmasknr'].'"',
			'extra1'		=>	'"'.$line['extra1'].'"',
			'extra2'		=>	'"'.$line['extra2'].'"',
			'extra3'		=>	'"'.$line['extra3'].'"',
			'statement_ref'	=>	'"'.$line['stateref'].'"',
			'naedo'			=>	'"'.$line['naedo'].'"',
		);
		// add totals
		$batch['batch_total'] += $line['batchamount'];
	}

	// Build FOOTER

	$package['foot'] = array(
		"end"		=>	'"##END##"',
		"total"		=>	'"'.$batch['batch_total'].'"'
	);

	$file = null;
	foreach($package as $row){
		$file .= implode(',', $row) . "\r";
	}
	

	/// REPLACE LATER WITH FTP
	// create mail object
	$mail = new PHPMailer;

	$mail->isSMTP();
	$mail->Host = '127.0.0.1';
	$mail->From = 'noreply@humble.co.za';
	$mail->FromName = 'humble mailer (humble notifier)';
	$mail->addAddress('will@humble.co.za', 'Will');
	$mail->addAddress('david@humble.co.za', 'David');
	$mail->addAddress('rodney@humble.co.za', 'Rod');

	$mail->addStringAttachment($file, $key . '.txt');

	$mail->Subject = 'Sage Pay - file process';
	$mail->Body    = 'Process file attached';

	if(!$mail->send()) {
		// no send error
		dump( $mail->ErrorInfo ,0 );
	}else{
		foreach($batch['lines'] as $index=>&$line){
			$db->update('sagepay', array('actiondate' => $batch['action_date'], 'sent' => 1, 'batchnr' => $batch['batch_number']), array('guid' => $line['guid']));
		}	
	}
*/

}



















