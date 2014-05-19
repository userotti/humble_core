<?php
/*

Caldoza Engine ------------------------

File	:	api/reports/audit.php
Created	: 	2013-12-18

*/

//console( $_GET );
//console( $params );

//error_log($_GET);

$wheres = array();

if(isset($_GET['start_date'])){
	$daterange = " `datetime`  >= '".date('Y-m-d', strtotime($_GET['start_date']))." 00:00:00' ";
	if(isset($_GET['end_date'])){
		$daterange = " ( ".$daterange." AND `datetime`  <= '".date('Y-m-d', strtotime($_GET['end_date']))." 23:59:59' ) ";
	}
	$wheres[] = $daterange;
}
if(!empty($_GET)){

	$fields = array(
		'productguid',
		'guid',
		'siteguid',
		'deviceguid',
		'cashierguid',
		'agent',
		'accguid',
		'movetype'
	);
	foreach($_GET as $field=>$value){

		if(is_array($value)){

			$pre = array();
			foreach($value as $option){
				$pre[] = " `".$field."` = '".$option."' ";
			}
			$wheres[] = " ( " . implode(' OR ', $pre) . " ) ";

		}else{
			if(in_array($field, $fields)){
				$wheres[] = " `".$field."` = '".$value."' ";
				$continue = true;
			}
		}
	}
}
$filter = null;
if(!empty($wheres)){
	$filter = " WHERE ".implode(" AND ", $wheres);
}
if(empty($continue)){
	return array('message' => "site or cashier guid is required.");
}
if($params['type'] == 'audit'){
	$return = $db->get_results("SELECT * FROM `audit` ".$filter);
}elseif ($params['type'] == 'cashup') {
	$return = $db->get_results("SELECT * FROM `cashups` ".$filter);

	if(!empty($_GET['template'])){

		$users = array();
		foreach($return as &$row){		

			if(!isset($users[$row->cashierguid])){
				$users[$row->cashierguid] = $db->get_var("SELECT CONCAT(`fname`, ' ', `sname`) FROM `users` WHERE `uguid` = '".$row->cashierguid."';");
			}

			$row->cashier = $users[$row->cashierguid];

		}

	}


}elseif ($params['type'] == 'sale') {

	$return = array();
	$headers = $db->get_results("SELECT * FROM `sh` ".$filter." ORDER BY `datetime` ASC");
	if(!empty($headers)){
		$return = array();
		foreach($headers as $header){
			$return[$header->guid] = $header;
			$tenders = $db->get_results("SELECT * FROM `tenders` WHERE `saleguid` = '".$header->guid."' ");
			$lines = $db->get_results("SELECT * FROM `sl` WHERE `guid` = '".$header->guid."' ");
			$return[$header->guid]->lines = $lines;
			$return[$header->guid]->tenders = $tenders;
		}
	}

}elseif ($params['type'] == 'movement') {
	
	$return = array();
	$headers = $db->get_results("SELECT * FROM `mh` ".$filter." ORDER BY `datetime` ASC");
	if(!empty($headers)){
		$return = array();
		foreach($headers as $header){
			$return[$header->guid] = $header;
			$moveimeis = $db->get_results("SELECT * FROM `moveimeis` WHERE `guid` = '".$header->guid."' ");
			$lines = $db->get_results("SELECT * FROM `ml` WHERE `guid` = '".$header->guid."' ");
			$return[$header->guid]->lines = $lines;
			$return[$header->guid]->moveimeis = $moveimeis;
		}
	}
	//return array('message'=>'waiting for will.... again');
}elseif ($params['type'] == 'onhand') {
	
	recon_movement();

	$siteguid = $_GET['siteguid'];
	$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");

	$products = $db->get_results($db->prepare("


	SELECT
		`guid` AS `productguid`,
		(SELECT
		SUM(`qty`*`movedir`) AS `total`
	FROM `movement`
	WHERE
		`productguid` = `products`.`guid`
	AND
		`siteguid` = %s
	) AS `on_hand`
	FROM
		`products`
	WHERE
		`companyguid` = %s ;
		", $siteguid,$companyguid), ARRAY_A);

	if(empty($products)){
		//dump($db);
		return array('message'=>'no products available');
	}
	$stock_total = 0;
	foreach($products as $key=>&$product){
		if((int) $product['on_hand'] === 0){
			unset( $products[$key] );
		}else{
			$product['on_hand'] = (int) $product['on_hand'];
			$stock_total += $product['on_hand'];
		}
	}
	sort($products);
	$out['message'] = 'OK';
	$out['total'] = count($products);
	$out['on_hand_total'] = $stock_total;
	$out['data'] = $products;
	return $out;
}elseif ($params['type'] == 'product-audit'){
	
	$results = $db->get_results("SELECT * FROM `movement` ".$filter);
	
	$out['message'] = 'OK';
	$out['data'] = $results;
	return $out;
}elseif ($params['type'] == 'payouts'){
	
	$results = $db->get_results("SELECT * FROM `payouts` ".$filter);
	
	$out['message'] = 'OK';
	$out['data'] = $results;
	return $out;
} elseif ($params['type'] == 'imeionhand') {
	$return = array();
	$siteguid = $_GET['siteguid'];
	$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");
	recon_imei();
	$arr = $db->get_results("select guid,stockcode,descr,cat from products where companyguid = '".$companyguid."' and si = 1 order by descr");
	foreach ($arr as $rec) {
		$category = $db->get_var("select category from categories where companyguid = '".$companyguid."' and cat = ".$rec->cat);
		$imeis = $db->get_results("select imei from imeihistory where siteguid = '".$siteguid."' and productguid = '".$rec->guid."' group by imei having sum(movedir) > 0");
		foreach ($imeis as $imei) {
			$imeicost = $db->get_var("select cost from imeicost where companyguid = '".$companyguid."' and productguid = '".$rec->guid."' and imei = '".$imei->imei."'");
			if (empty($imeicost)) { $imeicost = 0; }
			$line = array(
				'category'    => $category,
				'productguid' => $rec->guid,
				'sku'         => $rec->stockcode,
				'descr'       => $rec->descr,
				'imei'        => $imei->imei,
				'imeicost'    => $imeicost,
			);
			$return[] = $line;
		}
	}
} elseif ($params['type'] == 'imeiaudit') {
	$return = array();
	$siteguid = $_GET['siteguid'];
	$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");
	$serial = $_GET['serial'];
	recon_imei();
	$return = array();

	$arr = $db->get_results("select * from imeihistory where imei = '".$serial."' and siteguid in (select guid from sites where coguid = '".$companyguid."') order by datetime asc");
	foreach ($arr as $rec) {
		$sitename = $db->get_var("select sitename from sites where guid = '".$rec->siteguid."'");
		$movedescr = $db->get_var("select movedescr from movetypes where movecode = '".$rec->movetype."'");
		$moveref = $db->get_var("select refnr from mh where guid = '".$rec->moveguid."'");
		$descr = $db->get_var("select descr from products where guid = '".$rec->productguid."'");

		$line = array(
			'datetime' => $rec->datetime,
			'site' => $sitename,
			'movedescr' => $movedescr,
			'moveref' => $moveref,
			'descr' => $descr,
			'imei' => $rec->imei,
			'imeicost' => $rec->imeicost,
		);
		$return[] = $line;
	}
} elseif ($params['type'] == 'serialageanalysis') {
	$return = array();
	$siteguid = $_GET['siteguid'];
	$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");
	$date0 = date('Y-m-d H:i:s');
	$serials = $db->get_results("select productguid, imei, sum(movedir) onh from imeihistory where siteguid = '".$siteguid."' group by productguid,imei having sum(movedir) = 1");
	
	foreach ($serials as $row) {
		$product = $db->get_row("select * from products where guid = '".$row->productguid."'");
		$first = $db->get_row("select * from imeihistory where siteguid = '".$siteguid."' and productguid = '".$row->productguid."' and imei = '".$row->imei."' order by datetime asc");
		$date1 = $first->datetime;
		$interval = date_diff(date_create($date0),date_create($date1));
		$days = $interval->days;
		$category = 'Unknown';
		if ($days <= 30) {
			$category = '30 Days';
		} elseif ($days > 30 && $days <= 60) {
			$category = '60 Days';
		} elseif ($days > 60 && $days <= 90) {
			$category = '90 Days';
		} else {
			$category = 'More than 90 days';
		}
		$line = array(
			'productguid' => $row->productguid,
			'stockcode'   => $product->stockcode,
			'descr'       => $product->descr,
			'serial'      => $row->imei,
			'cost'        => $first->imeicost,
			'firstdate'   => $date1,
			'category'    => $category,
		);
		$return[] = $line;
	}
} elseif ($params['type'] == 'oldstock') {
	$return = array();
	$siteguid = $_GET['siteguid'];
	$out['siteguid'] = $siteguid;
	$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");

	$products = $db->get_results("select guid,stockcode,descr,cost,si from products where companyguid = '".$companyguid."'");
	foreach ($products as $product) {
		$onh = get_onhand($siteguid,$product->guid);
		if ($onh != 0) {
			$move = $db->get_var("select sum(movedir*qty) move from movement where siteguid = '".$siteguid."' and productguid = '".$product->guid."' and date(datetime)+30 > date(now())");
			if ($move == 0) {
				$line = array(
					'productguid' => $product->guid,
					'stockcode' => $product->stockcode,
					'descr' => $product->descr,
					'cost' => $product->cost,
					'onh' => $onh,
					'diff' => $onh*$product->cost,
				);
				$return[] = $line;
			}
		}
	}
} elseif ($params['type'] == 'topselling') {
	$return = array();
	$siteguid = $_GET['siteguid'];
	$sdate = $_GET['start_date'];
	$edate = $_GET['end_date']+1;
	$out['siteguid'] = $siteguid;
	$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");

	$sell = $db->get_results("select productguid, sum(qty) sls from movement where siteguid = '".$siteguid."' and movetype = 8 and datetime >= '".$sdate."' and datetime < '".$edate."' group by productguid order by sum(qty) desc");
	foreach ($sell as $row) {
		$product = $db->get_row("select stockcode,descr from products where guid = '".$row->productguid."'");
		$line = array(
			'productguid' => $row->productguid,
			'stockcode'   => $product->stockcode,
			'descr'       => $product->descr,
			'sls'         => $row->sls,
		);
		$return[] = $line;
	}
} elseif ($params['type'] == 'outofstock') {
	$return = array();
	$siteguid = $_GET['siteguid'];
	$date1 = date("Y-m-d H:i:s",strtotime("-1 month"));
	$date2 = date("Y-m-d H:i:s",strtotime("-7 day"));
	$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");
	$sell = $db->get_results("select productguid, sum(qty) sls from movement where siteguid = '".$siteguid."' and movetype = 8 and datetime >= '".$date1."' group by productguid order by sum(qty) desc");
	foreach ($sell as $row) {
		$onh = get_onhand($siteguid,$row->productguid);
		if ($onh == 0) {
			$product = $db->get_row("select stockcode,descr from products where guid = '".$row->productguid."'");
			$sls2 = $db->get_var("select sum(qty) sls from movement where siteguid = '".$siteguid."' and productguid ='".$row->productguid."' and movetype = 8 and datetime >= '".$date2."' group by productguid order by sum(qty) desc");
			$line = array(
				'productguid' => $row->productguid,
				'stockcode'   => $product->stockcode,
				'descr'       => $product->descr,
				'sls1'        => $row->sls,
				'sls2'        => $sls2,
			);
			$return[] = $line;
		}
	}
}

 
$out['message'] = 'OK';

if(!empty($return)){
	$out['total'] = count($return);
	$out['data']	= $return;
}

return $out;