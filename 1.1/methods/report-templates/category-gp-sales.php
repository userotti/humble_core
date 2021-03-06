<?php


$rows = array();
//dump($data);
if(!empty($data['data'])){
	foreach($data['data'] as $saleguid => &$sale){

		foreach ($sale->lines as $lineguid => $line) {
			if(empty($products[$line->productguid])){
				continue;
			}

			if(!isset($rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title])){
				$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['Quantity'] = 0;
				$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['Cost'] = 0;
				$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['Sell'] = 0;
				$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['Claim'] = 0;
				$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['GPR'] = 0;
				$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['GPP'] = 0;
				$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['vat'] = 0;
			}

			//dump($line);

			$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['Quantity'] += $line->qty;
			$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['Cost'] += $line->cost;
			$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['Sell'] += $line->sell;
			$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['Claim'] += $line->rebate;
			$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['GPR'] += $line->sell-($line->cost+$line->vat);
			$rows[$categories[$products[$line->productguid]->cat]->category][$saletypes[$line->saletype]->title]['vat'] += $line->vat;

		}

	}
}



$totals['Quantity'] = 0;
$totals['Cost'] = 0;
$totals['Sell'] = 0;
$totals['Claim'] = 0;
$totals['GPR'] = 0;

?><!DOCTYPE html>
<html>
<head>
	<title>Category Gross Profit Sales</title>
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
</style>
<body>

<!-- heading -->
<h1 style="font-weight: 400;">Category Gross Profit Sales</h1>
<div style="text-align: right;"><?php echo $site->sitename; ?></div>
<div style="text-align: right;">From <?php echo $startdate; ?> to <?php echo $enddate; ?></div>
<div style="text-align: right;">Printed at <?php echo date('Y-m-d H:i:s'); ?></div>

<!-- Table -->

<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th style="text-align: left;">Category</th>
			<th style="text-align: right;">Quantity</th>
			<th style="text-align: right; width: 120px;">Cost</th>
			<th style="text-align: right; width: 120px;">Sell</th>
			<th style="text-align: right; width: 120px;">Claim</th>
			<th style="text-align: right; width: 120px;">GP R</th>
			<th style="text-align: right; width: 120px;">GP %</th>
		</tr>
	</thead>
	<tbody>
<?php 

		if(!empty($rows)){
			ksort($rows);
			foreach ($rows as $cat=>&$saletypes) {
				ksort($saletypes);

				foreach ($saletypes as $saletype => &$values) {
					?>
					<tr>
						<td style="text-align: left;"><?php echo $cat; ?> - <?php echo $saletype; ?></td>
						<td style="text-align: right;"><?php echo $values['Quantity']; ?></td>
						<td style="text-align: right; width: 120px;"><?php echo money_format('%i', $values['Cost']); ?></td>
						<td style="text-align: right; width: 120px;"><?php echo money_format('%i', $values['Sell']); ?></td>
						<td style="text-align: right; width: 120px;"><?php echo money_format('%i', $values['Claim']); ?></td>
						<td style="text-align: right; width: 120px;"><?php echo money_format('%i', $values['GPR']); ?></td>
						<td style="text-align: right; width: 80px;"><?php if($values['GPR'] > 0 ){ echo round( $values['GPR'] / ($values['Sell']-$values['vat']) * 100, 2); }else{ echo '0.00'; } ?></td>
					</tr>
					<?php

					if(!isset($totals['Quantity'])){
						$totals['Quantity'] = 0;
					}
					if(!isset($totals['Cost'])){
						$totals['Cost'] = 0;
					}
					if(!isset($totals['Sell'])){
						$totals['Sell'] = 0;
					}
					if(!isset($totals['Claim'])){
						$totals['Claim'] = 0;
					}
					if(!isset($totals['GPR'])){
						$totals['GPR'] = 0;
					}
					if(!isset($totals['vat'])){
						$totals['vat'] = 0;
					}

					$totals['Quantity'] += $values['Quantity'];
					$totals['Cost'] += $values['Cost'];
					$totals['Sell'] += $values['Sell'];
					$totals['Claim'] += $values['Claim'];
					$totals['GPR'] += $values['GPR'];
					$totals['vat'] += $values['vat'];

				}

			}
		}

?>
	</tbody>
	<tfoot>
		<tr>
			<th style="text-align: left;"></th>
			<th style="text-align: right;"><?php echo $totals['Quantity']; ?></th>
			<th style="text-align: right; width: 120px;"><?php echo money_format('%i', $totals['Cost']); ?></th>
			<th style="text-align: right; width: 120px;"><?php echo money_format('%i', $totals['Sell']); ?></th>
			<th style="text-align: right; width: 120px;"><?php echo money_format('%i', $totals['Claim']); ?></th>
			<th style="text-align: right; width: 120px;"><?php echo money_format('%i', $totals['GPR']); ?></th>
			<th style="text-align: right; width: 120px;"><?php if($totals['GPR'] > 0 ){ echo round( $totals['GPR'] / ($totals['Sell']-$totals['vat']) * 100, 2);  }else{ echo '0.00'; } ?></th>
		</tr>
	</tfoot>
</table>
</body>
</html>













