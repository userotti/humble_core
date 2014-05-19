<?php
/*

Caldoza Engine ------------------------

File    :   api/products/update-ean.php
Created :   2013-12-09

*/
/*
ob_start();
dump($_POST,0);
$debug = ob_get_clean();
$db->insert('debugnotes', array('message'=>$debug));

*/



if(!empty($params['eanguid'])){

    // a filter to check if a submitted ean exists
    $filter = $db->prepare(" AND `guid` = %s ", $params['eanguid']);
    if(!empty($_POST['ean'])){
        $filter = $db->prepare(" AND ( `guid` = %s OR `ean` = %s ) ", $params['eanguid'], $_POST['ean']);
    }


    $ean = $db->get_row($db->prepare("

    SELECT
        *
    FROM
        `ean`
    WHERE
        `companyguid` = %s
    ".$filter."
        ", $user->cguid), ARRAY_A);

    

    $allowed = array(
        'ean',
        'productguid',
        'live'
    );
    
    foreach($allowed as $field){
        if(isset($_POST[$field])){
            $updateEAN[$field] = $_POST[$field];
        }
    }

    if(!empty($ean)){
        
        // set to live.
        if(!isset($updateEAN['live'])){
            $updateEAN['live'] = 1;
        }

        $db->update('ean', $updateEAN, array('guid' => $ean['guid'], 'companyguid' => $user->cguid));

        $db->delete('confirmed', array('itemGUID' => $ean['guid']));

        $updateEAN['guid'] = $ean['guid'];

        $return = array_merge($ean, $updateEAN);

        $return = array_merge( array('message'=>'OK'), $updateEAN );

        if(isset($updateEAN['live'])){
            if($updateEAN['live'] == 0){
                $return['disabled'] = true;
            }
        }

        return $return;

    }else{

        // not found - so add it.
        if($params['eanguid'] == 'new'){
            $params['eanguid'] = gen_uuid();
        }

        $newEAN = array_merge( $updateEAN, array(
            'guid'          =>  $params['eanguid'],
            'companyguid'   =>  $user->cguid
        ));

        $db->insert('ean', $newEAN);

        $return = array_merge( array('message'=>'OK'), $newEAN );

        return $return;

    };

};

return array('message'=>'No EAN GUID supplied');
