<?php



/*
$fl = fopen('1.1/methods/dump.txt', 'w+');

ob_start();
print_r($_POST);
$out = ob_get_clean();

fwrite($fl, $out);

fclose($fl);

echo 'Thanks!';
*/


define("NUL", chr(0));
define("EOT", chr(4));
define("ENQ", chr(5));
define("HT", chr(9));
define("LF", chr(10));
define("FF", chr(12));
define("CR", chr(13));
define("DLE", chr(16));
define("DC4", chr(20));
define("CAN", chr(24));
define("ESC", chr(27));
define("FS", chr(28));
define("GS", chr(29));


for($looper = 0; $looper < 1; $looper++){
//dump($params);
$sale = $db->get_row("SELECT * FROM `sh` WHERE `guid` = '".$params['guid']."'");
if(empty($sale)){
	return;
}

$site = $db->get_row("SELECT * FROM `sites` WHERE `guid` = '".$sale->siteguid."'");
$user = $db->get_row("SELECT * FROM `users` WHERE `uguid` = '".$sale->cashier."'");
$agent = $db->get_row("SELECT * FROM `users` WHERE `uguid` = '".$sale->agent."'");
$lines = $db->get_results("SELECT * FROM `sl` WHERE `guid` = '".$sale->guid."'");

//dump($sale);

$types = array();
foreach($lines as $line){
	$types[$line->basketguid][] = $line;
}


if(!$fp = fsockopen('tcp://'.$_SERVER['REMOTE_ADDR'], 9100)){
	return(array('error'));
}


fwrite($fp, ESC."@");

if( floatval( $sale->cash ) ){
	// Cash Drawer --- IF CASH only
	fwrite($fp, ESC."p07Q");
}


// Set LEft
fwrite($fp, ESC."a0");

// HEad Space
fwrite($fp, LF);
// center
fwrite($fp, ESC."a1");
// Title
fwrite($fp, "TAX INVOICE\n");
// left
fwrite($fp, ESC."a0");
// -------
fwrite($fp, "------------------------------------------------\n");
// Set Bold
fwrite($fp, ESC."E1");
// Site Name
fwrite($fp, $site->sitename."\n");
// Set Nomal
fwrite($fp, ESC."E2");
// Vat Nr
fwrite($fp, "VAT Nr: ".$site->vatnr."\n");
// Tel Nr
fwrite($fp, "Tel Nr: ".$site->tel."\n");
// Fax Nr
fwrite($fp, "VAT Nr: ".$site->fax."\n");
// Email Nr
fwrite($fp, "Email: ".$site->email."\n");
// Cashier
fwrite($fp, "Cashier: ".$user->fname." ".$user->sname."\n");
// Cashier
fwrite($fp, "Sale Assistant: ".$agent->fname." ".$agent->sname."\n");

// INV GIUID
fwrite($fp, "Inv GUID: ".$params['guid']."\n");
// INV Date
fwrite($fp, "Date: ".$sale->datetime."\n");
// -------
fwrite($fp, "------------------------------------------------\n");

/// SALES LINES
foreach($types as $type=>$lines){

	// get type
	$type= $db->get_var("SELECT `title` FROM `saleTypes` WHERE `guid` = '".$lines[0]->saletype."';");
	// Set Bold
	fwrite($fp, ESC."E1");
	// Sale Type
	fwrite($fp, $type."\n");
	// Set Nomal
	fwrite($fp, ESC."E2");

	foreach($lines as $line){

		//get product		
		$product= $db->get_row("SELECT * FROM `products` WHERE `guid` = '".$line->productguid."';");


		// descr
		fwrite($fp, "  ".$product->descr."\n");
		// if serial
		if(!empty($line->serial)){
			fwrite($fp, "  ".$line->serial."\n");
		}
		
		// qty / sell
		fwrite($fp, "  ".$line->qty." / ". money_format('%i', $line->sell / $line->qty ) . "\n");

		// Right
		fwrite($fp, ESC."a2");

		// Set Bold
		fwrite($fp, ESC."E1");
		// PRICE TOtal
		fwrite($fp, money_format('%i', $line->sell) . "\n");
		// Set Bold
		fwrite($fp, ESC."E2");

		// Set LEft
		fwrite($fp, ESC."a0");

	}
}

// -------
fwrite($fp, "------------------------------------------------\n");

// Totals

// Right
fwrite($fp, ESC."a2");

// EXCL
fwrite($fp, "Excl: ".$sale->excl."\n");
// VAT
fwrite($fp, "VAT: ".$sale->vat."\n");
// Set Bold
fwrite($fp, ESC."E1");
// INCL
fwrite($fp, "Incl: ".$sale->incl."\n");
// Set Bold
fwrite($fp, ESC."E2");


// -------
fwrite($fp, "------------------------------------------------\n");

// EXCL
fwrite($fp, "Cash Tendered: ".$sale->cash."\n");
// VAT
fwrite($fp, "Total Tendered: ".$sale->tender_total."\n");
// Set Bold
fwrite($fp, ESC."E1");
// INCL
fwrite($fp, "Change: ".$sale->tender_change."\n");
// Set Bold
fwrite($fp, ESC."E2");

// -------
fwrite($fp, "------------------------------------------------\n");

// Left
fwrite($fp, ESC."a0");


// copy print
if($looper === 1){
	fwrite($fp, ESC."a1");

	fwrite($fp, " ** MERCHANT COPY ** \n");
	// -------
	fwrite($fp, "------------------------------------------------\n");
	// Left
	fwrite($fp, ESC."a0");

}


// End Space
fwrite($fp, LF.LF.LF.LF);

// Cut
fwrite($fp, GS."V1");

// End
fwrite($fp, ESC."@");


fclose($fp);

}