<?php

	$st_session = $db->get_row("SELECT * FROM `stocktake_log` WHERE `siteguid` = '".$user->siteguid."' AND `enddate` IS NULL;", ARRAY_A);
	if(empty($st_session)){
	// create a new session
		$st_session = array(
			'guid'		=>	gen_uuid(),
			'siteguid'	=>	$user->siteguid,
			'userguid'	=>	$user->userGUID
		);
		$db->insert('stocktake_log', $st_session);
	}


	$items = $db->get_results($db->prepare("SELECT `guid` AS `siteguid`,`guid` AS `productguid`,`cat`,`descr` AS `productdescr`,(SELECT SUM(`qty`*`movedir`) AS `total` FROM `movement` 
		WHERE `productguid` = `products`.`guid` AND `siteguid` = %s) AS `onhand` FROM `products` WHERE `companyguid` = %s ", $user->siteguid, $user->cguid), ARRAY_A);
	$itemsout = array();
	$inc = 0;
	foreach($items as $key=>&$item){
		$ext = $db->get_row("SELECT SUM(`qty`) as `counted`, `imei` FROM `stocktake_counted` WHERE `takeguid` = '".$st_session['guid']."' AND `productguid` = '".$item['productguid']."';");
		if( ( $item['onhand'] === '0' || $item['onhand'] === null ) && $ext->counted === null){
			continue;
		}
		$itemsout[$inc]['siteguid'] = $user->siteguid;
		$itemsout[$inc]['productguid'] = $item['productguid'];
		$itemsout[$inc]['productdescr'] = $item['productdescr'];
		$itemsout[$inc]['productcat'] = $item['cat'];
		$itemsout[$inc]['onhand'] = (string) ( $item['onhand'] != null ? $item['onhand'] : 0 );
		$itemsout[$inc]['counted'] = (string) ( $ext->counted != null ? $ext->counted : 0 );
		$cost = $db->get_var("select cost from products where guid = '".$item['productguid']."'");
		if (empty($cost)) { 
			$cost = 0;
		} 
		$itemsout[$inc]['cost'] = $cost;
		$itemsout[$inc]['costdiff'] = $cost*($itemsout[$inc]['counted']-$itemsout[$inc]['onhand']);
		$itemsout[$inc]['imei'] = ( $ext->imei != null ? $ext->imei : '');		
		$inc++;
	}

	$out['total_items'] = 0;
	$out['percentage_completed'] = 0;
	$out['inventory_cost_difference'] = 0;
	$out['items_to_count'] = 0;
	$out['items_with_differences'] = 0;
	foreach ($itemsout as $row) {
		$out['total_items']++;
		if ($row['counted'] == 0) {
			$out['items_to_count']++;
		}
		if ($row['onhand'] != $row['counted'] && $row['counted'] != 0) {
			$out['items_with_differences']++;			
		}
		if ($row['onhand'] != $row['counted']) {
			$out['inventory_cost_difference'] = $out['inventory_cost_difference']+$row['costdiff'];
		}
	}
	$out['percentage_completed'] = ($out['total_items']-$out['items_to_count'])*100/$out['total_items'];
	$out['percentage_completed'] = round($out['percentage_completed'], 0);
	$out['inventory_cost_difference'] = round($out['inventory_cost_difference'], 2);

	$out['message'] = 'OK';
	$out['guid'] = $st_session['guid'];
	$out['items'] = $itemsout;

	return $out;