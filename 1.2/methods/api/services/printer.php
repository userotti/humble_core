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

global $slip;

$slip = null;

function push($line){
	global $slip;

	$slip .= $line;

};


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


if(!empty($_POST['small_print'])){
	define("line", "--------------------------------------------------------\n");
}else{
	define("line", "------------------------------------------------\n");
}



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

/*
if(!$fp = fsockopen('tcp://'.$_SERVER['REMOTE_ADDR'], 9100)){
	return(array('error'));
}
*/

push(ESC."@");

// TINY
if(!empty($_POST['small_print'])){
	push(ESC."M1");
}
//console($_POST);

// Set LEft
push(ESC."a0");

// HEad Space
push(LF);
// center
push(ESC."a1");
// Title
push("TAX INVOICE\n");
// left
push(ESC."a0");
// -------

// line break
push(line);

// Set Bold
push(ESC."E1");
// Site Name
push($site->sitename."\n");
// Set Nomal
push(ESC."E2");
// Vat Nr
if($site->vatnr != 'N/A'){
	push("VAT Nr: ".$site->vatnr."\n");
}
// Tel Nr
if($site->tel != 'N/A'){
	push("Tel Nr: ".$site->tel."\n");
}
// Fax Nr
if($site->fax != 'N/A'){
	push("Fax Nr: ".$site->fax."\n");
}
// Email Nr
if($site->email != 'N/A'){
	push("Email: ".$site->email."\n");
}
// Cashier
push("Cashier: ".$user->fname." ".$user->sname."\n");
// Cashier
push("Sale Assistant: ".$agent->fname." ".$agent->sname."\n");

// INV GIUID
push("Inv GUID: ".$params['guid']."\n");
// INV Date
push("Date: ".$sale->datetime."\n");
// -------
// line break
push(line);


/// SALES LINES
foreach($types as $type=>$lines){

	// get type
	$type= $db->get_var("SELECT `title` FROM `saleTypes` WHERE `guid` = '".$lines[0]->saletype."';");
	// Set Bold
	push(ESC."E1");
	// Sale Type
	push($type."\n");
	// Set Nomal
	push(ESC."E2");

	foreach($lines as $line){

		//get product		
		$product= $db->get_row("SELECT * FROM `products` WHERE `guid` = '".$line->productguid."';");


		// descr
		push("  ".$product->descr."\n");
		// if serial
		if(!empty($product->si)){
			push("  ".$line->serial."\n");
		}
		
		// qty / sell
		push("  ".$line->qty." / ". money_format('%i', $line->sell / $line->qty ) . "\n");

		// Right
		push(ESC."a2");

		// Set Bold
		push(ESC."E1");
		// PRICE TOtal
		push(money_format('%i', $line->sell) . "\n");
		// Set Bold
		push(ESC."E2");

		// Set LEft
		push(ESC."a0");

	}
}

// -------
// line break
push(line);

// Totals

// Right
push(ESC."a2");

// EXCL
push("Excl: ".$sale->excl."\n");
// VAT
push("VAT: ".$sale->vat."\n");
// Set Bold
push(ESC."E1");
// INCL
push("Incl: ".$sale->incl."\n");
// Set Bold
push(ESC."E2");


// -------
// line break
push(line);

// EXCL
if( floatval( $sale->cash ) ){
	push("Cash Tendered: ".$sale->cash."\n");
}
if( floatval( $sale->ccard ) ){
	push("Credit Card: ".$sale->ccard."\n");	
}
if( floatval( $sale->dcard ) ){
	push("Debit Card: ".$sale->dcard."\n");	
}
if( floatval( $sale->acc ) ){
	push("Account: ".$sale->acc."\n");	
}
// VAT
push("Total Tendered: ".$sale->tender_total."\n");
// Set Bold
push(ESC."E1");
// change
if($sale->tender_change != '0.00'){
	push("Change: ".$sale->tender_change."\n");
}
// Set Bold
push(ESC."E2");

// -------
// line break
push(line);


if($site->slipline1 != 'N/A'){
	// center
	push(ESC."a1");

	// print line
	push($site->slipline1."\n");
}
if($site->slipline2 != 'N/A'){

	// print line
	push($site->slipline2."\n");
}
if($site->slipline3 != 'N/A'){

	// print line
	push($site->slipline3."\n");
}

// Left
push(ESC."a0");


// copy print
if($looper === 1){
	push(ESC."a1");

	push(" ** MERCHANT COPY ** \n");
	// -------
	// line break
	push(line);
	// Left
	push(ESC."a0");

}


// End Space
push(LF.LF.LF.LF);

// Cut
push(GS."V1");

// End
push(ESC."@");

$out['message'] = 'OK';
$out['slip'] = $slip;
if( floatval( $sale->cash ) ){
	// Cash Drawer --- IF CASH only	
	$out['pop'] = ESC."p07Q";
}



return $out;
//fclose($fp);

}