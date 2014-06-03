<?php


// FOR PROCESSING PRODUCTS SYNC

$site = $db->get_row( "SELECT * FROM `sites` WHERE `guid` = '".$user->siteguid."';" );


$raw_h_products = $db->get_results( "SELECT * FROM `products` WHERE `companyguid` = '".$user->cguid."';" );

$h_products = array();
foreach($raw_h_products as $product){
	$h_products[$product->stockcode] = $product;
}

$args = array( 
	'companyid'	=> $site->pastelid
);

$p_products = do_pastel_call( 'item/get', $args );
/// Check pastel against humble
foreach($p_products['Results'] as $product){
	if(isset($h_products[$product['Code']])){
		// Check if it has a pastelid
		if(empty($h_products[$product['Code']]->pastelid)){
			// LINK Product
			$db->update('products', array('pastelid' => $product['ID']), array('guid' => $h_products[$product['Code']]->guid));
		}
	}else{
		// new product
		// Create a humble product
		$newproduct = array(
			"guid"			=>	gen_uuid(),
			"companyguid"	=>	$user->cguid,
			"descr"			=>	$product['Code'],
			"stockcode"		=>	$product['Code'],
			"live"			=>	$product['Active'],
			"sell"			=>	$product['PriceInclusive'],
			"cost"			=>	$product['LastCost'],
			"pastelid"		=>	$product['ID'],
		);

		$db->insert('products', $newproduct);
	}
}

/// Check humble against pastel
foreach($h_products as $product){
	if(!empty($product->pastelid)){
		continue;
	}

	//dump($product);
	$newproduct = array(
		"Description"			=> $product->descr,
		"Code"					=> $product->stockcode,
		"Active"				=> $product->live,
		"PriceInclusive"		=> $product->sell,
		//"PriceExclusive"		=> $product->,
		"Physical"				=> true,
		//"TaxTypeIdSales"		=> $product->,
		//"TaxTypeIdPurchases"	=> $product->,
		"LastCost"				=> $product->cost,
		//"AverageCost"			=> $product->,
		//"QuantityOnHand"		=> $product->,
		//"TotalQuantity"			=> $product->,
		//"TotalCost"				=> $product->,
		//"Unit"					=> $product->,
	);
	$newproduct = do_pastel_call( 'item/save', $args, $newproduct );
	if(!empty($newproduct['ID'])){
		$db->update('products', array('pastelid' => $newproduct['ID']), array('guid' => $product->guid));
	}


}
return;
//dump($p_products,0);
//dump($h_products);
