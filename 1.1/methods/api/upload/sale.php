<?php
/*

Caldoza Engine ------------------------

File	:	api/upload/sale.php
Created	: 	2013-12-17

*/



$siteguid = $user->siteguid;
$pastelid = $db->get_var("select pastelid from sites where guid = '".$siteguid."'");




if(isset($_POST['data'])){
	$data = json_decode( $_POST['data'], true );
}else{
	$data = $_POST;
}


$saleguid = $data['header']['guid'];


//if(!empty($user->pas) && !empty($user->pastel_pass)){
	$site = $db->get_row( "SELECT * FROM `sites` WHERE `guid` = '".$user->siteguid."';" );
	if(!empty($site->pastelid) && !empty($site->pastelhash)){
		$p_args = array( 
			'companyid'	=> $site->pastelid
		);
	}
	//$p_suppliers = do_pastel_call('supplier/get', $args );
//}


// header
if ( !empty( $data['header'] ) ) {
	
	// check missing stuff and fix

	if(empty($data['header']['datetime'])){
		$data['header']['datetime'] = date('Y-m-d H:i:s');
	}
	if(empty($data['header']['siteguid'])){
		$data['header']['siteguid'] = $user->siteguid;
	}
	if(empty($data['header']['cashier'])){
		$data['header']['cashier'] = $user->userGUID;
	}
	if(empty($data['header']['agent'])){
		$data['header']['agent'] = $user->userGUID;
	}
	if(empty($data['header']['deviceguid'])){
		$data['header']['deviceguid'] = $user->deviceGUID;
	}
	if(empty($data['header']['devicename'])){
		$data['header']['devicename'] = $user->deviceName;
	}
	if(empty($data['header']['customertaxnumber'])){
		$data['header']['customertaxnumber'] = 'N/A';
	}

	$db->insert( 'sh', $data['header'] );



	if(empty($data['tenders'][0])){
		// get the cash sale
		//$site->pastelhash
		$query = "SELECT
			`pasteltranslate`.`pastelid`

		FROM `humble_dev`.`community` AS `community`
		LEFT JOIN `humble_dev`.`pasteltranslate` AS `pasteltranslate` ON (`community`.`guid` = `pasteltranslate`.`itemguid` AND `pasteltranslate`.`siteguid` = '".$user->siteguid."')

		WHERE `community`.`communitytype` = 2 AND `community`.`companyguid` = '".$user->cguid."' LIMIT 1;";

		$pastelid = $db->get_var( $query );
		//$pastelid = 1264466;
	}else{
		//$pastelid = $db->get_var($db->prepare('SELECT `pastelid` FROM `community` WHERE `guid` = %d;', $data['tenders'][0]['communityguid']));
		
	}

	if(!empty($pastelid)){
		$pqty = 0;
		$customer_invoice = array(
			"DueDate"				=> date('Y-m-d H:i:s'),
			"CustomerId"			=> $pastelid,
			"Date"					=> date('Y-m-d H:i:s'),
			"Inclusive"				=> false,
			//"DiscountPercentage"	=> 0.1,
			//"TaxReference"			=> "",
			"DocumentNumber"		=> $data['header']['guid'], 
			"Reference"				=> $data['header']['deviceguid'],
			//"Message"				=> “”,
			//"Discount"				=> 7200,
			"Exclusive"				=> $data['header']['excl'],
			"Tax"					=> $data['header']['vat'],
			"Rounding"				=> 0,
			"Total"					=> $data['header']['tender_total'],
			"AmountDue"				=> $data['header']['tender_total'],
			//"PostalAddress01"		=> "P O Box 39478", 
			//"PostalAddress02"		=> "Sandton", 
			//"PostalAddress03"		=> "2039", 
			//"PostalAddress04"		=> "",
			//"PostalAddress05"		=> "",
			//"DeliveryAddress01"		=> "Morningview Park",
			//"DeliveryAddress02"		=> "Rivonia Road",
			//"DeliveryAddress03"		=> "Sandton",
			//"DeliveryAddress04"		=> "2303",
			//"DeliveryAddress05"		=> "",		
		);
	}
}


// lines
if ( !empty( $data['lines'] ) ) {
	foreach ( $data['lines'] as $line ) {
		//$line['productguid']
		if($line['qty'] > 0){
			// Check for current stocktake
			$takeid = $db->get_var("SELECT `guid` FROM `stocktake_log` WHERE `siteguid` = '".$user->siteguid."' AND `enddate` IS NULL;");

			// if there is a take in action add the sale line to the count if there is currently on hand
			if(!empty($takeid)){
				
				// check to see if the product is a counted item			
				$counted = $db->get_var("SELECT SUM(`qty`) FROM `stocktake_counted` WHERE `productguid` = '".$line['productguid']."' AND `takeguid` = '".$takeid."';");
				
				/*ob_start();	
				dump($db,0);
				dump("SELECT `guid` FROM `stocktake_counted` WHERE `productguid` = '".$line['productguid']."' AND `takeguid` = '".$takeid."';",0);
				dump($takeid,0);
				$debug = ob_get_clean();
				$db->insert('debugnotes', array('message'=>$debug));*/





				if(!empty($counted)){
					$reduce = $line['qty']-$counted;
					
					if($reduce > 0){
						$reduce = '-'.$counted;
					}else{
						$reduce = '-'.$line['qty'];
					}
					
					$newitem = array(
						'guid' => $data['header']['guid'],
						'siteguid' => $user->siteguid,
						'productguid' => $line['productguid'],
						'userguid' => $user->userGUID,
						'takeguid'=> $takeid,
						'qty'=> $reduce,
						'imei'=> $line['imei'],
					);
					$db->insert('stocktake_counted', $newitem);
				}
			}
		}

		// update product weight
		$product = $db->get_row( $db->prepare( "SELECT `products`.*, `pasteltranslate`.`pastelid` FROM `products` LEFT JOIN `pasteltranslate` ON (`products`.`guid` = `pasteltranslate`.`itemguid` AND `pasteltranslate`.`siteguid` = '".$user->siteguid."') WHERE `products`.`guid` = %s;", $line['productguid'] ), ARRAY_A );
		$weight = $db->get_var( $db->prepare( "SELECT `weight` FROM `products` WHERE `guid` = %s;", $line['productguid'] ) );
		$description = $db->get_var( $db->prepare( "SELECT `descr` FROM `products` WHERE `guid` = %s;", $line['productguid'] ) );


		/// Fix Missing Details
		// VAT
		if(empty($line['vat']) && !empty( $line['cost'] ) && !empty( $line['sell'] ) ){
			// check if its a taxable product
			if(!empty($product['vat']) && !empty( $product['vat'])){
				$product['vat'] = floatval($product['vat']);
				if(!empty( $product['vat'] )){
					$line['vat'] = round( $line['sell'] / ( 100 / $product['vat'] ) , 2 );
				}
			}
		}



		$db->update( 'products', array( 'weight'=>( $weight+$line['qty'] ) ), array( 'guid'=>$line['productguid'] ) );
		$db->delete( 'confirmed', array( 'itemGUID' => $line['productguid'] ) );
		$db->insert( 'sl', $line );
		if(isset($pqty)){
			$pqty += $line['qty'];
		}
		$movement = array(
			'siteguid'		=>	$user->siteguid,
			'productguid'	=>	$line['productguid'],
			'qty'			=>	abs($line['qty']),
			'movedir'		=>	($line['qty'] < 0 ? 1 : -1),
			'movetype'		=>	($line['qty'] < 0 ? 7 : 8),
			'moveguid'		=>	$line['guid'],
			'moveline'		=>	$line['line'],
			'guid'			=>	gen_uuid()
		);
		$db->insert('movement', $movement);


		if(!empty($customer_invoice)){
			$customer_invoice['Lines'][] = array(

				"LineType"				=> 0,
				"SelectionId"			=> $product['pastelid'],
				"TaxTypeId"				=> 1,
				"Description"			=> $description,
				"Quantity"				=> $line['qty'],
				"UnitPriceExclusive"	=> ( ($line['sell']-$line['vat']) / abs($line['qty']) ),
				"TaxPercentage"			=> 0.14,
				//"DiscountPercentage"	=> 0,
				"Exclusive"				=> ($line['sell']-$line['vat'])

			);
		}

	}
}
// tenders
if ( !empty( $data['tenders'] ) ) {
	
	foreach ( $data['tenders'] as $tender ) {
		// cleanup
		if(empty($tender['tenderguid'])){
			$tender['tenderguid'] = gen_uuid();
		}
		if(empty($tender['tenderdate'])){
			$tender['tenderdate'] = date('Y-m-d H:i:s');
		}

		$db->insert( 'tenders', $tender );
	}
}

if(!empty($customer_invoice) && !empty($p_args)){
	if($pqty < 0 ){
		$return = do_pastel_call('CustomerReturn/Save', $p_args, $customer_invoice );
		
		if(isset($return['ID'])){

			$translate = array(
				'siteguid'	=>	$user->siteguid,
				'itemguid'	=>	$data['header']['guid'],
				'pastelid'	=>	$return['ID']
			);

			//$db->insert('pasteltranslate', $translate);

			

		}
	}else{

		$customer_invoice['AmountDue'] = 0.00;
		//$invoice = do_pastel_call('TaxInvoice/Save', $p_args, $customer_invoice );

		
		/*if(isset($invoice['ID'])){

		

		

			$translate = array(
				'siteguid'	=>	$user->siteguid,
				'itemguid'	=>	$data['header']['guid'],
				'pastelid'	=>	$invoice['ID']
			);

			//$db->insert('pasteltranslate', $translate);
		}*/

	}
} 

	$saleGUID = $data['header']['guid'];

	if (empty($saleGUID)) {
			$saleGUID = "not defined";
	}
	

	//upload pastel tax invoice
	ob_start();
	//checkPastelSales();
	//upload_sale_pastel($saleGUID);
	ob_get_clean();

// include slip
$params['guid'] = $data['header']['guid'];

$sale = include dirname( dirname(__FILE__) ) . '/services/printer.php';
$return = array( 'message' => 'OK', 'guid' => $data['header']['guid'], 'slip' => $sale['slip'] );

if(isset($sale['pop'])){
	$return['pop'] = $sale['pop'];
}


if ($pastelid != 0) {
	upload_sale_pastel($saleguid);
}

mail_logs();

recon_movement();

return $return;







