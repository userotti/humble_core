<?php
/*

Caldoza Engine ------------------------

File	:	api/services/movement-sync.php
Created	: 	2014-01-28

*/

	recon_movement();
	recom_imei();

	/*$movements = $db->get_results("
		SELECT 
			*
		FROM `sh`
		WHERE
			`guid` NOT IN (SELECT `moveguid` FROM `movement` WHERE `moveguid` != '' GROUP BY `moveguid` );

	");

	if(!empty($movements)){
		foreach($movements as $head){
			//dump($head,0);
			$lines = $db->get_results("SELECT * FROM `sl` WHERE `guid` = '".$head->guid."';");
			if(!empty($lines)){
				foreach($lines as $line){

					$movement = array(
						'siteguid'		=>	$head->siteguid,
						'productguid'	=>	$line->productguid,
						'qty'			=>	abs($line->qty),
						'movedir'		=>	($line->qty < 0 ? 1 : -1),
						'movetype'		=>	($line->qty < 0 ? 7 : 8),
						'moveguid'		=>	$line->guid,
						'moveline'		=>	$line->line,
						'guid'			=>	gen_uuid(),
						'movecost'		=>	$line->cost,
						'moveguid'		=>	$head->guid,
						'datetime'		=>	$head->datetime,
					);
					//dump($movement);
					$db->insert('movement', $movement);

				}
			}
			//$dir = $direction[$line]
			//dump($lines);
		}
	}



	$movements = $db->get_results("
		SELECT 
			*
		FROM `mh`
		WHERE
			`movestate` = 1 
			AND
			`guid` NOT IN (SELECT `moveguid` FROM `movement` WHERE `moveguid` != '' GROUP BY `moveguid` );

	");

	$directions = $db->get_results("SELECT * FROM `movetypes`;");
	$direction = array();
	foreach($directions as $type){
		$direction[$type->movecode] = $type->movedir;		
	}

	if(!empty($movements)){
		foreach($movements as $head){
			//dump($head,0);
			$lines = $db->get_results("SELECT * FROM `ml` WHERE `guid` = '".$head->guid."';");
			if(!empty($lines)){
				foreach($lines as $line){

					$movement = array(
						'siteguid'		=>	$head->siteguid,
						'productguid'	=>	$line->productguid,
						'qty'			=>	abs($line->qty),
						'movedir'		=>	$direction[$head->movetype],
						'movetype'		=>	$head->movetype,
						'moveguid'		=>	$line->guid,
						'moveline'		=>	$line->line,
						'guid'			=>	gen_uuid(),
						'movecost'		=>	$line->linecost,
						'moveguid'		=>	$head->guid,
						'datetime'		=>	$head->datetime,
					);
					//dump($movement);
					$db->insert('movement', $movement);

				}
			}
			//$dir = $direction[$line]
			//dump($lines);
		}
	}*/
	//dump($db,0);
	//dump($movements);

?>