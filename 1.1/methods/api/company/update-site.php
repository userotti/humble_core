<?php
/*

Caldoza Engine ------------------------

File	:	api/company/update-site.php
Created	: 	2013-12-17

*/



$sitefields = array(
	'sitename',
	'address1',
	'address2',
	'addr3',
	'fax',
	'email',
	'tel',
	'targ0',
	'targ1',
	'targ2',
	'targ3',
	'targ4',
	'targ5',
	'vatnr',
	'live',
	'regnr',
	'slipline1',
	'slipline2',
	'slipline3',
	'pastelid',
	'usesnapscan',
	'snapscanmerchantid'
);

if(!empty($params['siteguid'])){


    $site = $db->get_row($db->prepare("

    SELECT
        *
    FROM
        `sites`
    WHERE
        `coguid` = %s
    AND `guid` = %s
        ", $user->cguid,  $params['siteguid']), ARRAY_A);

    if(!empty($site)){
    	foreach($sitefields as $field){
    		if(isset($_POST[$field])){
    			$site[$field] = $_POST[$field];
    		}
    	}

    	// update 
    	$db->update('sites', $site, array('guid'=>$params['siteguid'], 'coguid' => $user->cguid));
    	$db->delete('confirmed', array('itemGUID'=>$params['siteguid']));
    	$site['site_name'] = $site['sitename'];
    	$site['message'] = 'OK';
    	return $site;
    }
}
if(!empty($_POST)){
// fall through to insert
	// new sites
	$newsites = array(
		'guid'		=>	$params['siteguid'],
		'coguid'		=>	$user->cguid,
	);

	if($params['siteguid'] == 'new'){
		$newsites['guid'] = gen_uuid();
	}

	foreach($sitefields as $field){
		if(isset($_POST[$field])){
			$newsites[$field] = $_POST[$field];
		}
	}

	$db->insert('sites', $newsites);
	$newsites['message'] = 'OK';
	$newsites['site_name'] = $newsites['sitename'];

	return $newsites;
}
