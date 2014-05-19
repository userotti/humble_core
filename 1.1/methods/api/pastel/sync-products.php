<?php

global $user;

$user = new stdClass;
// FOR PROCESSING PRODUCTS SYNC

$sites = $db->get_results( "SELECT * FROM `sites` WHERE `pasteluser` != '' AND `pastelpass` != '' AND `pastelid` != ''; " );

foreach( $sites as $site){
	
	// set user & pass
	$user->pastel_user = $site->pasteluser;
	$user->pastel_pass = $site->pastelpass;

	// on hand filter
	$filter = $db->prepare( "(SELECT
		SUM(`movement`.`qty`*`movement`.`movedir`) AS `total`
	FROM `movement`
	WHERE
		`movement`.`productguid` = `products`.`guid`
	AND
		`movement`.`siteguid` = %s
	) AS `on_hand` ", $site->guid);


	// get site's products
	$query = "SELECT
		`products`.*,
		".$filter.",
		`pasteltranslate`.`pastelid`
		
	FROM `products`
	LEFT JOIN `pasteltranslate` ON (`products`.`guid` = `pasteltranslate`.`itemguid` AND `pasteltranslate`.`siteguid` = '".$site->guid."')

	WHERE `companyguid` = '".$site->coguid."';";

	// humble products
	$products = $db->get_results( $query, ARRAY_A);

	// pastel items
	$args = array( 
		'companyid'	=> $site->pastelid,

	);
	$pre_items = do_pastel_call( 'item/get', $args );
	$items = array();
	foreach($pre_items['Results'] as &$item){
		if(isset($item['Category'])){
			unset($item['Category']);
		}
		if(isset($item['TaxTypeIdSales'])){
			unset($item['TaxTypeIdSales']);
		}
		if(isset($item['TaxTypeIdPurchases'])){
			unset($item['TaxTypeIdPurchases']);
		}

		$items[$item['ID']] = $item;
	}



	foreach($products as &$product){

		if(!empty($product['pastelid'])){

			if(!isset($items[$product['pastelid']])){
				// Product not in pastel but has an id - assume its been removed from pastel so set it inactive
				$db->update('products', array('live' => 0), array('guid'=>$product['guid'], 'companyguid' => $site->coguid));
			}else{
				
				if(!empty($product['live'])){
					// both exists- update humble
					$update = array(
						'Description'				=>	$product['descr'],
						'Code'						=>	$product['stockcode'],
						'Active'					=>	(bool) $product['live'],
						'PriceInclusive'			=>	(float) $product['sell'],
						'PriceExclusive'			=>	(float) $product['sell'] - ( $product['sell'] * ( $product['vat']/100 ) ),
						'Physical'					=>	(bool) (!empty($product['virtual']) ? false : true),
						'LastCost'					=>	(float) $product['cost'],
						'AverageCost'				=>	(float) $product['cost'],
						'TotalQuantity'				=>	(float) $product['on_hand'], // GET THE ONHAND VALUE
						'TotalCost'					=>	(float) $product['cost']* abs( $product['on_hand'] ), // COST * ONHAND
						'SalesCommissionItem'		=>	(bool) false,
						'ID'						=>	(int) $product['pastelid'],
					);

					// check differences
					$do_update = false;
					foreach($update as $field=>$value){
						if( in_array($field, array('QuantityOnHand','ID','TotalQuantity') ) ){
							continue;
						}

						if((string) $items[$product['pastelid']][$field] != (string) $value){
							dump($items[$product['pastelid']],0);
							dump($product,0);
							dump($field .' - '. $items[$product['pastelid']][$field]. '  : ' . $value);

							$do_update = true;
						}

					}
					if($do_update){
						//dump('Updating - Pastel',0);
						//dump($update,0);
						//dump($items[$product['pastelid']]);
						$update_result = do_pastel_call( 'item/save', $args, $update );
					}
				}

			}
			// if it gets here is is in pastel and has a pastel id = product & item exist - 
			// remove it from the pastel list - the remaining items are new on pastel and must go to humble
			//
		}else{
				
			if(empty($product['live'])){
				continue; // ignore disabled products.
			}
			// SEE IF ITS in the pastil items
			
			$found = false;
			//dump($product['stockcode'],0);
			//echo '----------------<br>';
			foreach($items as $pastelid=>&$item){
				//dump($item['Code'],0);
				if($product['stockcode'] == $item['Code']){
					$found = $pastelid;
					break;
				}
			}
			if(!empty($found)){
				// found - update translate
				$translate = array(
					'siteguid'	=>	$site->guid,
					'itemguid'	=>	$product['guid'],
					'pastelid'	=>	$found
				);
				$db->insert('pasteltranslate', $translate);

			}else{
				// does not exist = update
				$insert = array(
					'Description'				=>	$product['descr'],
					'Code'						=>	$product['stockcode'],
					'Active'					=>	(bool) $product['live'],
					'PriceInclusive'			=>	(float) $product['sell'],
					'PriceExclusive'			=>	(float) $product['sell'] - ( $product['sell'] * ( $product['vat']/100 ) ),
					'Physical'					=>	(bool) (!empty($product['virtual']) ? false : true),
					'LastCost'					=>	(float) $product['cost'],
					'AverageCost'				=>	(float) $product['cost'],
					'TotalQuantity'				=>	(float) $product['on_hand'], // GET THE ONHAND VALUE
					'TotalCost'					=>	(float) $product['cost']* abs( $product['on_hand'] ), // COST * ONHAND
					'SalesCommissionItem'		=>	(bool) false,
				);
				

				// ADD TO PASTEL
				//dump($insert,0);
				
				$insert_result = do_pastel_call( 'item/save', $args, $insert );
				//dump($insert_result,0);
				//dump($products,0);
				//dump($items);

				// Add product to translate table.
				$translate = array(
					'siteguid'	=>	$site->guid,
					'itemguid'	=>	$product['guid'],
					'pastelid'	=>	$insert_result['ID']
				);
				$db->insert('pasteltranslate', $translate);
				
			}

		}

	}


	// Remove the items that are now linked.
	
	foreach ($products as &$product) {
		if(!empty($product['pastelid'])){
			unset($items[$product['pastelid']]);
		}
	}

	// Create products on humble
	if(!empty($items)){

		foreach($items as &$item){
			// remaining items are new.
			$insert = array(
				'guid'			=>	gen_uuid(),
				'companyguid'	=>	$site->coguid,
				'descr'			=>	$item['Description'],
				'stockcode'		=>	$item['Code'],
				'live'			=>	$item['Active'],
				'sell'			=>	$item['PriceInclusive'],
				'virtual'		=>	(bool) (!empty($item['Physical']) ? false : true),
				'cost'			=>	$item['LastCost'],
			);
			if($item['PriceInclusive']-$item['PriceExclusive'] > 0 && $item['PriceInclusive'] != $item['PriceExclusive']){
				$insert['vat'] = (($item['PriceInclusive']-$item['PriceExclusive'])/$item['PriceInclusive'])*100;
			}else{
				$insert['vat'] = 0;
			}
			//dump('New Product to Humble', 0);
			//dump($item,0);
			//dump($insert);
			$db->insert('products', $insert);

			// Add product to translate table.
			$translate = array(
				'siteguid'	=>	$site->guid,
				'itemguid'	=>	$insert['guid'],
				'pastelid'	=>	$item['ID']
			);
			$db->insert('pasteltranslate', $translate);
			//dump($translate);


		}

	}


}

