<?php

	function logtodb($system,$message) {
		global $db;
		$log = array(
			'guid' => gen_uuid(),
			'system' => $system,
			'message' => $message,
		);
		$db->insert('log',$log);
	}

	function get_pastel_id($siteGUID,$itemGUID) {
		global $db;
		$pastelID = $db->get_var("select pastelid from pasteltranslate where siteguid = '".$siteGUID."' and itemguid = '".$itemGUID."'");
		if (empty($pastelID)) {
			$pastelID = "";
		}
		return $pastelID;
	}

	function upload_sale_pastel($saleGUID) {
		global $db;
		date_default_timezone_set('Africa/Johannesburg');
		//logtodb("upload_sale_to_pastel","upload sale $saleGUID to pastel");
		$sendToPastel = true;

		$result = array(
			'saleGUID' => $saleGUID,
			'message' => 'OK',
		);

		$sh = $db->get_results("select siteguid,excl,vat,tender_total,datetime,pastelid from sh where guid = '".$saleGUID."'");
		foreach ($sh as $row) {
			$siteGUID = $row->siteguid;
			$excl = abs($row->excl);
			$vat = abs($row->vat);
			$incl = $excl+$vat;
			$total = abs($row->tender_total);
			$datetime = $row->datetime;
			if ($row->pastelid != 0) {
				$sendToPastel = false;
			}
		}
		
		$site = $db->get_results("select pastelid from sites where guid = '".$siteGUID."'");
		foreach ($site as $row) {
			$pastelID = $row->pastelid;
			$result['pastelID'] = $pastelID;
		}
		if ($pastelID == 0) {
			$sendToPastel = false;
			$result['message'] = 'No Pastel ID';
			return $result;
		}
		$p_args = array( 
			'companyid'	=> $pastelID,
		);
		$taxType = 1;
		$accountGUID = $db->get_var("select communityguid from tenders where saleguid = '".$saleGUID."'");
		if ($accountGUID == "N/A") {
			$accountGUID = "";
		}
		//printf("accountguid [$accountGUID]");
		if (empty($accountGUID)) {
			$cashCustomerPastelID = getCashCustomerPastelID($siteGUID);
			$result['cashCustomerPastelID'] = $cashCustomerPastelID;	
			$lineType = 0;	
			$taxType = 1;	
		} else {
			$cashCustomerPastelID = get_pastel_id($siteGUID,$accountGUID);
			$lineType = 0;
			$taxType = 1;
			$result['cashCustomerPastelID'] = $cashCustomerPastelID;			
		}
	
		$customer_invoice = array(
			"DueDate"				=> $datetime,
			"CustomerId"			=> $cashCustomerPastelID,
			"Date"					=> $datetime,
			"Inclusive"				=> false,
			"DocumentNumber"		=> "", 
			"Reference"				=> $saleGUID,
			"Exclusive"				=> $excl,
			"Tax"					=> $vat,
			"Rounding"				=> 0,
			"Total"					=> $total,
			"AmountDue"				=> false,
			"Printed"				=> true,
		);

		$sendInvoice = false;
		$sendReturn = false;
		$lineQty = 0;
		$salelines = $db->get_results("select * from sl where guid = '".$saleGUID."' and qty >= 0 order by line");
		if (count($salelines) == 0) { 
			$result['Error'][] = "No Invoice SaleLines for saleGUID $saleGUID";
		} else {
			$sendInvoice = true;
			foreach ($salelines as $line) {
				$productGUID = $line->productguid;
				$descr = $db->get_var("select descr from products where guid = '".$productGUID."'");
				$qty = abs($line->qty);
				$lineexcl = abs(($line->sell-$line->vat));
				$unitexcl = abs($lineexcl/abs($line->qty));
				$productPastelID = $db->get_var("select pastelid from pasteltranslate where siteguid = '".$siteGUID."' and itemguid = '".$productGUID."'");
				if ($productPastelID == 0) { 
					$sendToPastel = false;
					$result['Error'][] = "No Pastel ID for ProductGUID $productGUID";
				} else {
					$invoicelines[] = array(
						"LineType"				=> $lineType,
						"SelectionId"			=> $productPastelID,
						//"TaxTypeId"				=> $taxType,
						"TaxType"				=> $taxType,
						"Description"			=> $descr,
						"Quantity"				=> $qty,
						"UnitPriceExclusive"	=> $unitexcl,
						"TaxPercentage"			=> 0.14,
						"Exclusive"				=> $lineexcl,

					);		
					$lineQty++;
				}
			}
		}

		$salelines = $db->get_results("select * from sl where guid = '".$saleGUID."' and qty < 0 order by line");
		if (count($salelines) == 0) { 
			$result['Error'][] = "No Return SaleLines for saleGUID $saleGUID";
		} else {
			$sendReturn = true;
			foreach ($salelines as $line) {
				$productGUID = $line->productguid;
				$descr = $db->get_var("select descr from products where guid = '".$productGUID."'");
				$vat = $db->get_var("select vat from products where guid = '".$productGUID."'");
				$vat = $vat/100;
				$qty = abs($line->qty);
				$lineexcl = abs(($line->sell-$line->vat));
				$unitexcl = abs($lineexcl/abs($line->qty));
				$productPastelID = $db->get_var("select pastelid from pasteltranslate where siteguid = '".$siteGUID."' and itemguid = '".$productGUID."'");
				if ($productPastelID == 0) { 
					$sendToPastel = false;
					$result['Error'][] = "No Pastel ID for ProductGUID $productGUID";
				
				} else {
					$returnlines[] = array(
						"LineType"				=> $lineType,
						"SelectionId"			=> $productPastelID,
						//"TaxTypeId"				=> $taxType,
						"TaxType"				=> $taxType,
						"Description"			=> $descr,
						"Quantity"				=> $qty,
						"UnitPriceExclusive"	=> $unitexcl,
						"TaxPercentage"			=> $vat,
						"Exclusive"				=> $lineexcl,
					);		
					$lineQty++;
				}
			}
		}
		//if ($lineQty == 0) { logtodb('uploadSaleToPastel',"No Sale Lines for $saleGUID"); }
		
		if ($sendToPastel == true) {
			if ($sendInvoice == true) {
				$customer_invoice['Lines'] = $invoicelines;
				$pastel_return = do_pastel_call('TaxInvoice/Save', $p_args, $customer_invoice );
				$salePastelID = $pastel_return['ID'];
				if (empty($salePastelID)) {
					logtodb('upload sale to pastel','pastel id is zero');
					//console($pastel_return);
				}
				$db->update('sh', array('pastelid' => $salePastelID),array('guid' => $saleGUID));
				$result['return'] = $pastel_return;
				emailCustomerInvoice($saleGUID);
			}
			if ($sendReturn == true) {
				$customer_invoice['Lines'] = $returnlines;
				$pastel_return = do_pastel_call('CustomerReturn/Save', $p_args, $customer_invoice );	
				$salePastelID = $pastel_return['ID'];
				$db->update('sh', array('pastelid' => $salePastelID),array('guid' => $saleGUID, 'pastelid' => 0));
				$result['return'] = $pastel_return;
			}
		} else {
			//logtodb('uploadSaleToPastel',"Cannot Send To Pastel $saleGUID");
			$result['return'] = "Cannot Send To Pastel";
			foreach ($result['Error'] as $row) {
				//logtodb("uploadSaleToPastel",$row);
			}
		}

		
		//console($result);
		

		return $result;
	}

	function emailCustomerInvoice($saleGUID) {
		global $db;
		$proceed = true;
		$msg = "Start";

		$siteguid = $db->get_var("select siteguid from sh where guid = '".$saleGUID."'");
		if (empty($siteguid)) { 
			$proceed = false; 
			$msg = "SiteGUID is empty [$siteguid]";
		} 

		$saleid = $db->get_var("select pastelid from sh where guid = '".$saleGUID."'");
		if (empty($saleid) || $saleid == 0) { 
			$proceed = false; 
			$msg = "saleid is empty [$saleid]";
		} 

		$email = $db->get_var("select email from email_receipts where saleguid = '".$saleGUID."'");
		if (empty($email)) { 
			$proceed = false; 
			$msg = "email is empty [$email]";
		} 		

		if ($proceed) {
			$pastelid = $db->get_var("select pastelid from sites where guid = '".$siteguid."'");
			if (empty($pastelid) || $pastelid == 0) {
				$proceed = false;
				$msg = "pastelid is empty [$pastelid]";
			}
		}

		if ($proceed) {
			$p_args = array( 
				'companyid'	=> $pastelid,
			);
			$pastelemail = array(
				'ID'           => $saleid,
				'EmailAddress' => $email,
				'CCAddress'    => 'will@humble.co.za',
				'Subject'      => 'humble Tax Invoice',
				'Message'      => 'This is an automated Tax Invoice from humble',
			);
			$result = do_pastel_call('TaxInvoice/Email', $p_args, $pastelemail);
		} else {
			//logtodb("pastelemail","$saleGUID: cannot send pastel invoice email $msg");
			//console("Cannot Send Pastel Invoice Email for $saleGUID");
		}
	}

	function getCashCustomerPastelID($siteGUID) {
		global $db;
		$site = $db->get_results("select coguid,pastelid from sites where guid = '".$siteGUID."'");
		foreach ($site as $row) {
			$companyGUID = $row->coguid;	
			$pastelID = $row->pastelid;
		}
		$cashCustomerGUID = $db->get_var("select guid from community where companyguid = '".$companyGUID."' and communitytype = 2 and descr = 'Cash Sale - Humble Till'");
		$cashCustomerPastelID = $db->get_var("select pastelid from pasteltranslate where siteguid = '".$siteGUID."' and itemguid = '".$cashCustomerGUID."'");
		//printf("------------------cash customer $cashCustomerGUID | $cashCustomerPastelID\n");
		return $cashCustomerPastelID;
	}

	function checkCashCustomer($siteGUID) {
		global $db;
		$cashCustomerName = 'Cash Sale - Humble Till';
		$companyGUID = $db->get_var("select coguid from sites where guid = '".$siteGUID."'");	
		$results = $db->get_results("select guid from community where companyguid = '".$companyGUID."' and communitytype = 2 and descr = '".$cashCustomerName."'");
		if (count($results) === 0) {
			$customer = array(
				"guid" => gen_uuid(),
				"companyguid" => $companyGUID,
				"descr" => $cashCustomerName,
				"communitytype" => 2,
				"live" => 1,
			);
			$db->insert("community",$customer);
		}
	}

	function getAllIDs($pastelID,$method,$fields) {
		global $db;
		$gotAll = false;
		$arr = array();
		$p_args = array( 
			'companyid'	=> $pastelID,
		);
		do {
			$gotAlready = count($arr);
			$pastel_return = do_pastel_call($method,$p_args);
			$totalItems = $pastel_return['TotalResults'];
			foreach ($pastel_return['Results'] as $item) {
				$add = array();
				if (count($fields) != 0) {
					foreach ($fields as $field) {
						$add[$field] = $item[$field];
					}
					$arr[] = $add;
				} else {
					$arr[] = $item;
				}
			}
			if (count($arr) == $totalItems) {
				$gotAll = true;
			} else {
				$p_args['$skip'] = count($arr);	
				if (count($arr) >= 2000) {
					$gotAll = true;
					logtodb("getAllIDs","More than 2000 items: $pastelID | $totalItems");
				}
			}
		} while ($gotAll == false);
		return $arr;
	}

	function checkProducts($siteGUID) {
		global $db;
		$pastelID = $db->get_var("select pastelid from sites where guid = '".$siteGUID."'");
		$companyGUID = $db->get_var("select coguid from sites where guid = '".$siteGUID."'");

		$p_args = array( 
			'companyid'	=> $pastelID,
		);

		$translate = array(
			"Code" => "stockcode",
			"Description" => "descr",
			"LastCost" => "cost",
			"PriceInclusive" => "sell",
			"Active" => "live",
		);

		//$arr = getAllIDs($pastelID,"Item/Get",array("ID","Code","Description","LastCost","PriceInclusive","TotalQuantity","QuantityOnHand","Physical","Active"));
		$arr = getAllIDs($pastelID,"Item/Get",array());
		foreach ($arr as $item) {
			$productGUID = $db->get_var("select itemguid from pasteltranslate where pastelid = ".$item['ID']);
			if (is_null($productGUID)) {
				$productGUID = $db->get_var("select guid from products where stockcode = '".$item['Code']."' and descr = '".$item['Description']."' and companyguid = '".$companyGUID."'");
				if (is_null($productGUID)) {
					$newProductGUID = gen_uuid();
					$product = array(
						"guid" => $newProductGUID,
						"companyguid" => $companyGUID,
						"stockcode" => $item['Code'],
						"descr" => $item['Description'],
						"cat" => 0,
						"cost" => $item['LastCost'],
						"sell" => $item['PriceInclusive'],
						"live" => $item['Active'],
					);
					$db->insert("products",$product);
					insertPastelTranslate($siteGUID,$newProductGUID,$item['ID']);
				} else {
					insertPastelTranslate($siteGUID,$productGUID,$item['ID']);
				}
			} else {
				$update = false;
				$product = $db->get_row("select guid,stockcode,descr,cost,sell,vat,virtual,live from products where guid = '".$productGUID."'");
				foreach ($translate as $key => $value) {
					$val1 = $item[$key];
					$val2 = $product->$value;
					if ($val1 != $val2) {
						$db->update("products",array($value => $val1),array("guid" => $productGUID));	
						$db->delete('confirmed',array("itemguid" => $productGUID));
					}
				}
				if (empty($item['QuantityOnHand'])) {
					//console($item);
					$onh1 = 0;
				} else {
					$onh1 = $item['QuantityOnHand'];	
				}
				$onh2 = getOnhand($siteGUID,$productGUID);
				if ($onh1 != $onh2) {
					//console("onh diff for $productGUID [$onh1] vs [$onh2]");
					$newProduct = array(
 						'Description'				=>	$product->descr,
						'Code'						=>	$product->stockcode,
						'Active'					=>	(bool) $product->live,
						'PriceInclusive'			=>	(float) $product->sell,
						'PriceExclusive'			=>	(float) $product->sell-($product->sell*($product->vat/100)),
						'Physical'					=>	(bool) (!empty($product->virtual) ? false : true),
						'LastCost'					=>	(float) $product->cost,
						'AverageCost'				=>	(float) $product->cost,
						'TotalQuantity'				=>	$onh2,
						'QuantityOnHand'			=> 	$onh2,
						'TotalCost'					=>	(float) $product->cost*$onh2,
						'SalesCommissionItem'		=>	(bool) false,
						'ID'						=>	$item['ID'],
					);	
					$result = do_pastel_call( 'Item/Save',$p_args,$newProduct);
				}
				if ($item['Physical'] == true && $product->virtual == 1) {
					$db->update("products",array("virtual" => 0),array("guid" => $productGUID));	
					$db->delete('confirmed',array("itemguid" => $productGUID));
				} else if ($item['Physical'] == false && $product->virtual == 0) {
					$db->update("products",array("virtual" => 1),array("guid" => $productGUID));	
					$db->delete('confirmed',array("itemguid" => $productGUID));
				}
				$db->update("pasteltranslate",array('lastcheck' => date('Y-m-d H:i:s')),array("pastelid" => $item['ID'],"itemguid" => $productGUID));
			}
		}

		$products = $db->get_results("select guid,stockcode,descr,cost,sell,vat,virtual,a2.pastelid,live from products a1 left join pasteltranslate a2 on a1.guid = a2.itemguid where companyguid = '".$companyGUID."'");
		foreach ($products as $product) {
			if (is_null($product->pastelid)) {
 				$productGUID = $product->guid;
 				$qty = getOnhand($siteGUID,$productGUID);
 				$newProduct = array(
 					'Description'				=>	$product->descr,
					'Code'						=>	$product->stockcode,
					'Active'					=>	(bool) $product->live,
					'PriceInclusive'			=>	(float) $product->sell,
					'PriceExclusive'			=>	(float) $product->sell-($product->sell*($product->vat/100)),
					'Physical'					=>	(bool) (!empty($product->virtual) ? false : true),
					'LastCost'					=>	(float) $product->cost,
					'AverageCost'				=>	(float) $product->cost,
					'TotalQuantity'				=>	$qty,
					'TotalCost'					=>	(float) $product->cost*$qty,
					'SalesCommissionItem'		=>	(bool) false,
				);	
				$result = do_pastel_call( 'Item/Save',$p_args,$newProduct);
				if (!empty($result['ID'])) { insertPastelTranslate($siteGUID,$productGUID,$result['ID']); }
 			}
		}
	}

	function getOnhand($siteGUID,$productGUID) {
		global $db;
		$qty = $db->get_var("select sum(qty*movedir) qty from movement where siteguid = '".$siteGUID."' and productguid = '".$productGUID."'");
		if (is_null($qty)) {
			$qty = 0;
		}
		return $qty;
	}

	function updateCustomerOnPastel($siteGUID,$itemGUID) {
		//console("updateCustomerOnPastel $siteGUID | $itemGUID");
		global $db;
		$companyGUID = $db->get_var("select coguid from sites where guid = '".$siteGUID."'");
		$sitePastelID = $db->get_var("select pastelid from sites where guid = '".$siteGUID."'");
		$itemPastelID = $db->get_var("select pastelid from pasteltranslate where siteguid = '".$siteGUID."' and itemguid = '".$itemGUID."'");
		$p_args = array( 
			'companyid'	=> $sitePastelID,
		);
		$method = "Customer/Save";
		$customer = $db->get_row("select * from community where guid = '".$itemGUID."' and companyguid = '".$companyGUID."' and communitytype in (0,2)");
		$arr = array(
			"Name"				=>	$customer->descr,
			"Email"				=>	$customer->email,
			"Active"			=>	($customer->live == 1 ? true : false ),
			"Balance"			=>	(float)$customer->current_balance * 100,
			"CommunicationMethod" => 0,
			"CreditLimit"		=>	(float)$customer->community_limit * 100,
			"PostalAddress01"	=>	$customer->address_line1,
			"PostalAddress02"	=>	$customer->address_line2,
			"PostalAddress03"	=>	$customer->suburb,
			"PostalAddress04"	=>	$customer->city,
			"PostalAddress05"	=>	$customer->postal_code,
			"DeliveryAddress01"	=>	$customer->billing_address_line1,
			"DeliveryAddress02"	=>	$customer->billing_address_line2,
			"DeliveryAddress03"	=>	$customer->billing_suburb,
			"DeliveryAddress04"	=>	$customer->billing_city,
			"DeliveryAddress05"	=>	$customer->billing_postal_code
		);
		//console("pastelid item [$itemPastelID]");
		if (is_numeric($itemPastelID)) {
			$arr['ID'] = $itemPastelID;
		}
		$result = do_pastel_call($method,$p_args,$arr);
		if (!empty($result['ID'])) {
			insertPastelTranslate($siteGUID,$itemGUID,$result['ID']);
		}
	}

	function updateSupplierOnPastel($siteGUID,$itemGUID) {
		//console("updateSupplierOnPastel $siteGUID | $itemGUID");
		global $db;
		$companyGUID = $db->get_var("select coguid from sites where guid = '".$siteGUID."'");
		$sitePastelID = $db->get_var("select pastelid from sites where guid = '".$siteGUID."'");
		$itemPastelID = $db->get_var("select pastelid from pasteltranslate where siteguid = '".$siteGUID."' and itemguid = '".$itemGUID."'");
		$p_args = array( 
			'companyid'	=> $sitePastelID,
		);
		$method = "Supplier/Save";
		$supplier = $db->get_row("select * from community where guid = '".$itemGUID."' and companyguid = '".$companyGUID."' and communitytype = 1");
		$arr = array(
			"Name"				=>	$supplier->descr,
			"Email"				=>	$supplier->email,
			"Active"			=>	($supplier->live == 1 ? true : false ),
			"Balance"			=>	(float)$supplier->current_balance * 100,
			"CommunicationMethod" => 0,
			"CreditLimit"		=>	(float)$supplier->community_limit * 100,
			"PostalAddress01"	=>	$supplier->address_line1,
			"PostalAddress02"	=>	$supplier->address_line2,
			"PostalAddress03"	=>	$supplier->suburb,
			"PostalAddress04"	=>	$supplier->city,
			"PostalAddress05"	=>	$supplier->postal_code,
			"DeliveryAddress01"	=>	$supplier->billing_address_line1,
			"DeliveryAddress02"	=>	$supplier->billing_address_line2,
			"DeliveryAddress03"	=>	$supplier->billing_suburb,
			"DeliveryAddress04"	=>	$supplier->billing_city,
			"DeliveryAddress05"	=>	$supplier->billing_postal_code
		);
		//console("pastelid item [$itemPastelID]");
		if (is_numeric($itemPastelID)) {
			$arr['ID'] = $itemPastelID;
		}
		$result = do_pastel_call($method,$p_args,$arr);
		if (!empty($result['ID'])) {
			insertPastelTranslate($siteGUID,$itemGUID,$result['ID']);
		}
	}

	function checkSuppliers($siteGUID) {
		global $db;
		$sitePastelID = $db->get_var("select pastelid from sites where guid = '".$siteGUID."'");
		$companyGUID = $db->get_var("select coguid from sites where guid = '".$siteGUID."'");
		$p_args = array( 
			'companyid'	=> $sitePastelID,
		);
		$translate = array(
			"Name" => "descr",
			"Active" => "live",
		);
		$pastelIDs = array();
		$arr = getAllIDs($sitePastelID,"Supplier/Get",array("ID","Name","Active"));
		foreach ($arr as $item) {
			$pastelID = $item['ID'];
			$pastelIDs[] = $pastelID;
			$descr = $item['Name'];
			$supplierGUID = $db->get_var("select itemguid from pasteltranslate a1 inner join community a2 on a1.itemguid = a2.guid where pastelid = ".$pastelID." and siteguid = '".$siteGUID."' and communitytype = 1");
			if (empty($supplierGUID)) {
				$supplierGUID = $db->get_var("select guid from community where descr = '".$item['Name']."' and companyguid = '".$companyGUID."' and communitytype = 1");
				if (empty($supplierGUID)) {
					$supplierGUID = gen_uuid();
					$supplier = array(
						"guid" => $supplierGUID,
						"companyguid" => $companyGUID,
						"descr" => $descr,
						"communitytype" => 1,
						"live" => $item['Active'],
					);
					$db->insert("community",$supplier);
					insertPastelTranslate($siteGUID,$supplierGUID,$pastelID);
				} else {
					console($item);
					insertPastelTranslate($siteGUID,$supplierGUID,$pastelID);
				}
			} else {
				$update = false;
				$supplier = $db->get_row("select guid,descr,live from community where guid = '".$supplierGUID."'");
				foreach ($translate as $key => $value) {
					$val1 = $item[$key];
					$val2 = $supplier->$value;
					if ($val1 != $val2) {
						$db->update("community",array($value => $val1),array("guid" => $supplierGUID));	
						$db->delete('confirmed',array("itemguid" => $supplierGUID));
					}
				}
				$db->update("pasteltranslate",array('lastcheck' => date('Y-m-d H:i:s')),array("pastelid" => $item['ID'],"itemguid" => $supplierGUID));
			}
		}

		//console("companyguid $companyGUID");
		$suppliers = $db->get_results("select guid,descr from community where companyguid = '".$companyGUID."' and communitytype = 1");
		
		foreach ($suppliers as $supplier) {
			$thisPastelID = get_pastel_id($siteGUID,$supplier->guid);
			if (!in_array($thisPastelID,$pastelIDs)) {
				updateSupplierOnPastel($siteGUID,$supplier->guid);
			}

			/*$newsupplier = array(
				"Name"				=>	$supplier->descr,
				"Email"				=>	$supplier->email,
				"Active"			=>	($supplier->live == 1 ? true : false ),
				"Balance"			=>	(float)$supplier->current_balance * 100,
				"CommunicationMethod" => 0,
				"CreditLimit"		=>	(float)$supplier->community_limit * 100,
				"PostalAddress01"	=>	$supplier->address_line1,
				"PostalAddress02"	=>	$supplier->address_line2,
				"PostalAddress03"	=>	$supplier->suburb,
				"PostalAddress04"	=>	$supplier->city,
				"PostalAddress05"	=>	$supplier->postal_code,
				"DeliveryAddress01"	=>	$supplier->billing_address_line1,
				"DeliveryAddress02"	=>	$supplier->billing_address_line2,
				"DeliveryAddress03"	=>	$supplier->billing_suburb,
				"DeliveryAddress04"	=>	$supplier->billing_city,
				"DeliveryAddress05"	=>	$supplier->billing_postal_code
			);*/
			//updateSupplierOnPastel($siteGUID,$supplier->guid);
			//$result = do_pastel_call('Supplier/Save', $p_args, $newsupplier);
			//if (!empty($result['ID'])) { insertPastelTranslate($siteGUID,$supplierGUID,$result['ID']); }
		}
	}

	function checkCustomers($siteGUID) {
		global $db;
		$pastelID = $db->get_var("select pastelid from sites where guid = '".$siteGUID."'");
		$companyGUID = $db->get_var("select coguid from sites where guid = '".$siteGUID."'");
		$p_args = array( 
			'companyid'	=> $pastelID,
		);
		$translate = array(
			"Name"        => "descr",
			"Active"      => "live",
			"Email"       => "email",
			"ContactName" => "contactname",
			"Mobile"      => "contactnumber",
		);
		$arr = getAllIDs($pastelID,"Customer/Get",array());
		$i = 1;
		foreach ($arr as $item) {
			if (isset($item['Category']) && $siteGUID == "FEBE2730-081E-417B-A5F2C84B8728DBBA") {
				$category = $item['Category']['ID'];
				$catdescr = $item['Category']['Description'];
				$tillcustomer = $db->get_var("select tillcustomer from packages where category = ".$category);
			} else {
				$tillcustomer = 0;
			}
			$descr = $item['Name'];
			$customerGUID = $db->get_var("select itemguid from pasteltranslate a1 inner join community a2 on a1.itemguid = a2.guid where pastelid = ".$item['ID']." and siteguid = '".$siteGUID."' and communitytype in (0,2)");
			if (is_null($customerGUID)) {
				$customerGUID = $db->get_var("select guid from community where descr = '".$item['Name']."' and companyguid = '".$companyGUID."' and communitytype in (0,2)");
				if (is_null($customerGUID)) {
					$newCustomerGUID = gen_uuid();
					$cType = 0;
					if (array_key_exists("CashSale",$item)) {
						if ($item['CashSale'] == true) {
							$cType = 2;
						}
					}
					$customer = array(
						"guid"          => $newCustomerGUID,
						"companyguid"   => $companyGUID,
						"descr"         => $item['Name'],
						"communitytype" => $cType,
						"live"          => $item['Active'],
						"tillcustomer"  => $tillcustomer,
					);
					if (isset($item['ContactName'])) { $customer['contactname'] = $item['ContactName']; }
					if (isset($item['Mobile'])) { $customer['contactnumber'] = $item['Mobile']; }
					if (isset($item['Email'])) { $customer['email'] = $item['Email']; }
					if (isset($item['Category'])) { 
						if ($item['Category']['ID'] == "78836") {
							$customer['tillcustomer'] = 1;
						}
					}
					$db->insert("community",$customer);
					insertPastelTranslate($siteGUID,$newCustomerGUID,$item['ID']);
				} else {
					$newID = $item['ID'];
					$str = sprintf("$siteGUID | $customerGUID | $newID");
					logtodb("checkCustomers",$str);
					insertPastelTranslate($siteGUID,$customerGUID,$item['ID']);
				}
			} else {
				$update = false;
				$customer = $db->get_row("select * from community where guid = '".$customerGUID."'");
				if (isset($item['Category'])) { 
					if ($customer->tillcustomer != $tillcustomer) {
						$db->update("community",array('tillcustomer' => $tillcustomer),array("guid" => $customerGUID));	
					}
				}
				foreach ($translate as $key => $value) {
					if (isset($item[$key])) {
						$val1 = $item[$key];
						$val2 = $customer->$value;
						if ($val1 != $val2) {
							$db->update("community",array($value => $val1),array("guid" => $customerGUID));	
							$db->delete('confirmed',array("itemguid" => $customerGUID));
						}
					}
				}
				$db->update("pasteltranslate",array('lastcheck' => date('Y-m-d H:i:s')),array("pastelid" => $item['ID'],"itemguid" => $customerGUID));
			}
		}

		$customers = $db->get_results("select * from community where companyguid = '".$companyGUID."' and communitytype in (0,2) and guid not in (select itemguid from pasteltranslate where siteguid = '".$siteGUID."')");
		foreach ($customers as $customer) {
			$newcustomer = array(
				"Name"                =>	$customer->descr,
				"Email"               =>	$customer->email,
				"Active"              =>	($customer->live == 1 ? true : false ),
				"Balance"             =>	(float)$customer->current_balance * 100,
				"CashSale"            =>	($customer->communitytype == 2 ? true : false),
				"CommunicationMethod" => 	0,
				"CreditLimit"         =>	(float)$customer->community_limit * 100,
				"PostalAddress01"     =>	$customer->address_line1,
				"PostalAddress02"     =>	$customer->address_line2,
				"PostalAddress03"     =>	$customer->suburb,
				"PostalAddress04"     =>	$customer->city,
				"PostalAddress05"     =>	$customer->postal_code,
				"DeliveryAddress01"   =>	$customer->billing_address_line1,
				"DeliveryAddress02"   =>	$customer->billing_address_line2,
				"DeliveryAddress03"   =>	$customer->billing_suburb,
				"DeliveryAddress04"   =>	$customer->billing_city,
				"DeliveryAddress05"   =>	$customer->billing_postal_code,
				"ContactName"         => $customer->contactname,
				"Mobile"              => $customer->contactnumber,
			);
			if ($siteGUID == 'FEBE2730-081E-417B-A5F2C84B8728DBBA' && $customer->tillcustomer != 0) {
				$cat = $db->get_var("select category from packages where tillcustomer = ".$customer->tillcustomer);
				$newcustomer['Category'] = array(
					'ID'          => $cat,
				);
 			}
			$result = do_pastel_call('Customer/Save', $p_args, $newcustomer);
			if (!empty($result['ID']) && is_numeric($result['ID'])) {
				$resultID = $result['ID'];
				insertPastelTranslate($siteGUID,$customer->guid,$result['ID']);
			} else { 
				console($result);
				logtodb('checkCustomers',"no result id for $customer->guid [$result]"); 
			}
		}
	}

	function update_product_on_pastel($productGUID) {
		global $db;

		$product = $db->get_row("select * from products where guid = '".$productGUID."'");
		$companyGUID = $product->companyguid;
		$sites = $db->get_results("select guid,pastelid from sites where coguid = '".$companyGUID."' and pastelid != 0 and pastelhash != ''");
		foreach ($sites as $site) {
			$sitePastelID = $site->pastelid;
			$productPastelID = $db->get_var("select pastelid from pasteltranslate where siteguid ='".$site->guid."' and itemguid = '".$productGUID."'");
			$p_args = array( 
				'companyid'	=> $sitePastelID,
			);

			if (empty($productPastelID)) {
				$onh = getOnhand($site->guid,$productGUID);
				$updatedProduct = array(
					'Description'				=>	$product->descr,
					'Code'						=>	$product->stockcode,
					'Active'					=>	(bool) $product->live,
					'PriceInclusive'			=>	(float) $product->sell,
					'PriceExclusive'			=>	(float) $product->sell-($product->sell*($product->vat/100)),
					'Physical'					=>	(bool) (!empty($product->virtual) ? false : true),
					//'OpeningCost'				=>	(float) $product->cost,
					'LastCost'					=>	(float) $product->cost,
					'AverageCost'				=>	(float) $product->cost,
					'TotalQuantity'				=>	$onh,
					'TotalCost'					=>	(float) $product->cost*$onh,
					'SalesCommissionItem'		=>	(bool) false,
				);
				$result = do_pastel_call('Item/Save', $p_args, $updatedProduct);
			} else {
				$onh = getOnhand($site->guid,$productGUID);
				$updatedProduct = array(
					'Description'				=>	$product->descr,
					'Code'						=>	$product->stockcode,
					'Active'					=>	(bool) $product->live,
					'PriceInclusive'			=>	(float) $product->sell,
					'PriceExclusive'			=>	(float) $product->sell-($product->sell*($product->vat/100)),
					'Physical'					=>	(bool) (!empty($product->virtual) ? false : true),
					//'OpeningCost'					=>	(float) $product->cost,
					'LastCost'					=>	(float) $product->cost,
					'AverageCost'				=>	(float) $product->cost,
					'TotalQuantity'				=>	$onh,
					'TotalCost'					=>	(float) $product->cost*$onh,
					'SalesCommissionItem'		=>	(bool) false,
					'ID'						=>	$productPastelID,
				);
				$result = do_pastel_call('Item/Save', $p_args, $updatedProduct);
			}
		}
	}

	function checkGRV($siteGUID) {
		global $db;

		$rows = $db->get_results("select * from mh where siteguid = '".$siteGUID."' and movetype in (1,2) and pastelid = 0");
		foreach ($rows as $row) {
			if ($row->movetype == 1) {
				sendSupplierInvoice($row->guid);
			} elseif ($row->movetype == 2) {
				sendSupplierReturn($row->guid);
			}
		}
	}

	function sendSupplierInvoice($moveGUID) {
		global $db;
		$header = $db->get_row("select guid,siteguid,datetime,excl,vat,incl,refnr,movetype,accguid from mh where guid = '".$moveGUID."' and pastelid = 0 and movetype in (1) order by datetime desc");
		if (!empty($header)) {
			$siteGUID = $header->siteguid;
			$pastelID = $db->get_var("select pastelid from sites where guid = '".$siteGUID."'");
			$p_args = array('companyid'	=> $pastelID);
			$invPastelID = get_pastel_id($siteGUID,$header->accguid);

			$inv = array(
				"DueDate"        => $header->datetime,
				"SupplierId"     => $invPastelID,
				"Date"           => $header->datetime,
				"Inclusive"      => $header->incl,
				"DocumentNumber" => $header->guid, 
				"Reference"      => $header->refnr,
				"Exclusive"      => $header->excl,
				"Tax"            => $header->vat,
				"Rounding"       => 0,
				"Total"          => $header->incl,
				"AmountDue"      => $header->incl,
			);

			$lines = array();
			$ml = $db->get_results("select * from ml where guid = '".$moveGUID."' order by line desc");
			foreach ($ml as $row) {
				$product = $db->get_row("select * from products where guid = '".$row->productguid."'");
				$productPastelID = get_pastel_id($siteGUID,$row->productguid);
				$line = array(
					'LineType'           => 0,
					'SelectionId'        => $productPastelID,
					'TaxTypeId'          => 0,
					'Description'        => $row->descr,
					'Quantity'           => $row->qty,
					'UnitPriceExclusive' => $row->unitcost,
					'TaxPercentage'      => $product->vat/100,
					'DiscountPercentage' => 0,
					'Exclusive'          => $row->qty*$row->unitcost,
				);
				$lines[] = $line;
			}
			$inv['Lines'] = $lines;

			$result = do_pastel_call('SupplierInvoice/Save',$p_args,$inv);
			if (!empty($result['ID'])) {
				if (is_numeric($result['ID'])) {
					$db->update('mh', array('pastelid' => $result['ID']),array('guid' => $moveGUID, 'pastelid' => 0));
				} else {
					logtodb("checkGRV",$result);
					var_dump($result);
				}
			}
		}
	}

	function sendSupplierReturn($moveGUID) {
		global $db;
		$header = $db->get_row("select guid,siteguid,datetime,excl,vat,incl,refnr,movetype,accguid from mh where guid = '".$moveGUID."' and pastelid = 0 and movetype in (2) order by datetime desc");
		if (!empty($header)) {
			$siteGUID = $header->siteguid;
			$pastelID = $db->get_var("select pastelid from sites where guid = '".$siteGUID."'");
			$p_args = array('companyid'	=> $pastelID);
			$invPastelID = get_pastel_id($siteGUID,$header->accguid);

			$inv = array(
				"DueDate"        => $header->datetime,
				"SupplierId"     => $invPastelID,
				"Date"           => $header->datetime,
				"Inclusive"      => $header->incl,
				"DocumentNumber" => $header->guid, 
				"Reference"      => $header->refnr,
				"Exclusive"      => $header->excl,
				"Tax"            => $header->vat,
				"Rounding"       => 0,
				"Total"          => $header->incl,
				"AmountDue"      => $header->incl,
			);

			$lines = array();
			$ml = $db->get_results("select * from ml where guid = '".$moveGUID."' order by line desc");
			foreach ($ml as $row) {
				$product = $db->get_row("select * from products where guid = '".$row->productguid."'");
				$productPastelID = get_pastel_id($siteGUID,$row->productguid);
				$line = array(
					'LineType'           => 0,
					'SelectionId'        => $productPastelID,
					'TaxTypeId'          => 0,
					'Description'        => $row->descr,
					'Quantity'           => $row->qty,
					'UnitPriceExclusive' => $row->unitcost,
					'TaxPercentage'      => $product->vat/100,
					'DiscountPercentage' => 0,
					'Exclusive'          => $row->qty*$row->unitcost,
				);
				$lines[] = $line;
			}
			$inv['Lines'] = $lines;

			
			$result = do_pastel_call('SupplierReturn/Save',$p_args,$inv);
			if (!empty($result['ID'])) {
				if (is_numeric($result['ID'])) {
					$db->update('mh', array('pastelid' => $result['ID']),array('guid' => $moveGUID, 'pastelid' => 0));
				} else {
					logtodb("checkGRV",$result);
					var_dump($result);
				}
			}
		}
	}

	function insertPastelTranslate($siteGUID,$itemGUID,$pastelID) {
		//console("[$siteGUID][$itemGUID][$pastelID]");
		global $db;

		if ($pastelID != 0) {
			$pasteltranslate = array(
				"siteguid"  => $siteGUID,
				"itemguid"  => $itemGUID,
				"pastelid"  => $pastelID,
				"lastcheck" => date('Y-m-d H:i:s'),
			);
			$db->delete('pasteltranslate',array('siteguid' => $siteGUID, 'itemguid' => $itemGUID));
			$db->insert("pasteltranslate",$pasteltranslate);		
		} else {
			logtodb("insertPastelTranslate","insert blank pastelid $siteGUID | $itemGUID | $pastelID");
		}
	}

	function checkPastelSales() {
		global $db;
		$sites = $db->get_results("select guid, coguid, pastelid, pastelhash,sitename from sites where pastelid != 0 and pastelhash != '' and guid in (select distinct siteguid from sh where pastelid = 0)");
		foreach ($sites as $site) {
			$sh = $db->get_results("select guid, pastelid from sh where siteguid = '".$siteguid."' and pastelid = 0 order by datetime desc");
			foreach ($sh as $sale) {
				upload_sale_pastel($sale->guid);
			}
		}
	}

	function rebateClaims($siteguid) {
		global $db;

		$siteguid = '9D0C80EB-9322-43B9-B235-63A2177D7725';
		$pastelID = $db->get_var("select pastelid from sites where guid = '".$siteguid."'");
		$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");
		$p_args = array( 
			'companyid'	=> $pastelID,
		);
		$deviceclaimguid = $db->get_var("select guid from community where companyguid = '".$companyguid."' and descr = 'Cell C Device Claims'");
		$deviceclaimpastelid = $db->get_var("select pastelid from pasteltranslate where siteguid = '".$siteguid."' and itemguid = '".$deviceclaimguid."'");

		$sh = $db->get_results("select guid,datetime from sh where siteguid = '".$siteguid."' and guid in (select guid from sl where rebate != 0 and pastelclaimid = 0) order by datetime desc"); 
		foreach ($sh as $header) {
			$sl = $db->get_results("select * from sl where guid = '".$header->guid."' and rebate != 0 and pastelclaimid = 0");
			foreach ($sl as $line) {
				$duedate = date('Y-m-d H:i:s',strtotime('+30 days'));
				$productPastelID = $db->get_var("select pastelid from pasteltranslate where siteguid = '".$siteguid."' and itemguid = '".$line->productguid."'");
				$descr = $db->get_var("select descr from products where guid = '".$line->productguid."'");
				$imei = $line->imei;
				$excl = $line->rebate;
				$vat = $excl*0.14;
				$vat = 687.84;
				$incl = $excl+$vat;

				$msg = sprintf("IMEI: $imei");
				if ($line->msisdn != 'N/A') { $msg = sprintf("$msg\nMSISDN: $line->msisdn"); }
				if ($line->email != 'N/A') { $msg = sprintf("$msg\nemail: $line->email"); }
				$notes = $db->get_results("select * from basketnotes where saleguid = '".$header->guid."'");
				foreach ($notes as $note) {
					$msg = sprintf("$msg\n$note->note");
				}
				
				$claim = array(
					"DueDate"        => $duedate,
					"CustomerId"     => $deviceclaimpastelid,
					"Date"           => $header->datetime,
					"Inclusive"      => false,
					"DocumentNumber" => "", 
					"Reference"      => $header->guid,
					"Exclusive"      => $excl,
					"Tax"            => $vat,
					"Rounding"       => 0,
					"Total"          => $incl,
					"AmountDue"      => true,
					"Printed"        => false,
					"Message"        => $msg,
				);

				$lines = array();
				$lines[] = array(
					"LineType"           => 0,
					"SelectionId"        => $productPastelID,
					"Description"        => $descr,
					"Quantity"           => $line->qty,
					"UnitPriceExclusive" => $line->rebate,
					"TaxPercentage"      => 0.14,
					"Exclusive"          => $line->rebate,
				);
				$claim['Lines'] = $lines;
				$result = do_pastel_call('TaxInvoice/Save',$p_args,$claim);
				if (!empty($result['ID'])) {
					if (is_numeric($result['ID'])) {
						$db->update('sl', array('pastelclaimid' => $result['ID']),array('guid' => $header->guid,'line' => $line->line, 'pastelclaimid' => 0));
					} else {
						logtodb("checkGRV",$result);
						var_dump($result);
					}
				}
			}
		}
	}

?>