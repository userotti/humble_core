<?php
/*

Caldoza Engine ------------------------

File	:	api/company/update-site.php
Created	: 	2013-12-17

*/



$companyfields = array(
	'company',
	'tradingas',
	'vatnr',
    'taxprompt'
);

if(!empty($params['companyguid'])){


    $company = $db->get_row($db->prepare("

    SELECT
        *
    FROM
        `companies`
    WHERE
        `guid` = %s
        ", $params['companyguid']), ARRAY_A);

    if(!empty($company)){
    	foreach($companyfields as $field){
    		if(isset($_POST[$field])){
    			$company[$field] = $_POST[$field];
    		}
    	}

    	// update 
    	$db->update('companies', $company, array('guid'=>$params['companyguid']));
    	$company['message'] = 'OK';
    	return $company;
    }
}