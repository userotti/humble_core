<?php

	function search_array($arr, $search) {
		$result = array();
		foreach ($arr as $rec) {
			$add = false;
			foreach ($search as $key => $val) {
				if (isset($rec->$key)) {
					if ($rec->$key == $val) {
						if ($add == false) {
							$result[] = $rec;
							$add = true;
						}
					}
				}
			}
		}
		return $result;
	}

	function get_onhand($siteGUID,$productGUID) {
		global $db;

		$si = $db->get_var("select si from products where guid = '".$productGUID."'");
		if ($si) {
			$qty = $db->get_var("select sum(movedir) qty from imeihistory where siteguid = '".$siteGUID."' and productguid = '".$productGUID."'");
		} else {
			$qty = $db->get_var("select sum(qty*movedir) qty from movement where siteguid = '".$siteGUID."' and productguid = '".$productGUID."'");
		}
		if (is_null($qty)) {
			$qty = 0;
		}
		return $qty;
	}

	function recon_imei() {
		global $db;
		$imeis = $db->get_results("select * from moveimeis where imeihistoryguid = ''");
		foreach ($imeis as $rec) {
			$moveGUID = $rec->guid;
			$mh = $db->get_row("select movetype,siteguid,datetime from mh where guid = '".$moveGUID."' and movestate = 1");
			if (!empty($mh)) {
				$guid = gen_uuid();
				$moveType = $mh->movetype;
				//$moveDir = $db->get_var("select movedir from movetypes where movecode = ".$moveType);
				$ml = $db->get_row("select line from ml where guid = '".$moveGUID."' and productguid = '".$rec->productguid."'");
				$arr = array(
					'guid'        => $guid,
					'productguid' => $rec->productguid,
					'imei'        => $rec->imei,
					'imeicost'    => $rec->imeicost,
					'movetype'    => $moveType,
					'movedir'     => $rec->movedir,
					'moveguid'    => $moveGUID,
					'siteguid'    => $mh->siteguid,
					'datetime'    => $mh->datetime,
					'moveline'    => $ml->line,
				);
				if ($db->insert('imeihistory',$arr)) {
					$db->update("moveimeis",array('imeihistoryguid' => $guid),array('guid' => $moveGUID,'productguid' => $rec->productguid,'imei' => $rec->imei,'movedir' => $rec->movedir,'imeicost' => $rec->imeicost));
				}
			} else {
				$db->update("moveimeis",array('imeihistoryguid' => 'N/A'),array('guid' => $moveGUID,'productguid' => $rec->productguid,'imei' => $rec->imei,'movedir' => $rec->movedir,'imeicost' => $rec->imeicost));
			}
		}

		$imeis = $db->get_results("select * from sl where serial not in ('', 'N/A') and imeihistoryguid = ''");
		foreach ($imeis as $rec) {
			$moveGUID = $rec->guid;
			$sh = $db->get_row("select siteguid,datetime from sh where guid = '".$moveGUID."'");
			if (!empty($sh)) {
				$guid = gen_uuid();
				if ($rec->qty < 0) {
					$movetype = 7;
					$movedir = 1;
				} else {
					$movetype = 8;
					$movedir = -1;
				}
				$arr = array(
					'guid'        => $guid,
					'productguid' => $rec->productguid,
					'imei'        => $rec->serial,
					'imeicost'    => $rec->cost,
					'movetype'    => $movetype,
					'movedir'     => $movedir,
					'moveguid'    => $moveGUID,
					'siteguid'    => $sh->siteguid,
					'datetime'    => $sh->datetime,
					'moveline'    => $rec->line,
				);
				if ($db->insert('imeihistory',$arr)) {
					$db->update("sl",array('imeihistoryguid' => $guid),array('guid' => $moveGUID,'line' => $rec->line));
				}
			}
		}

		$imeis = $db->get_results("select ih.* from imeihistory ih left join imeicost ic on ih.productguid = ic.productguid and ih.imei = ic.imei where ic.imei is null");
		foreach ($imeis as $rec) {
			$companyguid = $db->get_var("select coguid from sites where guid = '".$rec->siteguid."'");
			$arr = array(
				'guid'        => gen_uuid(),
				'companyguid' => $companyguid,
				'productguid' => $rec->productguid,
				'imei'        => $rec->imei,
				'cost'        => $rec->imeicost,
			);
			$db->insert('imeicost',$arr);
		}
	}

	function recon_movement() {
		global $db;

		

		$db->delete("movement",array('movetable' => '',));

		$arr = $db->get_results("select * from ml where movementguid = ''");
		foreach ($arr as $line) {
			$moveguid = $line->guid;
			$moveline = $line->line;
			$guid = gen_uuid();
			$mh = $db->get_row("select * from mh where guid = '".$moveguid."'");
			if (empty($mh)) {
				$siteguid = 'N/A';
				$datetime = date('Y-m-d H:i:s');
				$movetype = 0;
				$movedir = 0;
				$movestate = 0;
			} else {
				$siteguid = $mh->siteguid;
				$datetime = $mh->datetime;
				$movetype = $mh->movetype;
				$movedir = $db->get_var("select movedir from movetypes where movecode = ".$movetype);
				$movestate = $mh->movestate;
			}

			$movement = array(
				'guid'        => $guid,
				'siteguid'    => $siteguid,
				'datetime'    => $datetime,
				'productguid' => $line->productguid,
				'qty'         => $line->qty,
				'movedir'     => $movedir,
				'movetype'    => $movetype,
				'moveguid'    => $moveguid,
				'moveline'    => $moveline,
				'movecost'    => $line->linecost,
				'movetable'   => 'ml',
			);
			if ($movestate == 1) {
				if ($db->insert('movement',$movement)) {
					$db->update('ml',array('movementguid' => $guid),array('guid' => $moveguid,'line' => $moveline));
				}
			}
		}

		$arr = $db->get_results("select * from sl where movementguid = ''");
		foreach ($arr as $line) {
			$moveguid = $line->guid;
			$moveline = $line->line;
			$guid = gen_uuid();
			$sh = $db->get_row("select * from sh where guid = '".$moveguid."'");
			if (empty($sh)) {
				$siteguid = 'N/A';
				$datetime = date('Y-m-d H:i:s');
				$movetype = 0;
				$movedir = 0;
			} else {
				$siteguid = $sh->siteguid;
				$datetime = $sh->datetime;
				if ($line->qty >= 0) {
					$movetype = 8;
					$movedir = -1;
				} else {
					$movetype = 7;
					$movedir = 1;
				}
			}

			$movement = array(
				'guid'        => $guid,
				'siteguid'    => $siteguid,
				'datetime'    => $datetime,
				'productguid' => $line->productguid,
				'qty'         => $line->qty,
				'movedir'     => $movedir,
				'movetype'    => $movetype,
				'moveguid'    => $moveguid,
				'moveline'    => $moveline,
				'movecost'    => $line->cost,
				'movetable'   => 'sl',
			);
			if ($db->insert('movement',$movement)) {
				$db->update('sl',array('movementguid' => $guid),array('guid' => $moveguid,'line' => $moveline));
			}
		}
		
	}

	function process_stocktake($takeguid) {
		global $db;
		global $user;

		recon_movement();


		$adj = array();
		$imeihist = array();
		$session = $db->get_row("select * from stocktake_log where guid = '".$takeguid."'");
		$siteguid = $session->siteguid;
		$userguid = $session->userguid;
		$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");
		$products = $db->get_results("select guid,descr,si,producttype from products where companyguid = '".$companyguid."'");
		$excl = 0;

		foreach ($products as $product) {
			$productguid = $product->guid;
			$producttype = $product->producttype;
			$si = $product->si;
			if ($producttype != 6) {
				if ($si == false) {
					$count = $db->get_var("select sum(qty) qty from stocktake_counted where takeguid = '".$takeguid."' and productguid = '".$product->guid."'");
					$onhand = get_onhand($siteguid,$productguid);
					if ($count != $onhand) {
						$qty = $count-$onhand;
						$cost = $db->get_var("select cost from products where guid = '".$productguid."'");
						$adj[] = array(
							'productguid' => $productguid,
							'qty'         => $qty,
							'cost'        => $cost,
							'imei'        => 'N/A',
						);
						$excl = $excl+($qty*$cost);
					}
				} else {
					$imeis = $db->get_results("select distinct imei from stocktake_counted where takeguid = '".$takeguid."' and productguid = '".$productguid."' and imei != 'N/A'");
					$imeisonh = $db->get_results("select imei from imeihistory where siteguid = '".$siteguid."' and productguid = '".$productguid."' group by imei having sum(movedir) != 0");
					$imeis = array_merge($imeis,$imeisonh);
					
					foreach ($imeis as $line) {
						$imei = $line->imei;
						$cost = $db->get_var("select cost from imeicost where companyguid = '".$companyguid."' and productguid = '".$productguid."' and imei = '".$imei."'");
						if (empty($cost)) {
							$cost = $db->get_var("select cost from products where companyguid = '".$companyguid."' and guid = '".$productguid."'");
						}
						if (empty($cost)) {
							$cost = 0;
						}
						$count = $db->get_var("select sum(qty) qty from stocktake_counted where takeguid = '".$takeguid."' and productguid = '".$productguid."' and imei = '".$imei."'");
						if (empty($count)) { $count = 0; }
						$onh = $db->get_var("select sum(movedir) onh from imeihistory where siteguid = '".$siteguid."' and productguid = '".$productguid."' and imei = '".$imei."'");
						if (empty($onh)) { $onh = 0; }
						$qty = $count-$onh;
						if ($qty != 0) {
							$addLine = true;
							foreach ($adj as $rec) {
								if ($productguid == $rec['productguid']) {
									$addLine = false;
								}
							}
							if ($addLine == true) {
								$adj[] = array(
									'productguid' => $productguid,
									'qty'         => $qty,
									'cost'        => $cost,
									'imei'        => 'N/A',
								);		
							} else {
								foreach ($adj as $rec) {
									if ($productguid == $rec['productguid']) {
										$linecost = abs($rec['qty']*$rec['cost']);
										$rec['qty'] = $rec['qty']+$qty;
										$rec['cost'] = ($linecost+$cost)/abs($rec['qty']);
										$adj[key($adj)] = $rec;
									}
								}
							}

							$imeihist[] = array(
								'productguid' => $productguid,
								'imei'        => $imei,
								'movedir'     => $qty,
								'imeicost'    => $cost,
							);
						}

					}
				}
			}
		}

		$mh = array(
			'guid'        => $takeguid,
			'companyguid' => $companyguid,
			'siteguid'    => $siteguid,
			'datetime'    => date('Y-m-d H:i:s'),
			'movetype'    => 6,
			'movestate'   => 1,
			'accguid'     => '',
			'acc'         => '',
			'refnr'       => 'Stocktake',
			'deviceguid'  => $user->deviceGUID,
			'devicename'  => $user->deviceName,
			'userguid'    => $user->uguid,
			'excl'        => $excl,
			'vat'         => $excl*0.14,
			'incl'        => $excl*1.14,
			'direction'   => 'TAKE',
			'pastelid'    => 0,
		);
		$db->delete('mh',array('guid' => $takeguid));
		$result = $db->insert('mh',$mh);

		if ($result) {
			$nr = 0;
			$db->delete('ml',array('guid' => $takeguid));
			foreach ($adj as $line) {
				$ml = array(
					'guid'        => $takeguid,
					'line'        => $nr,
					'productguid' => $line['productguid'],
					'descr'       => '',
					'qty'         => $line['qty'],	
					'unitcost'    => $line['cost'],
					'linecost'    => abs($line['qty']*$line['cost']),
					'linevat'     => abs($line['qty']*$line['cost']*0.14),
					'lineincl'    => abs($line['qty']*$line['cost']*1.14),
					'serial'      => 'N/A',
				);
				$db->insert('ml',$ml);
				$nr++;
			}
		}

		if ($result) {
			$db->delete('moveimeis',array('guid' => $takeguid));
			foreach ($imeihist as $line) {
				$mi = array(
					'guid'        => $takeguid,
					'productguid' => $line['productguid'],
					'imei'        => $line['imei'],
					'movedir'     => $line['movedir'],
					'imeicost'    => $line['imeicost'],
				);
				$db->insert('moveimeis',$mi);
			}
		}

		$db->update("stocktake_log",array('enddate' => date('Y-m-d H:i:s')),array('guid' => $takeguid));
		recon_movement();
		//checkProducts($siteguid);
	}



	function mail_logs() {
		global $db;
		$from = 'server@humble.co.za';
		$arr = $db->get_results("select * from log where mailed = 0 order by insdate asc");
		foreach ($arr as $rec) {
			$msg = $rec->message;
			//console("Log: $msg");
			$subject = "humble log - $rec->message";
			$line = sprintf("GUID [$rec->guid]\nInsert Date [$rec->insdate]\nSystem [$rec->system]\nMessage [$rec->message]\n");
			if (mail("will@humble.co.za",$subject,$line,"From: $from\n")) {
				$db->update('log',array('mailed' => 1),array('guid' => $rec->guid));
			}
		}
	}



	function synch_humble_customers() {
		global $db;

		$humble_company_guid = 'A2CD6180-9A7A-4A83-8EB5773DB278844D';
		$humble_site_guid = 'FEBE2730-081E-417B-A5F2C84B8728DBBA';
		$arr = $db->get_results("select * from companies where guid != '".$humble_company_guid."'");
		foreach ($arr as $rec) {
			$companyguid = $rec->guid;
			$companyName = strtoupper($rec->company);
			$masteruser = $rec->masteruser;
			$packageguid = $rec->packageguid;
			$tillcustomer = $db->get_var("select tillcustomer from packages where guid = '".$packageguid."'");
			$customer = $db->get_results("select * from community where companyguid = '".$humble_company_guid."' and upper(descr) = '".$companyName."' and communitytype = 0");
			if (empty($customer)) {
				$cust = array(
					'guid'          => gen_uuid(),
					'companyguid'   => $humble_company_guid,
					'descr'         => $rec->company,
					'communitytype' => 0,
					'tillcustomer'  => $tillcustomer,
				);
				$usr = $db->get_row("select * from users where uguid = '".$masteruser."'");
				$cust['email'] = strtoupper($usr->email);
				$cust['contactname'] = sprintf("$usr->fname $usr->sname");
				$cust['contactnumber'] = $usr->cellnr;
				$db->insert("community",$cust);
			} else {
				$tcust = $customer[0]->tillcustomer;
				if ($tillcustomer != $tcust) {
					console("$customer vs $tcust");
				}


			}
			
		}
	}

	function internal_stats() {
		global $db;
		$results = array();

		$today = date('Y-m-d 00:00:00');
		$today = $today;
		$results['today'] = $today;

		$sales = array();
		$arr = $db->get_results("select siteguid, count(*) qty, max(datetime) maxdate, min(datetime) mindate, sum(excl) revenue from sh where datetime >= '".$today."' group by siteguid");
		foreach ($arr as $rec) {
			$sitename = $db->get_var("select sitename from sites where guid = '".$rec->siteguid."'");
			$companyguid = $db->get_var("select coguid from sites where guid = '".$rec->siteguid."'");
			$companyname = $db->get_var("select company from companies where guid = '".$companyguid."'");
			$lastcashup = $db->get_var("select max(datetime) from cashups where siteguid = '".$rec->siteguid."'");
			$sales[] = array(
				'company'    => $companyname,
				'site'       => $sitename,
				'sales'      => $rec->qty,
				'firstsale'  => $rec->mindate,
				'lastsale'   => $rec->maxdate,
				'revenue'    => $rec->revenue,
				'lastcashup' => $lastcashup,
			);
		}
		$results['sales'] = $sales;











		return $results;
	}

	function cloudSync($siteguid,$deviceguid,$system,$message) {
		global $db;
		$log = array(
			'guid'       => gen_uuid(),
			'siteguid'   => $siteguid,
			'deviceguid' => $deviceguid,
			'synctype'   => $system,
			'message'    => $message,
		);
		$db->insert("cloudsync",$log);
	}

	function fixCashierPINs() {
		global $db;

		$rows = $db->get_results("select distinct cguid from users where cashierpin = '' ");
		foreach ($rows as $row) {
			$companyguid = $row->cguid;
			$usrs = $db->get_results("select uguid guid from users where cguid = '".$companyguid."' and cashierpin = '' ");
			foreach ($usrs as $usr) {
				$userguid = $usr->guid;
				$pin = rand(1000,9999);
				$db->update("users",array("cashierpin" => $pin),array("uguid" => $userguid));
			}
		}

		$rows = $db->get_results("select uguid,fname,pword,cashierpin from users where email = '' ");
		foreach ($rows as $row) {
			$email = sprintf("$row->fname@humble.co.za");
			$db->update("users",array("email" => $email),array("uguid" => $row->uguid));
		}

		$rows = $db->get_results("select uguid,fname,pword,cashierpin from users where pword = '' ");
		foreach ($rows as $row) {
			$db->update("users",array("pword" => $row->cashierpin),array("uguid" => $row->uguid));
		}
	}

	function checkTempBase() {
		global $db;

		$rows = $db->get_results("select * from temp_base where original = '' or original is null");
		foreach ($rows as $row) {
			$db->delete("temp_base",array('guid' => $row->guid));
		}

		$rows = $db->get_results("select * from temp_base where msisdn = '' ");
		foreach ($rows as $row) {
			$msisdn = $row->original;
			$len = strlen($msisdn);
			$first = substr($msisdn,0,1);
			if ($len == 10 and $first == '0') {
				$db->update("temp_base",array('msisdn' => $msisdn),array('guid' => $row->guid));
			} else {
				$convert = false;
				if ($first == '6') { $convert = true; }
				if ($first == '7') { $convert = true; }
				if ($first == '8') { $convert = true; }
				if ($len == 12 && $convert == true) {
					$sub = substr($msisdn,0,9);
					$msisdn = sprintf("0$sub");
					$db->update("temp_base",array('msisdn' => $msisdn),array('guid' => $row->guid));
				}
			}
		}

		$rows = $db->get_results("select msisdn, count(*) from temp_base group by msisdn having count(*) > 1");
		foreach ($rows as $row) {
			$recs = $db->get_results("select * from temp_base where msisdn = '".$row->msisdn."'");
			$del = false;
			foreach ($recs as $rec) {
				if ($del == true) {
					$db->delete("temp_base",array('guid' => $rec->guid));
				}
				$del = true;
			}
		}

		$rows = $db->get_results("select msisdn from temp_base");
		foreach ($rows as $row) {
			$msisdn = $row->msisdn;
			printf("insert into api_customers (msisdn,node,customer_classification) values ('$msisdn',89,1);\n");
		}
	}

	function checkAutomatedTransfers() {
		global $db;

		$rows = $db->get_results("select * from mh where movetype = 4 and trfguid = '' order by datetime desc");
		foreach ($rows as $row) {
			$oldguid = $row->guid;
			$newguid = gen_uuid();
			$header = array(
				'guid'        => $newguid,
				'companyguid' => $row->companyguid,
				'siteguid'    => $row->accguid,
				'datetime'    => $row->datetime,
				'movetype'    => 3,
				'movestate'   => 0,
				'accguid'     => $row->siteguid,
				'trfguid'     => $row->guid,
				'acc'         => $db->get_var("select sitename from sites where guid = '".$row->siteguid."'"),
				'refnr'       => $row->refnr,
				'deviceguid'  => $row->deviceGUID,
				'devicename'  => $row->deviceName,
				'userguid'    => $row->userguid,
				'excl'        => $row->excl,
				'vat'         => $row->vat,
				'incl'        => $row->incl,
				'direction'   => 'IN',
				'pastelid'    => 0,
			);
			$db->insert("mh",$header);
			$lines = $db->get_results("select * from ml where guid = '".$row->guid."' order by line");
			foreach ($lines as $row) {
				$ins = array(
					'guid' => $newguid,
					'line' => $row->line,
					'productguid' => $row->productguid,
					'descr' => $row->descr,
					'qty' => $row->qty,
					'unitcost' => $row->unitcost,
					'linecost' => $row->linecost,
					'linevat' => $row->linevat,
					'lineincl' => $row->lineincl,
					'serial' => $row->serial,
					'movementguid' => '',
				);
				$db->insert("ml",$ins);
			}
			$db->update("mh",array('trfguid' => $newguid),array('guid' => $oldguid));
			break;
		}

	}

	function fixDuplicateCustomer() {
		global $db;

		$rows = $db->get_results("select companyguid, descr, count(*) qty from community where companyguid = 'A2CD6180-9A7A-4A83-8EB5773DB278844D' and communitytype = 0 group by companyguid, descr having count(*) > 1");
		foreach ($rows as $row) {
			$companyguid = $row->companyguid;
			$descr = $row->descr;
			$i = 0;
			$fixes = $db->get_results("select guid,descr from community where companyguid = '".$companyguid."' and descr = '".$descr."' and communitytype = 0 and guid not in (select itemguid from pasteltranslate)");
			foreach ($fixes as $fix) {
				$newdescr = sprintf("$descr $i");
				console($newdescr);
				$i++;
				$db->update("community",array('descr' => $newdescr),array('guid' => $fix->guid, 'descr' => $descr));
			}
		}
	}

	function checkSnapScanPayments() {
		global $db;
		$rows = $db->get_results("select guid,auth_code from snapscanpayments where saleguid = '' or saleguid = 'N/A' ");
		foreach ($rows as $row) {
			$guid = $row->guid;
			$title = sprintf("SnapScan $row->auth_code");
			$saleguid = $db->get_var("select saleguid from tenders where title = '".$title."'");
			if (!empty($saleguid)) {
				$db->update("snapscanpayments",array('saleguid' => $saleguid),array('guid' => $guid));
			} else {
				$db->update("snapscanpayments",array('saleguid' => 'N/A'),array('guid' => $guid));
			}
		}
		$rows = $db->get_results("select tenderguid,saleguid from tenders where siteguid = ''");
		foreach ($rows as $row) {
			$tenderguid = $row->tenderguid;
			$saleguid = $row->saleguid;
			$siteguid = $db->get_var("select siteguid from sh where guid = '".$saleguid."'");
			if (!empty($siteguid)) {
				$db->update("tenders",array('siteguid' => $siteguid),array('tenderguid' => $tenderguid));			
			}
		}
	}

	function checkTempSales() {
		global $db;

		$old_date = date("Y-m-d H:i:s",strtotime("-7 day"));
		$rows = $db->get_results("select guid,sdate from tempSH where completed = 0");
		foreach ($rows as $row) {
			$guid = $row->guid;
			$sdate = $row->sdate;

			$exist = $db->get_var("select guid from sh where guid = '".$guid."'");
			if ($exist) {
				$db->update("tempSH",array('completed' => 1),array('guid' => $guid));
				$db->update("tempSL",array('completed' => 1),array('saleguid' => $guid));
				$db->update("tempBaskets",array('completed' => 1),array('saleguid' => $guid));
				$db->update("basketnotes",array('completed' => 1),array('saleguid' => $guid));
			} else {
				if (date($sdate) < $old_date) {
					$db->update("tempSH",array('completed' => 2),array('guid' => $guid));
					$db->update("tempSL",array('completed' => 2),array('saleguid' => $guid));
					$db->update("tempBaskets",array('completed' => 2),array('saleguid' => $guid));
					$db->update("basketnotes",array('completed' => 2),array('saleguid' => $guid));
				}
			}
		}
	}

	function checkDeskDotCom() {
		global $db;
		
		$sites = $db->get_results("select distinct siteguid from login where result = 'OK' and siteguid != '' and siteguid in (select guid from sites where coguid in (select guid from companies where live = 0))");
		foreach ($sites as $site) {
			$siteguid = $site->siteguid;
			$companyguid = $db->get_var("select coguid from sites where guid = '".$siteguid."'");
			$db->update("companies",array('live' => 1),array('guid' => $companyguid));
		}

		$params = array();
		$request = do_desk_call("companies",$params,array(),array());
		$companies = $request['_embedded']['entries'];

		foreach ($companies as $company) {
			$id = $company['id'];
			$name = $company['name'];
			$companyguid = $db->get_var("select guid from companies where company = '".$name."' and deskid = 0 and live = 1");
			if (!empty($companyguid)) {
				$db->update("companies",array("deskid" => $id),array("guid" => $companyguid));
			}
		}

		$newcompanies = $db->get_results("select * from companies where live = 1 and deskid = 0 order by insdate desc");
		foreach ($newcompanies as $company) {
			$custom = array(
				'companyguid' => $company->guid,
			);
			$ins = array(
				'name'          => $company->company,
				'domains' => array(),
				'created_at' => $company->insdate,
				'updated_at' => $company->insdate,
				'custom_fields' => $custom,
			);
			$result = do_desk_call('companies',array('Accept' => 'application/json'),$ins,array());
			if (!empty($result['id'])) {
				$db->update("companies",array('deskid' => $result['id']),array('guid' => $company->guid));
			}
		}

		$newcustomers = $db->get_results("select * from users where cguid in (select guid from companies where deskid != 0 and live = 1) and deskid = 0 and live = 1");
		foreach ($newcustomers as $customer) {
			$ins = array(
				'first_name' => $customer->fname,
				'last_name' => $customer->sname,
				'company' => $db->get_var("select company from companies where guid = '".$customer->cguid."'"),
				'external_id' => $customer->uguid,
			);
			if (!empty($customer->cellnr)) {
				$ins['phone_numbers'] = array(
					'type' => 'mobile',
					'value' => $customer->cellnr,
				);
			}
			if (!empty($customer->email)) {
				$ins['emails'] = array(
					'type'  => 'work',
					'value' => $customer->email,
				);
			}
			$result = do_desk_call('customers',array('Accept' => 'application/json'),$ins,array());
			if (!empty($result['id'])) {
				$db->update("users",array('deskid' => $result['id']),array('uguid' => $customer->uguid));
			}
		}

		$welcomecases = $db->get_results("select guid,company,masteruser from companies where deskid != 0 and welcomecase = 0 order by insdate desc");
		foreach ($welcomecases as $company) {
			$deskid = $db->get_var("select deskid from users where uguid = '".$company->masteruser."'");
			$mobile = $db->get_var("select cellnr from users where uguid = '".$company->masteruser."'");
			$call = array(
				'type'        => 'phone',
				'subject'     => 'Welcome Call',
				'priority'    => 10,
				'status'      => 'open',
				'description' => sprintf("Please call the user and welcome them to humble.\r\n\r\nAsk if there is anything you can help them with.\r\nMobile: $mobile"),
				'labels'      => array('New Customer',),
				'message'  => array(
					'direction' => 'out',
					'body'      => 'Please Call New Customer and Welcome Them.',
				),
				'_links'   => array(
					'customer' => array(
						'class' => 'customer',
						'href'  => sprintf("/api/vs/customers/$deskid"),
					),
					'assigned_user' => array(
						'class' => 'user',
						'href'  => '/api/v2/users/21781678',
					),
					'assigned_group' => array(
						'class' => 'group',
						'href'  => '/api/v2/groups/416477',
					),
				),
			);
			$result = do_desk_call('cases',array('Accept' => 'application/json'),$call,array());
			if (!empty($result['id'])) {
				$db->update("companies",array('welcomecase' => $result['id']),array('guid' => $company->guid));
			}
		}	
	}

?>