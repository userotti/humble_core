<?php
/*

Caldoza Engine ------------------------

File	:	api/products/update-product.php
Created	: 	2013-12-09

*/
/*
ob_start();
dump($_POST,0);
$debug = ob_get_clean();
$db->insert('debugnotes', array('message'=>$debug));

*/





if(!empty($params['productguid'])){
    $product = $db->get_row($db->prepare("

    SELECT
        *
    FROM
        `products`
    WHERE
        `companyguid` = %s
    AND `guid` = %s
        ", $user->cguid,  $params['productguid']), ARRAY_A);

    if(!empty($product)){
        // no edits
        $nonedit = array(
            'guid',
            'companyguid',
            'insdate',
            'changed',
            'weight'
        );

        // is Update
        foreach($product as $field=>&$value){
            if(in_array($field, $nonedit)){
                continue;
            }
            if(isset($_POST[$field])){
                $value = $_POST[$field];
            }
        }

        $product['changed'] = date('Y-m-d H:i:s');

        $db->update('products', $product, array('guid'=>$params['productguid']));
        update_product_on_pastel($params['productguid']);
        // remove
        $db->delete('confirmed', array('itemGUID'=>$params['productguid']));

        $product['message'] = 'OK';
        cloudSync($user->siteguid,$user->deviceGUID,'update-product.php','update product');
        return $product;
    }else{


        if($params['productguid'] == 'new'){
            $params['productguid'] = gen_uuid();
            $new = true;
        }

        $newProduct = array(
            'guid'          =>  $params['productguid'],
            'companyguid'   =>  $user->cguid,
            'insdate'       =>  date('Y-m-d H:i:s'),
        );
        $allowed = array(
            'stockcode',
            'descr',
            'cat',
            'si',
            'cost',
            'sell',
            'vat',
            'brand',
            'subtype',
            'weight',
            'parent',
            'virtual',
            'producttype',
            'live'
        );
        foreach($allowed as $field){
            if(isset($_POST[$field])){
                $newProduct[$field] = $_POST[$field];
            }
        }

        $db->insert('products', $newProduct);
        update_product_on_pastel($params['productguid']);
        if(!empty($new)){

        }

        if(!empty($new)){
            $product = $db->get_row($db->prepare("

            SELECT
                *
            FROM
                `products`
            WHERE
                `companyguid` = %s
            AND `guid` = %s
                ", $user->cguid,  $newProduct['guid']), ARRAY_A);
            cloudSync($user->siteguid,$user->deviceGUID,'update-product.php','new product a');
            return array('message'=>'OK', 'product' => $product);

        };

        cloudSync($user->siteguid,$user->deviceGUID,'update-product.php','new product');
        return array('message'=>'OK', 'guid' => $newProduct['guid']);

    }
}else{

    $newProduct = array(
        'guid'          =>  gen_uuid(),
        'companyguid'   =>  $user->cguid,
        'insdate'       =>  date('Y-m-d H:i:s'),
    );
    $allowed = array(
        'stockcode',
        'descr',
        'cat',
        'si',
        'cost',
        'sell',
        'vat',
        'brand',
        'subtype',
        'weight',
        'parent',
        'virtual',
        'producttype',
        'live'
    );
    foreach($allowed as $field){
        if(isset($_POST[$field])){
            $newProduct[$field] = $_POST[$field];
        }
    }
    
    $newProduct['live'] = 1;

    $db->insert('products', $newProduct);
    update_product_on_pastel($params['productguid']);
    cloudSync($user->siteguid,$user->deviceGUID,'update-product.php','new product b');
    return array('message'=>'OK', 'guid' => $newProduct['guid']);
}
