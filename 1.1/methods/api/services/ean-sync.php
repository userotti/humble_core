<?php
/*

Caldoza Engine ------------------------

File	:	api/services/ean-sync.php
Created	: 	2014-01-31

*/



	$items = $db->get_results("SELECT * FROM `tblStockRecord`");

	$count = 0;
	foreach($items as $item){
		//dump($item,0);
		$product = $db->get_row("SELECT * FROM `products` WHERE `stockcode` = '".$item->StockCode."' AND `companyguid` = '008c672d-5b2c-11e3-8696-005056a5104a';");
		//dump($product);
		$newean = array(
			'guid'	=>	gen_uuid(),
			'companyguid'	=>	'008c672d-5b2c-11e3-8696-005056a5104a',
			'productguid'	=>	$product->guid,
			'ean'	=>	$item->StockBarCode,
		);
		echo $item->StockBarCode.'<br>';
		
		if($db->insert('ean', $newean)){
			$count++;
		}else{
			//dump($db);
		}
		
	}



echo 'Done '.$count;;
?>