<?php

	$result = array();
	$guid = $_POST['guid'];
	$datetime = ( isset( $_POST['datetime'] ) ? $_POST['datetime'] : date('Y-m-d H:i:s') );
	$takeguid = $_POST['takeguid'];
	$siteguid = ( isset( $_POST['siteguid'] ) ? $_POST['siteguid'] : $user->siteguid );
	$deviceguid = ( isset( $_POST['deviceguid'] ) ? $_POST['deviceguid'] : $user->deviceGUID );
	$devicename = ( isset( $_POST['devicename'] ) ? $_POST['devicename'] : $user->deviceName );
	$userguid = $user->uguid;
	$productguid = $_POST['productguid'];
	$productdescr = $db->get_var("select descr from products where guid = '".$productguid."'");
	$qty = $_POST['qty'];
	$imei = $_POST['imei'];

	$result['guid'] = $guid;
	$result['message'] = "Not OK";

	$check = $db->get_var("select guid from stocktake_counted where guid = '".$guid."'");
	$result['check'] = $check;
	if (empty($check)) {
		$ins = array(
			'guid'         => $guid,
			'datetime'     => $datetime,
			'takeguid'     => $takeguid,
			'siteguid'     => $siteguid,
			'deviceguid'   => $deviceguid,
			'devicename'   => $devicename,
			'userguid'     => $userguid,
			'productguid'  => $productguid,
			'productdescr' => $productdescr,
			'qty'          => $qty,
			'imei'         => $imei,
		);
		$db->insert('stocktake_counted',$ins);
		$confirm = array(	
			'itemguid'   => $guid,
			'deviceguid' => $deviceguid,
		);		
		$db->insert('confirmed',$confirm);
		$result['message'] = "OK";
	} else {
		$result['message'] = "Already Inserted";
	}
	return $result;



	/*$stock = $db->get_row($db->prepare("SELECT * FROM `stocktake_counted` WHERE `guid` = %s;", $_POST['guid']), ARRAY_A);

	console($stock);

	if(empty($stock)){
		console("stock is empty");
		$descr = $db->get_var($db->prepare("SELECT `descr` FROM `products` WHERE `guid` = %s AND `companyguid` = %s;", $_POST['productguid'], $user->cguid));
		$newitem = array(
			'guid'        => gen_uuid(),
			'siteguid'    => $user->siteguid,
			'productguid' => $_POST['productguid'],
			'userguid'    => $user->userGUID,
			'takeguid'    => $_POST['takeguid'],
			'qty'         => $_POST['qty'],
			'imei'        => $_POST['imei'],
		);
		$newitem = array_merge($newitem, $_POST);
		$db->insert('stocktake_counted', $newitem);
	}else{
		console("stock is empty");
		$stock['qty'] = $_POST['qty'];
		$db->update('stocktake_counted', $stock, array('productguid'=> $stock['productguid'], 'siteguid'=>$user->siteguid));
	}

	$arr = array('message'=>'OK', 'guid'=>$_POST['guid']);
	console($arr);
	return $arr;*/