<?php
	$table = $params['table'];
	$guid = $params['guid'];
	$data = json_decode($_POST['data']);
	$siteguid = $user->siteguid;
	$companyguid = $user->cguid;
	$deviceguid = $user->deviceGUID;
	$channelguid = $db->get_var("select channelguid from companies where guid = '".$companyguid."'");
	$result = array(
		'message' => 'Unknown Table',
		'table'   => $table,
		'guid'    => $guid,
		'line'    => -1,
		'serial'    => 'N/A',
	);

	if ($table == 'Audit') {
		$exist = $db->get_var("select guid from audit where guid = '".$data->guid."'");
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid'        => $data->guid,
				'datetime'    => $data->insdate,
				'siteguid'    => $data->siteguid,
				'deviceguid'  => $data->deviceguid,
				'cashierguid' => $data->cashierguid,
				'note'        => $data->note,
			);
			$cool = $db->insert("audit",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'BasketNotes') {
		$exist = $db->get_var("select guid from basketnotes where guid = '".$data->guid."'");
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid'        => $data->guid,
				'siteguid'    => $data->siteguid,
				'saleguid'    => $data->saleguid,
				'basketguid'  => $data->basketguid,
				'basketline'  => $data->basketline,
				'cashierguid' => $data->cashierguid,
				'note'        => $data->note,
				'live'        => $data->live,
			);
			$cool = $db->insert("basketnotes",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Cashups') {
		$exist = $db->get_var("select guid from cashups where guid = '".$data->guid."'");
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid' => $data->guid,
				'siteguid' => $data->siteguid,
				'datetime' => $data->datetime,
				'cashierguid' => $data->cashierguid,
				'cash' => $data->cash,
				'ccard' => $data->ccard,
				'dcard' => $data->dcard,
				'acc' => $data->acc,
				'snapscan' => $data->snapscan,
				'declarecash' => $data->declarecash,
				'declareccard' => $data->declareccard,
				'declaredcard' => $data->declaredcard,
				'declareacc' => $data->declareacc,
				'declaresnapscan' => $data->declaresnapscan,
				'floatamount' => $data->floatamount,
				'bank' => $data->bank,
				'deviceguid' => $data->deviceguid,
				'devicename' => $data->devicename,
			);
			$cool = $db->insert("cashups",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Categories') {
		$exist = $db->get_var("select guid from categories where guid = '".$data->guid."'");
		if ($exist) {
			$upd = array(
				'companyguid' => $companyguid,
				'cat'         => $data->cat,
				'category'    => $data->category,
				'live'        => $data->live,
			);
			$cool = $db->update("categories",$upd,array('guid' => $data->guid));
			if ($cool == 0) {
				$result['message'] = 'OK';
				$db->delete("confirmed",array('itemguid' => $data->guid));
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		} else {
			$ins = array(
				'guid'        => $data->guid,
				'companyguid' => $companyguid,
				'cat'         => $data->cat,
				'category'    => $data->category,
				'live'        => $data->live,
			);
			$cool = $db->insert("categories",$ins);
			if ($cool) {
				$result['message'] = 'OK';
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		}
	}

	if ($table == 'Community') {
		$exist = $db->get_var("select guid from community where guid = '".$data->guid."'");
		if ($exist) {
			$upd = array(
				'companyguid'   => $companyguid,
				'descr'         => $data->descr,
				'communitytype' => $data->communitytype,
				'live'          => $data->live,
			);
			$cool = $db->update("community",$upd,array('guid' => $data->guid));
			if ($cool == 0) {
				$result['message'] = 'OK';
				$db->delete("confirmed",array('itemguid' => $data->guid));
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		} else {
			$ins = array(
				'guid'          => $data->guid,
				'companyguid'   => $companyguid,
				'descr'         => $data->descr,
				'communitytype' => $data->communitytype,
				'live'          => $data->live,
			);
			$cool = $db->insert("community",$ins);
			if ($cool) {
				$result['message'] = 'OK';
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		}
	}

	if ($table == 'EAN') {
		$exist = $db->get_var("select guid from ean where guid = '".$data->guid."'");
		if ($exist) {
			$upd = array(
				'companyguid' => $companyguid,
				'productguid' => $data->productguid,
				'ean'         => $data->ean,
				'live'        => $data->live,
			);
			$cool = $db->update("ean",$upd,array('guid' => $data->guid));
			if ($cool == 0) {
				$result['message'] = 'OK';
				$db->delete("confirmed",array('itemguid' => $data->guid));
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		} else {
			$ins = array(
				'guid'        => $data->guid,
				'companyguid' => $companyguid,
				'productguid' => $data->productguid,
				'ean'         => $data->ean,
				'live'        => $data->live,
			);
			$cool = $db->insert("ean",$ins);
			if ($cool) {
				$result['message'] = 'OK';
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		}
	}

	if ($table == 'Moveheaders') {
		$exist = $db->get_var("select guid from mh where guid = '".$data->guid."'");
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid'        => $data->guid,
				'companyguid' => $companyguid,
				'siteguid'    => $data->siteguid,
				'datetime'    => $data->datetime,
				'movetype'    => $data->movetype,
				'movestate'   => $data->movestate,
				'accguid'     => $data->communityguid,
				'acc'         => $data->communitydescr,
				'refnr'       => $data->refnr,
				'deviceGUID'  => $data->deviceguid,
				'deviceName'  => $data->devicename,
				'userguid'    => $data->userguid,
				'excl'        => $data->excl,
				'vat'         => $data->vat,
				'incl'        => $data->incl,
				'direction'   => $data->direction,
			);
			$cool = $db->insert("mh",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Movelines') {
		$result['line'] = $data->line;
		$exist = $db->get_var("select guid from ml where guid = '".$data->guid."' and line = ".$data->line);
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid' => $data->guid,
				'line' => $data->line,
				'productguid' => $data->productguid,
				'descr' => $data->descr,
				'qty' => $data->qty,
				'unitcost' => $data->cost,
				'linecost' => $data->qty*$data->cost,
				'linevat' => $data->qty*$data->vat,
				'lineincl' => $data->qty*($data->cost+$data->vat),
			);
			$cool = $db->insert("ml",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Moveimeis') {
		$result['serial'] = $data->imei;
		$exist = $db->get_var("select guid from moveimeis where guid = '".$data->guid."' and imei = '".$data->imei."'");
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$movedir = 0;
			$direction = $db->get_var("select direction from mh where guid = '".$data->guid."'");
			if ($direction == 'IN') { $movedir = 1; }
			if ($direction == 'OUT') { $movedir = -1; }
			$ins = array(
				'guid'        => $data->guid,
				'productguid' => $data->productguid,
				'imei'        => $data->imei,
				'movedir'     => $movedir,
				'imeicost'    => $data->imeicost,
			);
			$cool = $db->insert("moveimeis",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Payouts') {
		$exist = $db->get_var("select guid from payouts where guid = '".$data->guid."'");
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid' => $data->guid,
				'datetime' => $data->datetime,
				'cashierguid' => $data->cashierguid,
				'siteguid' => $data->siteguid,
				'deviceguid' => $data->deviceguid,
				'devicename' => $data->devicename,
				'reason' => $data->reason,
				'amount' => $data->amount,
			);
			$cool = $db->insert("payouts",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Products') {
		$exist = $db->get_var("select guid from products where guid = '".$data->guid."'");
		if ($exist) {
			$upd = array(
				'companyguid' => $companyguid,
				'stockcode'   => $data->stockcode,
				'descr'       => $data->descr,
				'cat'         => $data->cat,
				'si'          => $data->si,
				'cost'        => $data->cost,
				'sell'        => $data->sell,
				'vat'         => $data->vat,
				'brand'       => $data->brand,
				'subtype'     => $data->subtype,
				'weight'      => $data->weight,
				'parent'      => $data->parent,
				'virtual'     => $data->virtual,
				'producttype' => $data->producttype,
				'live'        => $data->live,
				'printlabel'  => $data->printlabel,
			);
			$cool = $db->update("products",$upd,array('guid' => $data->guid));
			if ($cool == 0) {
				$result['message'] = 'OK';
				$db->delete("confirmed",array('itemguid' => $data->guid));
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		} else {
			$ins = array(
				'guid'        => $data->guid,
				'companyguid' => $companyguid,
				'stockcode'   => $data->stockcode,
				'descr'       => $data->descr,
				'cat'         => $data->cat,
				'si'          => $data->si,
				'cost'        => $data->cost,
				'sell'        => $data->sell,
				'vat'         => $data->vat,
				'brand'       => $data->brand,
				'subtype'     => $data->subtype,
				'weight'      => $data->weight,
				'parent'      => $data->parent,
				'virtual'     => $data->virtual,
				'producttype' => $data->producttype,
				'live'        => $data->live,
				'printlabel'  => $data->printlabel,
			);
			$cool = $db->insert("products",$ins);
			if ($cool) {
				$result['message'] = 'OK';
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		}
	}

	if ($table == 'Saleheaders') {
		$exist = $db->get_var("select guid from sh where guid = '".$data->guid."'");
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid'              => $data->guid,
				'datetime'          => $data->datetime,
				'siteguid'          => $data->siteguid,
				'excl'              => $data->excl,
				'vat'               => $data->vat,
				'incl'              => $data->incl,
				'cash'              => $data->cash,
				'ccard'             => $data->ccard,
				'dcard'             => $data->dcard,
				'cheq'              => 0,
				'voucher'           => 0,
				'acc'               => $data->acc,
				'cashier'           => $data->cashier,
				'agent'             => $data->agent,
				'deviceName'        => $data->devicename,
				'deviceguid'        => $data->deviceguid,
				'longitude'         => $data->longitude,
				'latitude'          => $data->latitude,
				'tender_total'      => $data->tender_total,
				'tender_change'     => $data->tender_change,
				'customertaxnumber' => $data->customertaxnumber,
			);
			$cool = $db->insert("sh",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Salelines') {
		$result['line'] = $data->line;
		$exist = $db->get_var("select guid from sl where guid = '".$data->guid."' and line = ".$data->line);
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid'        => $data->guid,
				'line'        => $data->line,
				'productguid' => $data->productguid,
				'qty'         => $data->qty,
				'cost'        => $data->cost,
				'sell'        => $data->sell,
				'disc'        => $data->disc,
				'vat'         => $data->vat,
				'siteguid'    => $siteguid,
				'tariffguid'  => $data->tariffguid,
				'rebate'      => $data->rebate,
				'basketguid'  => $data->basketguid,
				'basketline'  => $data->basketline,
				'msisdn'      => $data->msisdn,
				'imei'        => $data->imei,
				'email'       => $data->email,
				'saletype'    => $data->saletype,
			);
			$cool = $db->insert("sl",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Saletypes') {
		$exist = $db->get_var("select guid from saleTypes where guid = '".$data->guid."'");
		if ($exist) {
			$upd = array(
				'channel'   => $channelguid,
				'ord'       => $data->ord,
				'title'     => $data->title,
				'descr'     => $data->descr,
				'color'     => $data->color,
				'askTariff' => $data->asktariff,
				'askMSISDN' => $data->askmsisdn,
				'askEmail'  => $data->askemail,
				'live'      => 1,
			);
			$cool = $db->update("saleTypes",$upd,array('guid' => $data->guid));
			if ($cool == 0) {
				$result['message'] = 'OK';
				$db->delete("confirmed",array('itemguid' => $data->guid));
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		} else {
			$ins = array(
				'guid'      => $data->guid,
				'channel'   => $channelguid,
				'ord'       => $data->ord,
				'title'     => $data->title,
				'descr'     => $data->descr,
				'color'     => $data->color,
				'askTariff' => $data->asktariff,
				'askMSISDN' => $data->askmsisdn,
				'askEmail'  => $data->askemail,
				'live'      => 1,
			);
			$cool = $db->insert("saleTypes",$ins);
			if ($cool) {
				$result['message'] = 'OK';
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		}
	}

	if ($table == 'Sites') {
		$exist = $db->get_var("select guid from sites where guid = '".$data->guid."'");
		if ($exist) { 
			$upd = array(
				'coguid'             => $companyguid,
				'sitename'           => $data->sitename,
				'address1'           => $data->addr1,
				'address2'           => $data->addr2,
				'addr3'              => $data->addr3,
				'tel'                => $data->tel,
				'fax'                => $data->fax,
				'email'              => $data->email,
				'vatnr'              => $data->vatnr,
				'regnr'              => $data->regnr,
				'live'               => $data->live,
				'slipline1'          => $data->slipline1,
				'slipline2'          => $data->slipline2,
				'slipline3'          => $data->slipline3,
				'pastelid'           => $data->pastelid,
				'pastelcompanyname'  => $data->pastelcompanyname,
				'countrycode'        => $data->countrycode,
				'usesnapscan'        => $data->usesnapscan,
				'snapscanmerchantid' => $data->snapscanmerchantid,
			);
			$cool = $db->update("sites",$upd,array('guid' => $data->guid));
			if ($cool == 0) {
				$result['message'] = 'OK';
				$db->delete("confirmed",array('itemguid' => $data->guid));
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		} else {
			$ins = array(
				'guid'               => $data->guid,
				'coguid'             => $companyguid,
				'sitename'           => $data->sitename,
				'address1'           => $data->addr1,
				'address2'           => $data->addr2,
				'addr3'              => $data->addr3,
				'tel'                => $data->tel,
				'fax'                => $data->fax,
				'email'              => $data->email,
				'vatnr'              => $data->vatnr,
				'regnr'              => $data->regnr,
				'live'               => $data->live,
				'slipline1'          => $data->slipline1,
				'slipline2'          => $data->slipline2,
				'slipline3'          => $data->slipline3,
				'pastelid'           => $data->pastelid,
				'pastelcompanyname'  => $data->pastelcompanyname,
				'countrycode'        => $data->countrycode,
				'usesnapscan'        => $data->usesnapscan,
				'snapscanmerchantid' => $data->snapscanmerchantid,
			);
			$cool = $db->insert("sites",$ins);
			if ($cool) {
				$result['message'] = 'OK';
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		}
	}

	if ($table == 'Stocktakecount') {
		$exist = $db->get_var("select guid from stocktake_counted where guid = '".$data->guid."'");
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid'         => $data->guid,
				'datetime'     => $data->datetime,
				'takeguid'     => $data->takeguid,
				'siteguid'     => $data->siteguid,
				'deviceguid'   => $data->deviceguid,
				'devicename'   => $data->devicename,
				'userguid'     => $data->userguid,
				'productguid'  => $data->productguid,
				'productdescr' => $data->productdescr,
				'qty'          => $data->qty,
				'imei'         => $data->imei,
			);
			$cool = $db->insert("stocktake_counted",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Tariffs') {
		$exist = $db->get_var("select guid from tariffs where guid = '".$data->guid."'");
		if ($exist) {
			$upd = array(
				'channelguid' => $channelguid,
				'tariffdescr' => $data->descr,
				'weight'      => $data->weight,
				'category'    => $data->category,
				'subs'        => $data->subs,
				'live'        => $data->live,
			);
			$cool = $db->update("tariffs",$upd,array('guid' => $data->guid));
			if ($cool == 0) {
				$result['message'] = 'OK';
				$db->delete("confirmed",array('itemguid' => $data->guid));
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		} else {
			$ins = array(
				'guid'        => $data->guid,
				'channelguid' => $channelguid,
				'tariffdescr' => $data->descr,
				'weight'      => $data->weight,
				'category'    => $data->category,
				'subs'        => $data->subs,
				'live'        => $data->live,
			);
			$cool = $db->insert("tariffs",$ins);
			if ($cool) {
				$result['message'] = 'OK';
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		}
	}

	if ($table == 'TempBaskets') {
		$exist = $db->get_var("select guid from tempBaskets where guid = '".$data->guid."'");
		if ($exist) { 
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid'         => $data->guid,
				'siteguid'     => $data->siteguid,
				'saleguid'     => $data->saleguid,
				'ord'          => $data->ord,
				'saletypeguid' => $data->saletypeguid,
				'tariffguid'   => $data->tariffguid,
				'msisdn'       => $data->msisdn,
				'email'        => $data->email,
				'live'         => $data->live,
			);
			$cool = $db->insert("tempBaskets",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'TempSH') {
		$exist = $db->get_var("select guid from tempSH where guid = '".$data->guid."'");
		if ($exist) { 
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid'        => $data->guid,
				'sdate'       => $data->sdate,
				'siteguid'    => $data->siteguid,
				'cashierguid' => $data->cashierguid,
				'completed'   => $data->completed,
				'live'        => $data->live,
				'descr'       => $data->descr,
			);
			$cool = $db->insert("tempSH",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'TempSL') {
		$exist = $db->get_var("select guid from tempSL where guid = '".$data->guid."'");
		if ($exist) { 
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'guid'         => $data->guid,
				'line'         => $data->line,
				'siteguid'     => $data->siteguid,
				'saleguid'     => $data->saleguid,
				'basketguid'   => $data->basketguid,
				'basketline'   => $data->basketline,
				'productguid'  => $data->productguid,
				'productdescr' => $data->productdescr,
				'qty'          => $data->qty,
				'serial'       => $data->serial,
				'unitcost'     => $data->unitcost,
				'unitsell'     => $data->unitsell,
				'unitdisc'     => $data->unitdisc,
				'unitvat'      => $data->unitvat,
				'unitrebate'   => $data->unitrebate,
				'live'         => $data->live,
			);
			$cool = $db->insert("tempSL",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Tenders') {
		$result['guid'] = $data->tenderguid;
		$exist = $db->get_var("select tenderguid from tenders where tenderguid = '".$data->tenderguid."'");
		if ($exist) {
			$result['message'] = 'OK';
		} else {
			$ins = array(
				'tenderguid'    => $data->tenderguid,
				'tenderdate'    => $data->tenderdate,
				'saleguid'      => $data->guid,
				'siteguid'      => $siteguid,
				'cashierguid'   => $data->cashierguid,
				'category'      => $data->category,
				'title'         => $data->title,
				'amount'        => $data->amount,
				'communityguid' => $data->communityguid,
			);
			$cool = $db->insert("tenders",$ins);
			if ($cool) {
				$result['message'] = 'OK';
			}
		}
	}

	if ($table == 'Users') {
		$exist = $db->get_var("select uguid from users where uguid = '".$data->guid."'");
		if ($exist) { 
			$upd = array(
				'cguid'      => $companyguid,
				'fname'      => $data->fname,
				'sname'      => $data->sname,
				'email'      => $data->email,
				'pword'      => $data->pword,
				'cashierpin' => $data->cashierpin,
				'lastsite'   => $siteguid,
				'community'  => $data->allowcommunity,
				'products'   => $data->allowproducts,
				'reports'    => $data->allowreports,
				'live'       => $data->live,
				'basket'     => $data->allowbasket,
				'move'       => $data->allowmove,
				'users'      => $data->allowusers,
				'general'    => $data->allowgeneral,
			);
			$cool = $db->update("users",$upd,array('uguid' => $data->guid));
			if ($cool == 0) {
				$result['message'] = 'OK';
				$db->delete("confirmed",array('itemguid' => $data->guid));
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		} else {
			$ins = array(
				'uguid'      => $data->guid,
				'cguid'      => $companyguid,
				'fname'      => $data->fname,
				'sname'      => $data->sname,
				'email'      => $data->email,
				'pword'      => $data->pword,
				'cashierpin' => $data->cashierpin,
				'lastsite'   => $siteguid,
				'community'  => $data->allowcommunity,
				'products'   => $data->allowproducts,
				'reports'    => $data->allowreports,
				'live'       => $data->live,
				'basket'     => $data->allowbasket,
				'move'       => $data->allowmove,
				'users'      => $data->allowusers,
				'general'    => $data->allowgeneral,
			);
			$cool = $db->insert("users",$ins);
			if ($cool) {
				$result['message'] = 'OK';
				$db->insert("confirmed",array('itemguid' => $data->guid, 'deviceguid' => $deviceguid));
			}
		}
	}
	
	return $result;
?>