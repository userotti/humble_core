<?php
	$mayProcess = true;
	$return = array(
		'message'=>'Not Defined Yet', 
	);

	$userguid = $user->uguid;
	$siteguid = $user->siteguid;
	$take = $db->get_row("SELECT * FROM stocktake_log WHERE siteguid = '".$siteguid."' order by startdate desc");

	
	if(empty($take)) {
		$return['message'] = 'Stock Take not found.';
		$mayProcess = false;		
	}
	if (!is_null($take->enddate) && $mayProcess == true) {
		$mayProcess = false;
		$return['message'] = "Stocktake has already been processed.";
		$return['guid'] = $take->guid;
	}
	if ($take->userguid != $userguid && $mayProcess == true) {
		$unames = $db->get_row("select fname,sname from users where uguid = '".$take->userguid."'");
		$username = "$unames->fname $unames->sname";
		$return['message'] = "You may not process this Stock Take. Only $username can process it.";
		$return['guid'] = $take->guid;
		$mayProcess = false;
	}
	
	if ($mayProcess == true) {
		$return['message'] = "OK";
		$return['guid'] = $take->guid;
		process_stocktake($take->guid);
	}
	$return['mayProcess'] = $mayProcess;

	return $return;
	