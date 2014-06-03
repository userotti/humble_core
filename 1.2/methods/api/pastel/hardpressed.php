<?php
	console("will.php");

	$siteguid    = '0C8213F4-E2CC-4396-9F70E74E2A6B978B';
	$companyguid = '2D8B9217-0BB3-405B-AF1E7702F6265A32';
	$pos         = array();
	$neg         = array();
	$posguid     = 'f5b904cd-e498-11e3-b186-005056ba5bac';
	$negguid     = 'f83fa604-e498-11e3-b186-005056ba5bac';

	$products = $db->get_results("select * from temp_products");
	foreach ($products as $product) {
		$guid      = $product->guid;
		$stockcode = $product->stockcode;
		$descr     = $product->descr;
		$cat       = $product->cat;
		$cost      = $product->cost;
		$sell      = $product->sell;
		$vat       = $product->vat;
		$units     = $product->units;

		if (empty($guid) && !empty($stockcode)) {
			console("empty $guid with stockcode $stockcode");
			$guid = gen_uuid();
			$db->update("temp_products",array('guid' => $guid),array('stockcode' => $stockcode));
		}

		$live = $db->get_var("select live from products where companyguid = '".$companyguid."' and guid = '".$guid."'");
		if (empty($stockcode) && $live == 1) {
			console("$guid is live");
			$db->update("products",array('live' => 0),array('guid' => $guid));
		}

		$exist = $db->get_var("select guid from products where companyguid = '".$companyguid."' and guid = '".$guid."'");
		if (empty($exist)) {
			console("create product $guid");
			$ins = array(
				'guid'        => $guid,
				'companyguid' => $companyguid,
				'stockcode'   => $stockcode,
				'descr'       => $descr,
				'cat'         => $cat,
				'cost'        => $cost,
				'sell'        => $sell,
				'vat'         => $vat,
				'live'        => 1,
			);
			$db->insert("products",$ins);
		}		

		$fields = array(
			'stockcode',
			'descr',
			'cat',
			'cost',
			'sell',
			'vat',
		);

		if (!empty($stockcode)) {
			foreach ($fields as $field) {
				$val = $db->get_var("select ".$field." from products where guid = '".$guid."'");
				if ($product->$field != $val) {
					console("diff in [$guid] for $field");
					$db->update("products",array($field => $product->$field),array('guid' => $guid));
				}
			}
		}

		$onh = get_onhand($siteguid,$guid);
		

		if ($onh != $units) {
			if ($onh < $units) {
				$pos[] = array(
					'guid' => $guid,
					'qty' => $units-$onh,
				);
			} else {
				$neg[] = array(
					'guid' => $guid,
					'qty' => $onh-$units,
				);
			}
		}
		$db->delete("confirmed",array('itemguid' => $guid));
	}

	foreach ($pos as $row) {
		$exist = $db->get_var("select guid from ml where guid = '".$posguid."' and productguid = '".$row['guid']."'");
		if (empty($exist)) {
			$line = $db->get_var("select max(line)+1 from ml where guid = '".$posguid."'");
			if (empty($line)) { $line = 0; }
			$cost = $db->get_var("select cost from products where guid = '".$row['guid']."'");
			$ins = array(
				'guid' => $posguid,
				'line' => $line,
				'productguid' => $row['guid'],
				'descr' => $db->get_var("select descr from products where guid = '".$row['guid']."'"),
				'qty' => abs($row['qty']),
				'unitcost' => $cost,
				'linecost' => abs($row['qty'])*$cost,
				'linevat' => 0,
				'lineincl' => abs($row['qty'])*$cost,
				'serial' => 'N/A',
			);
			$db->insert("ml",$ins);
		}
	}

	console($neg);
	foreach ($neg as $row) {
		$exist = $db->get_var("select guid from ml where guid = '".$negguid."' and productguid = '".$row['guid']."'");
		if (empty($exist)) {
			$line = $db->get_var("select max(line)+1 from ml where guid = '".$negguid."'");
			if (empty($line)) { $line = 0; }
			$cost = $db->get_var("select cost from products where guid = '".$row['guid']."'");
			$ins = array(
				'guid' => $negguid,
				'line' => $line,
				'productguid' => $row['guid'],
				'descr' => $db->get_var("select descr from products where guid = '".$row['guid']."'"),
				'qty' => abs($row['qty']),
				'unitcost' => $cost,
				'linecost' => abs($row['qty'])*$cost,
				'linevat' => 0,
				'lineincl' => abs($row['qty'])*$cost,
				'serial' => 'N/A',
			);
			$db->insert("ml",$ins);
		}
	}

?>