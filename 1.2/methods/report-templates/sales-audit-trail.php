<?php


$rows = array();
//dump($data);
if(!empty($data['data'])){
	foreach($data['data'] as $saleguid => &$sale){
		
		if(!isset($users[$sale->agent])){
			continue;
		}

		if(empty($sale->lines)){
			continue;
		}
		// header
		$rows[$saleguid]['header'] = array(
			'title'		=> $sale->datetime . ' | Cashier: ' . $users[$sale->cashier]->fname . ' ' . $users[$sale->cashier]->sname . ' | Agent: ' . $users[$sale->agent]->fname . ' ' . $users[$sale->agent]->sname . ' | ' . $saletypes[$sale->lines[0]->saletype]->title
		);

		foreach ($sale->lines as $lineguid => $line) {
			if(empty($products[$line->productguid])){
				continue;
			}

			$rows[$saleguid]['lines'][$lineguid]['SKU'] = $line->stockcode;
			$rows[$saleguid]['lines'][$lineguid]['Product'] = $products[$line->productguid]->descr;
			$rows[$saleguid]['lines'][$lineguid]['Quantity'] = $line->qty;
			$rows[$saleguid]['lines'][$lineguid]['Cost'] = $line->cost;
			$rows[$saleguid]['lines'][$lineguid]['Sell'] = $line->sell;
			$rows[$saleguid]['lines'][$lineguid]['Claim'] = $line->rebate;
			$rows[$saleguid]['lines'][$lineguid]['GPR'] = $line->sell-($line->cost+$line->vat);
			$rows[$saleguid]['lines'][$lineguid]['vat'] = $line->vat;
			$rows[$saleguid]['lines'][$lineguid]['vat'] = $line->sell - ( $line->sell / ( ( 100 + $products[$line->productguid]->vat )/100 ) );
			$rows[$saleguid]['lines'][$lineguid]['vatp'] = $products[$line->productguid]->vat;

		}

	}
}


$totals['Quantity'] = 0;
$totals['Cost'] = 0;
$totals['Sell'] = 0;
$totals['Claim'] = 0;
$totals['GPR'] = 0;
$totals['vat'] = 0;

?><!DOCTYPE html>
<html>
<head>
	<title>Sales Audit Trail</title>
</head>
<style>

body{
	font-family: sans-serif;
}

table {
  border-top: 3px solid #000000;
  margin-top: 30px;
  width: 100%;
}
thead th {
  padding: 10px 0 8px;
  border-bottom: 3px solid #000000;
  font-weight: normal;
}

tfoot th {
  padding: 10px 0 8px;
  border-top: 3px solid #000000;
  border-bottom: 3px solid #000000;
  font-weight: normal;
}

td{padding: 10px 0;}

.midline td{
  border-top: 2px solid #000000;	
}

</style>
<body>

<!-- heading -->
<h1 style="font-weight: 400;">Sales Audit Trail</h1>
<div style="text-align: right;"><?php echo $site->sitename; ?></div>
<div style="text-align: right;">From <?php echo $startdate; ?> to <?php echo $enddate; ?></div>
<div style="text-align: right;">Printed at <?php echo date('Y-m-d H:i:s'); ?></div>

<!-- Table -->

<table cellpadding="0" cellspacing="0">
	
<?php 

		if(!empty($rows)){
			//ksort($rows);
			foreach ($rows as $saleguid=>&$sale) {
				//ksort($saletypes);

				?>
				<tbody>
				<tr class="midline"><td colspan="8"><?php echo $sale['header']['title']; ?></td></tr>
				</tbody>
				<thead>
				<tr>
					<td style="text-align: left;">Product</td>
					<td style="text-align: right;">Quantity</td>
					<td style="text-align: right; width: 80px;">Cost</td>
					<td style="text-align: right; width: 80px;">Sell</td>
					<td style="text-align: right; width: 80px;">Claim</td>
					<td style="text-align: right; width: 80px;">GP R</td>
					<td style="text-align: right; width: 80px;">GP %</td>
				</tr>
				</thead>
				<?php					

				$subtotals = array();

				if(empty($sale['lines'])){
					continue;
				}
				foreach ($sale['lines'] as $values) {
					//dump($values);
					?>
					<tbody>
					<tr>
						<td style="text-align: left;"><?php echo $values['Product']; ?></td>
						<td style="text-align: right; width: 80px;"><?php echo $values['Quantity']; ?></td>
						<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $values['Cost']); ?></td>
						<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $values['Sell']); ?></td>
						<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $values['Claim']); ?></td>
						<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $values['GPR']); ?></td>
						<td style="text-align: right; width: 80px;"><?php if($values['GPR'] > 0 ){ echo round( $values['GPR'] / ($values['Sell']-$values['vat']) * 100, 2); }else{ echo '0.00'; } ?></td>
					</tr>
					</tbody>
					<?php

					if(!isset($subtotals['Quantity'])){
						$subtotals['Quantity'] = 0;
					}
					if(!isset($subtotals['Cost'])){
						$subtotals['Cost'] = 0;
					}
					if(!isset($subtotals['Sell'])){
						$subtotals['Sell'] = 0;
					}
					if(!isset($subtotals['Claim'])){
						$subtotals['Claim'] = 0;
					}
					if(!isset($subtotals['GPR'])){
						$subtotals['GPR'] = 0;
					}
					if(!isset($subtotals['vat'])){
						$subtotals['vat'] = 0;
					}

					$subtotals['Quantity'] += $values['Quantity'];
					$subtotals['Cost'] += $values['Cost'];
					$subtotals['Sell'] += $values['Sell'];
					$subtotals['Claim'] += $values['Claim'];
					$subtotals['GPR'] += $values['GPR'];
					$subtotals['vat'] += $values['vat'];



					$totals['Quantity'] += $values['Quantity'];
					$totals['Cost'] += $values['Cost'];
					$totals['Sell'] += $values['Sell'];
					$totals['Claim'] += $values['Claim'];
					$totals['GPR'] += $values['GPR'];
					$totals['vat'] += $values['vat'];
				}
				?>

				<tr class="midline">
					<td style="text-align: left;"></td>
					<td style="text-align: right;"><?php echo $subtotals['Quantity']; ?></td>
					<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $subtotals['Cost']); ?></td>
					<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $subtotals['Sell']); ?></td>
					<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $subtotals['Claim']); ?></td>
					<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $subtotals['GPR']); ?></td>
					<td style="text-align: right; width: 120px;"><?php if($subtotals['GPR'] > 0 ){ echo round( $subtotals['GPR'] / ($subtotals['Sell']-$subtotals['vat']) * 100, 2);  }else{ echo '0.00'; } ?></td>
				</tr>

				<?php
			
			}
		}

?>
	<tbody>
	<tr><td colspan="8">&nbsp;</td></tr>
	</tbody>
	<tfoot>
		<tr>
			<th style="text-align: left;"></th>
			<th style="text-align: right;"><?php echo $totals['Quantity']; ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', $totals['Cost']); ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', $totals['Sell']); ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', $totals['Claim']); ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', $totals['GPR']); ?></th>
			<th style="text-align: right; width: 120px;"><?php if($values['GPR'] > 0 ){ echo round( $totals['GPR'] / ($totals['Sell']-$totals['vat']) * 100, 2);  }else{ echo '0.00'; } ?></th>
		</tr>
	</tfoot>
</table>
</body>
</html>













