<?php
	$result = array(
		'siteguid' => $user->siteguid,
		'data'     => array(),
	);

	$trfs = $db->get_results("select * from mh where siteguid = '".$result['siteguid']."' and movetype = 3 and movestate = 0 order by datetime asc");
	foreach ($trfs as $trf) {
		$header = array(
			'guid' => $trf->guid,
			'companyguid' => $trf->companyguid,
			'siteguid' => $trf->siteguid,
			'datetime' => $trf->datetime,
			'movetype' => $trf->movetype,
			'movestate' => $trf->movestate,
			'accguid' => $trf->accguid,
			'trfguid' => $trf->trfguid,
			'acc' => $trf->acc,
			'refnr' => $trf->refnr,
			'deviceguid' => $trf->deviceGUID,
			'devicename' => $trf->deviceName,
			'userguid' => $trf->userguid,
			'excl' => $trf->excl,
			'vat' => $trf->vat,
			'incl' => $trf->incl,
			'direction' => $trf->direction,
			'pastelid' => $trf->pastelid,
		);

		$lines = array();
		$rows = $db->get_results("select * from ml where guid = '".$trf->guid."'");
		foreach ($rows as $row) {
			$lines[] = array(
				'guid'         => $row->guid,
				'line'         => $row->line,
				'productguid'  => $row->productguid,
				'descr'        => $row->descr,
				'qty'          => $row->qty,
				'unitcost'     => $row->unitcost,
				'linecost'     => $row->linecost,
				'linevat'      => $row->linevat,
				'lineincl'     => $row->lineincl,
				'serial'       => $row->serial,
				'movementguid' => $row->movementguid,
			);
		}

		$transfer = array(
			'header' => $header,
			'lines'  => $lines,
		);
		$result['data'][] = $transfer;
	}
	



	$result['message'] = 'OK';
	return $result;
?>