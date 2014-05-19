<?php

$header = $db->get_row( $db->prepare( "SELECT * FROM `sh` WHERE `guid` = %s;", $params['saleguid'] ) );
if(empty($header)){
	echo '<h1 style="background-color: #ff00aa;">ERROR: Sale not found.</h1>';
	return;
}
$site = $db->get_row("SELECT * FROM `sites` WHERE `guid` = '".$header->siteguid."';");
//$company = $db->get_row("SELECT * FROM `companies` WHERE `guid` = '".$site->coguid."';");
$lines = $db->get_results("
	SELECT 
		
		`sl`.`imei` AS `line_imei`,
		`sl`.`qty` AS `line_qty`,
		`sl`.`cost` AS `line_cost`,
		`sl`.`sell` AS `line_sell`,
		`sl`.`vat` AS `line_vat`,

		`products`.`stockcode` AS `stockcode`,
		`products`.`descr` AS `product`,
		`saleTypes`.*

	FROM `sl`
	LEFT JOIN `products` ON (`sl`.`productguid` = `products`.`guid`)
	LEFT JOIN `saleTypes` ON (`sl`.`saletype` = `saleTypes`.`guid`)

	WHERE `sl`.`guid` = '".$header->guid."';");

// GROUP sales by Type
$sales = array();
foreach($lines as $line){
	$sales[$line->title][] = $line;
}

?><!DOCTYPE html>
<html>
<head>
	<title>humble</title>
	<!-- Bootstrap -->
	<link href="http://till.humble.co.za/static/site/css/bootstrap.css" rel="stylesheet" media="all">
</head>
<body style="padding: 50px; margin: 0px;">
	<div class="container" id="main-panel">
		<div class="row">
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<h4><?php echo $site->sitename; ?></h4>
				<?php
					if(!empty($site->address1) AND strtolower($site->address1) != 'n/a'){
						echo '<address>';
							echo $site->address1;
							if(!empty($site->address2) AND strtolower($site->address2) != 'n/a'){
								echo ', '.$site->address2.'<br>';
							}else{
								echo '<br>';
							}
							
							if(!empty($site->addr3) AND strtolower($site->addr3) != 'n/a'){
								echo $site->addr3.'<br>';
							}

							if(!empty($site->tel) AND strtolower($site->tel) != 'n/a'){
								echo '<strong>Telephone:</strong> '.$site->tel.'</strong><br>';
							}

							if(!empty($site->fax) AND strtolower($site->fax) != 'n/a'){
								echo '<strong>Fax:</strong> '.$site->fax.'</strong><br>';
							}

							if(!empty($site->email) AND strtolower($site->email) != 'n/a'){
								echo '<strong>Email:</strong> '.$site->email.'</strong><br>';
							}

							if(!empty($site->regnr) AND strtolower($site->regnr) != 'n/a'){
								echo '<strong>Reg. No:</strong> '.$site->regnr.'</strong><br>';
							}

							if(!empty($site->vatnr) AND strtolower($site->vatnr) != 'n/a'){
								echo '<strong>VAT NO:</strong> '.$site->vatnr.'</strong><br>';
							}




						echo '</address>';
					}
				?>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center">
				<h2>TAX INVOICE</h2>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right">
				<?php echo date("F j, Y, g:i a"); ?>
			</div>
		</div>
		<table class="table">
		<?php foreach($sales as $type=>$sale){
			// reset line total
			$line_total = 0;			
		?>
			<thead>
				<tr>
					<th colspan="6"><?php echo $type; ?></th>
				</tr>
				<tr>
					<th>SKU</th>
					<th>Product</th>
					<th>IMEI</th>
					<th class="text-center">Quantity</th>
					<th class="text-right">Cost</th>
					<th class="text-right">Line Cost</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($sale as $line){
					$line_total += $line->line_sell*$line->line_qty;
				?>
				<tr>
					<td><?php echo $line->stockcode; ?></td>
					<td><?php echo $line->product; ?></td>
					<td><?php echo $line->line_imei; ?></td>
					<td class="text-center"><?php echo $line->line_qty; ?></td>
					<td class="text-right"><?php echo $line->line_sell; ?></td>
					<td class="text-right"><?php echo money_format('%i', $line_total); ?></td>
				</tr>
				<?php } ?>
				<tr>
					<th colspan="6">&nbsp;</th>
				</tr>				
			</tbody>
		<?php } ?>
			<tfoot>
				<tr>
					<th colspan="5" class="text-right">Total</th>
					<th class="text-right"><?php echo $header->incl; ?></th>
				</tr>
			</tfoot>
		</table>
		
		<?php if(!empty($site->slipline1) && strtolower($site->slipline1) != 'n/a'){ ?><span style="clear:right;" class="pull-right"><?php echo $site->slipline1; ?></span><?php } ?>
		<?php if(!empty($site->slipline2) && strtolower($site->slipline2) != 'n/a'){ ?><span style="clear:right;" class="pull-right"><?php echo $site->slipline2; ?></span><?php } ?>
		<?php if(!empty($site->slipline3) && strtolower($site->slipline3) != 'n/a'){ ?><span style="clear:right;" class="pull-right"><?php echo $site->slipline3; ?></span><?php } ?>

	</div>
</body>
</html>