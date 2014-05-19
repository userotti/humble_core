<?php
/*

Caldoza Engine ------------------------

File	:	api/products/update-category.php
Created	: 	2014-01-06

*/


        $allowed = array(
			'cat',
			'category',
			'live',
        );
        $newdata = array();
        foreach($allowed as $field){
            if(isset($_POST[$field])){
                $newdata[$field] = $_POST[$field];
            }
        }
        $newdata['companyguid'] = $user->cguid;

        $is_cat = $db->get_var($db->prepare("SELECT `guid` FROM `categories` WHERE `guid` = %s", $params['catid']));
        if(!empty($is_cat)){
            
            $db->update('categories', $newdata, array('guid'=>$params['catid'], 'companyguid'=>$user->cguid));
            
            $db->delete('confirmed', array('itemGUID' => $params['catid']));

        }else{

            if($params['catid'] == 'new'){
                $params['catid'] = gen_uuid();
            }
            
            $newdata['guid'] = $params['catid'];
            
            $newdata['cat'] = $db->get_var($db->prepare("SELECT `cat` FROM `categories` WHERE `companyguid` = %s ORDER BY `cat` DESC LIMIT 1;", $user->cguid))+1;



            console($newdata);

            $db->insert('categories', $newdata);
        }
        return array('message'=>'OK', 'guid' => $params['catid'], 'updated' => $newdata);
