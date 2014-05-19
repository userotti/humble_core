<?php
console( $_POST );

$data = json_decode( $_POST['data'], true );

if(empty($data)){
	return array('message' => 'error', 'detail' => 'incomplete or no data sent');
}

// header
if ( !empty( $data['header'] ) ) {
	
	if(!isset($data['header']['guid'])){ $data['header']['guid'] = gen_uuid(); }
	if(!isset($data['header']['companyguid'])){ $data['header']['companyguid'] = $user->cguid; }
	if(!isset($data['header']['siteguid'])){ $data['header']['siteguid'] = $user->siteguid; }
	if(!isset($data['header']['deviceguid'])){ $data['header']['deviceGUID'] = $user->deviceGUID; }
	if(!isset($data['header']['userguid'])){ $data['header']['userguid'] = $user->userGUID; }
	if(!isset($data['header']['acc']) && isset($data['header']['accguid'])){ 
		$data['header']['acc'] = $db->get_var($db->prepare('SELECT `descr` FROM `community` WHERE `guid` = %d;', $data['header']['accguid']));
	}

	// clean date format
	$data['header']['datetime'] = date('Y-m-d H:i:s', strtotime($data['header']['datetime']));
	$db->insert( 'mh', $data['header'] );
	$direction = $db->get_var($db->prepare('SELECT `movedir` FROM `movetypes` WHERE `movecode` = %d;', $data['header']['movetype']));
}

// lines
if ( !empty( $data['lines'] ) ) {
	foreach ( $data['lines'] as $index=>$line ) {
		$product = $db->get_row( $db->prepare( "SELECT * FROM `products` WHERE `guid` = %s;", $line['productguid'] ), ARRAY_A );

		if(!isset($line['descr'])){
			$line['descr'] = $product['descr'];
		}
		if(!isset($line['guid'])){
			$line['guid'] = $data['header']['guid'];
		}
		if(!isset($line['serial'])){
			$line['serial'] = $db->get_var( $db->prepare( "SELECT `ean` FROM `ean` WHERE `productguid` = %s AND `companyguid` = %s ;", $line['productguid'], $data['header']['companyguid'] ), ARRAY_A );
		}

		// update cost accordingly :: IF from WEB.
		if(floatval($line['unitcost']) !== floatval($product['cost']) && !empty($_POST['web'])){
			$db->update('products', array('cost'=>floatval($line['unitcost'])), array("guid" => $product['guid']));
		}

		$weight = $db->get_var( $db->prepare( "SELECT `weight` FROM `products` WHERE `guid` = %s;", $line['productguid'] ) );
		//$db->update( 'products', array( 'weight'=>( $weight+$line['qty'] ) ), array( 'guid'=>$line['productguid'] ) );
		//$db->delete( 'confirmed', array( 'itemGUID' => $line['productguid'] ) );
		$db->insert( 'ml', $line );
		
		if(!empty($supplier_invoice) && !empty($product['pastelid'])){
			if($data['header']['movetype'] == '12'){
				$lineEx = ( ($line['lineincl']-$line['linevat']) / abs($line['qty']) );
			}else{
				$lineEx = $line['lineincl']-$line['linevat'];
			}
		}
	}
}

// imeis
if ( !empty( $data['imeis'] ) ) {
	foreach ( $data['imeis'] as $imei ) {
		$newcost = array(
			'guid'			=>	$imei['guid'],
			'companyguid'	=>	$user->cguid,
			'productguid'	=>	$imei['productguid'],
			'imei'			=>	$imei['imei'],
			'cost'			=>	$imei['imeicost'],
		);
		$db->insert( 'moveimeis', $imei );
	}
}


$out = array( 'message' => 'OK', 'guid' => $data['header']['guid'] );

recon_movement();
recon_imei();

return $out;
?>