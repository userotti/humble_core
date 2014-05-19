<?php

	$result = array();
	$result['companyguid'] = $user->cguid;
	$result['dealcode'] = strtoupper($params['dealcode']);
	
	$row = $db->get_row("select * from dealheaders where companyguid = '".$result['companyguid']."' and dealcode = '".$result['dealcode']."'");
	if (!empty($row)) {
		$result['guid'] = $row->guid;
		$result['companyguid'] = $row->companyguid;
		$result['sdate'] = $row->sdate;
		$result['edate'] = $row->edate;
		$result['dealdescr'] = $row->dealdescr;
		$result['tariffguid'] = $row->tariffguid;
		$result['payin'] = $row->payin;
		$lines = array();
		$rows = $db->get_results("select * from deallines where dealguid = '".$result['guid']."' order by ord asc");
		foreach ($rows as $row) {
			$lines[] = array(
				'guid'        => $row->guid,
				'ord'         => $row->ord,
				'productguid' => $row->productguid,
				'qty'         => $row->qty,
				'locksell'    => $row->locksell,
			);
		}
		$result['lines'] = $lines;
		$result['message'] = "OK";
	} else {
		$result['message'] = "Deal Code Not Found";
	}
	return $result;
?>