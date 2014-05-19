<?php
	global $user;
	$user = new stdClass;

	require_once("func.php");

	//logtodb("pastel_sync","Start Pastel Sync Run");

	mail_logs();

	$sites = $db->get_results("select guid, coguid, pastelid, pastelhash from sites where pastelid != 0 and pastelhash != '' ");
	foreach ($sites as $site) {
		$user->siteguid = $site->guid;
		$user->cguid = $site->coguid;
		checkCashCustomer($site->guid);
		checkProducts($site->guid);
		checkSuppliers($site->guid);
		checkCustomers($site->guid);
		checkGRV($site->guid);
		getCashCustomerPastelID($site->guid);
		rebateClaims($site->guid);


	}	




	$sites = $db->get_results("select guid, coguid, pastelid, pastelhash,sitename from sites where pastelid != 0 and pastelhash != '' and guid in (select distinct siteguid from sh where pastelid = 0)");
	if (count($sites) == 0) {
		//console("No Sites have sales");
		//logtodb("will check","No Sites have sales outstanding");
	} else {
		foreach ($sites as $site) {

			$siteguid = $site->guid;
			$user->siteguid = $siteguid;
			$user->cguid = $site->coguid;
			$pastelid = $site->pastelid;
			$pastelhash = $site->pastelhash;

			$sh = $db->get_results("select guid, pastelid from sh where siteguid = '".$siteguid."' and pastelid = 0 order by datetime desc");
		
			foreach ($sh as $sale) {
				$saleGUID = $sale->guid;
				$ret = upload_sale_pastel($saleGUID);
			}
		}
	}


	checkAutomatedTransfers();
	fixDuplicateCustomer();




	//logtodb("pastel_sync","End Pastel Sync Run");

	mail_logs();
	




























?>