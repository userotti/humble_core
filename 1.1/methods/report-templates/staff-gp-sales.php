<?php


$rows = array();

$report_title = "Staff GP Sales";
//dump($data);

if(!empty($data['data'])){
	foreach($data['data'] as $saleguid => &$sale){
		
		// header
		$rows[$sale->agent]['header'] = array(
			'title'		=> $users[$sale->agent]->fname . ' ' . $users[$sale->agent]->sname
		);

		foreach ($sale->lines as $lineguid => $line) {
			if(empty($products[$line->productguid])){
				continue;
			}

			if(!isset($rows[$sale->agent]['lines'][$line->productguid])){
				$rows[$sale->agent]['lines'][$line->productguid]['Cat'] = 0;
				$rows[$sale->agent]['lines'][$line->productguid]['Product'] = 0;
				$rows[$sale->agent]['lines'][$line->productguid]['Quantity'] = 0;
				$rows[$sale->agent]['lines'][$line->productguid]['Cost'] = 0;
				$rows[$sale->agent]['lines'][$line->productguid]['Sell'] = 0;
				$rows[$sale->agent]['lines'][$line->productguid]['Claim'] = 0;
				$rows[$sale->agent]['lines'][$line->productguid]['GPR'] = 0;
				$rows[$sale->agent]['lines'][$line->productguid]['vat'] = 0;
			}



			$rows[$sale->agent]['lines'][$line->productguid]['Cat'] = $categories[$products[$line->productguid]->cat]->category;
			$rows[$sale->agent]['lines'][$line->productguid]['Product'] = $products[$line->productguid]->descr;
			$rows[$sale->agent]['lines'][$line->productguid]['Quantity'] += $line->qty;
			$rows[$sale->agent]['lines'][$line->productguid]['Cost'] += $line->cost;
			$rows[$sale->agent]['lines'][$line->productguid]['Sell'] += $line->sell;
			$rows[$sale->agent]['lines'][$line->productguid]['Claim'] += $line->rebate;
			$rows[$sale->agent]['lines'][$line->productguid]['GPR'] += $line->sell-($line->cost+$line->vat);
			$rows[$sale->agent]['lines'][$line->productguid]['vat'] += $line->vat;

		}

	}
}


//dump($data['data']);
//dump($rows);

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
.midhead td{
  border-bottom: 1px solid #000000;	
}

</style>
<body>

<!-- heading -->
<h1 style="font-weight: 400;"><?php echo $report_title; ?></h1>
<div style="text-align: right;"><?php echo $site->sitename; ?></div>
<div style="text-align: right;">From <?php echo $startdate; ?> to <?php echo $enddate; ?></div>
<div style="text-align: right;">Printed at <?php echo date('Y-m-d H:i:s'); ?></div>

<!-- Table -->

<table cellpadding="0" cellspacing="0">
	
<?php 

		if(!empty($rows)){
			ksort($rows);
			foreach ($rows as $saleguid=>&$sale) {
				//ksort($saletypes);

				?>
				<tbody>
				<tr class="midline"><td colspan="8"><?php echo $sale['header']['title']; ?></td></tr>
				<tr class="midhead">
					<td style="text-align: left; width:210px;">Category</td>
					<td style="text-align: left;">Product</td>
					<td style="text-align: right;">Quantity</td>
					<td style="text-align: right; width: 80px;">Cost</td>
					<td style="text-align: right; width: 80px;">Sell</td>
					<td style="text-align: right; width: 80px;">Claim</td>
					<td style="text-align: right; width: 80px;">GP R</td>
					<td style="text-align: right; width: 80px;">GP %</td>
				</tr>
				</tbody>
				<?php					

				$subtotals = array();

				foreach ($sale['lines'] as $values) {
					//dump($values);
					?>
					<tbody>
					<tr>
						<td style="text-align: left;"><?php echo $values['Cat']; ?></td>
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
			<th style="text-align: left;"></th>
			<th style="text-align: right;"><?php echo $totals['Quantity']; ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', $totals['Cost']); ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', $totals['Sell']); ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', $totals['Claim']); ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', $totals['GPR']); ?></th>
			<th style="text-align: right; width: 120px;"><?php if($totals['GPR'] > 0 ){ echo round( $totals['GPR'] / ($totals['Sell']-$totals['vat']) * 100, 2);  }else{ echo '0.00'; } ?></th>
		</tr>
	</tfoot>
</table>
</body>
</html>













