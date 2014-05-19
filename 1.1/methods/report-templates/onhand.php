<?php

//dump($categories);
$rows = array();
if(!empty($data['data'])){
	foreach($data['data'] as $index => &$line){

			if(empty($altproducts[$line['productguid']])){
				continue;
			}


			if(!isset($rows[$categories[$altproducts[$line['productguid']]->cat]->category][$altproducts[$line['productguid']]->descr]['Quantity'])){
				$rows[$categories[$altproducts[$line['productguid']]->cat]->category][$altproducts[$line['productguid']]->descr]['Quantity'] = 0;
			}
			if(!isset($rows[$categories[$altproducts[$line['productguid']]->cat]->category]['Cost'])){
				$rows[$categories[$altproducts[$line['productguid']]->cat]->category][$altproducts[$line['productguid']]->descr]['Cost'] = 0;
			}

			$rows[$categories[$altproducts[$line['productguid']]->cat]->category][$altproducts[$line['productguid']]->descr]['Quantity'] += $line['on_hand'];
			$rows[$categories[$altproducts[$line['productguid']]->cat]->category][$altproducts[$line['productguid']]->descr]['Cost'] += $altproducts[$line['productguid']]->cost;


	}
}

//dump($rows);

$totals['Quantity'] = 0;
$totals['Cost'] = 0;
$totals['LineCost'] = 0;

?><!DOCTYPE html>
<html>
<head>
	<title>Inventory On Hand</title>
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
.midline td {
  border-bottom: 2px solid #000000;
}

td{padding: 10px 0;}

.midline-top td{
	border-top: 2px solid #000000;	
}
</style>
<body>

<!-- heading -->
<h1 style="font-weight: 400;">Inventory On Hand</h1>
<div style="text-align: right;"><?php echo $site->sitename; ?></div>
<div style="text-align: right;">Printed at <?php echo date('Y-m-d H:i:s'); ?></div>

<!-- Table -->

<table cellpadding="0" cellspacing="0">
<?php 
	
		if(!empty($rows)){
			ksort($rows);
			
			foreach ($rows as $cat=>&$lines) {
				ksort($lines);

				//dump($lines);

				$subtotals = array(
					'Quantity'	=>	0,
					'Cost'	=>	0,
					'LineCost'	=>	0,
				);
				?>				
				<tbody>
					<tr><td colspan="4" style="padding-top: 20px;"><?php echo $cat; ?></td></tr>
					<tr class="midline">
						<td style="text-align: left;">Product</td>
						<td style="text-align: right;">Quantity</td>
						<td style="text-align: right; width: 120px;">Cost</td>
						<td style="text-align: right; width: 120px;">Line Cost</td>
					</tr>
				<?php
				foreach($lines as $product=>$values){

					?>
					<tr>
						<td style="text-align: left;"><?php echo $product; ?></td>
						<td style="text-align: right;"><?php echo $values['Quantity']; ?></td>
						<td style="text-align: right; width: 120px;"><?php echo money_format('%i', $values['Cost']); ?></td>
						<td style="text-align: right; width: 120px;"><?php echo money_format('%i', $values['Cost']*$values['Quantity']); ?></td>
					</tr>
					<?php

					if(!isset($totals['Quantity'])){
						$totals['Quantity'] = 0;
					}

					if(!isset($totals['Cost'])){
						$totals['Cost'] = 0;
					}

					if(!isset($totals['LineCost'])){
						$totals['LineCost'] = 0;
					}

					$totals['Quantity'] += $values['Quantity'];
					$totals['Cost'] += $values['Cost'];
					$totals['LineCost'] += $values['Cost']*$values['Quantity'];

					$subtotals['Quantity'] += $values['Quantity'];
					$subtotals['Cost'] += $values['Cost'];
					$subtotals['LineCost'] += $values['Cost']*$values['Quantity'];


				}
				?>
				<tr class="midline-top">
					<td style="text-align: left;"></td>
					<td style="text-align: right;"><?php echo $subtotals['Quantity']; ?></td>
					<td style="text-align: right; width: 120px;"><?php echo money_format('%i', $subtotals['Cost']); ?></td>
					<td style="text-align: right; width: 120px;"><?php echo money_format('%i', $subtotals['LineCost']); ?></td>
				</tr>

				</tbody>
				<?php

			}
		}

?>

	<tfoot>
		<tr><td colspan="4" style="padding-top: 20px;"></td></tr>
		<tr>
			<th style="text-align: left;"></th>
			<th style="text-align: right;"><?php echo $totals['Quantity']; ?></th>
			<th style="text-align: right; width: 120px;"><?php echo money_format('%i', $totals['Cost']); ?></th>
			<th style="text-align: right; width: 120px;"><?php echo money_format('%i', $totals['LineCost']); ?></th>
		</tr>
	</tfoot>
</table>
</body>
</html>













