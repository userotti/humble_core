<?php

	$siteGUID = $user->siteguid;
	$pastelid = $db->get_var("select pastelid from sites where guid = '".$siteGUID."'");

	if(isset($_POST['data'])){
		$data = json_decode( $_POST['data'], true );
	}else{
		$data = $_POST;
	}
	$saleguid = $data['header']['guid'];
	$site = $db->get_row( "SELECT * FROM `sites` WHERE `guid` = '".$siteGUID."';" );
	
	// header
	if (!empty($data['header'])) {
		if(empty($data['header']['datetime'])) { $data['header']['datetime'] = date('Y-m-d H:i:s'); }
		if(empty($data['header']['siteguid'])) { $data['header']['siteguid'] = $siteGUID; }
		if(empty($data['header']['cashier'])) { $data['header']['cashier'] = $user->userGUID; }
		if(empty($data['header']['agent'])) { $data['header']['agent'] = $user->userGUID; }
		if(empty($data['header']['deviceguid'])) { $data['header']['deviceguid'] = $user->deviceGUID; }
		if(empty($data['header']['devicename'])) { $data['header']['devicename'] = $user->deviceName; }
		if(empty($data['header']['customertaxnumber'])) { $data['header']['customertaxnumber'] = 'N/A'; }
		$exist = $db->get_var("select guid from sh where guid = '".$saleguid."'");
		if (empty($exist)) {
			$db->insert( 'sh', $data['header'] );
		} else {
			logtodb("duplicatesale","$saleguid");
		}
	}

	// lines
	if (!empty($data['lines'])) {
		foreach ($data['lines'] as $line) {
			$exist = $db->get_var("select guid from sl where guid = '".$line['guid']."' and line = ".$line['line']);
			if (empty($exist)) {
				if($line['qty'] > 0){
					// Check for current stocktake
					$takeid = $db->get_var("SELECT `guid` FROM `stocktake_log` WHERE `siteguid` = '".$user->siteguid."' AND `enddate` IS NULL;");
					// if there is a take in action add the sale line to the count if there is currently on hand
					if(!empty($takeid)){
						// check to see if the product is a counted item			
						$counted = $db->get_var("SELECT SUM(`qty`) FROM `stocktake_counted` WHERE `productguid` = '".$line['productguid']."' AND `takeguid` = '".$takeid."';");
						if(!empty($counted)){
							$reduce = $line['qty']-$counted;
							if($reduce > 0){
								$reduce = '-'.$counted;
							}else{
								$reduce = '-'.$line['qty'];
							}
							$newitem = array(
								'guid'        => $data['header']['guid'],
								'siteguid'    => $user->siteguid,
								'productguid' => $line['productguid'],
								'userguid'    => $user->userGUID,
								'takeguid'    => $takeid,
								'qty'         => $reduce,
								'imei'        => $line['imei'],
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
			}
		}
	}

	// tenders
	if (!empty($data['tenders'])) {
		foreach ( $data['tenders'] as $tender ) {
			// cleanup
			if(empty($tender['tenderguid'])) { $tender['tenderguid'] = gen_uuid(); }
			if(empty($tender['tenderdate'])) { $tender['tenderdate'] = date('Y-m-d H:i:s'); }
			if (empty($tender['siteguid'])) { $tender['siteguid'] = $siteGUID; }
			$exist = $db->get_var("select tenderguid from tenders where tenderguid = '".$tender['tenderguid']."'");
			if (empty($exist)) {
				$db->insert('tenders',$tender);
			}
		}
	}
 
	// include slip
	$params['guid'] = $data['header']['guid'];
	$sale = include dirname( dirname(__FILE__) ) . '/services/printer.php';
	$return = array( 
		'message' => 'OK',
		'guid'    => $data['header']['guid'],
		'slip'    => $sale['slip'],
	);

	if(isset($sale['pop'])){
		$return['pop'] = $sale['pop'];
	}

	recon_movement();

	if (!empty($pastelid)) {
		upload_sale_pastel($saleguid);
	}

	//console("sale returning");
	//console($return);
	return $return;