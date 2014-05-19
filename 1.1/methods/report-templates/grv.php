<?php

//dump($data);

$rows = array();
$type = 'Received';
if(!empty($data['data'])){
	foreach($data['data'] as $guid => &$adjustment){
		
		$reference = $adjustment->refnr;
		$date = $adjustment->datetime;
		$direction = $adjustment->direction;
		$acc = $adjustment->acc;
		$processed = $users[$adjustment->userguid]->fname.' '.$users[$adjustment->userguid]->sname;


		foreach ($adjustment->lines as $lineguid => $line) {
			if(empty($products[$line->productguid])){				
				continue;
			}

			$rows['lines'][$lineguid]['Product'] = $products[$line->productguid]->descr;
			$rows['lines'][$lineguid]['Quantity'] = $line->qty;
			$rows['lines'][$lineguid]['Cost'] = $line->linecost;
			$rows['lines'][$lineguid]['Vat'] = $line->linevat;
			$rows['lines'][$lineguid]['Incl'] = $line->lineincl;			

		}

	}
}

if($direction == 'OUT'){
	$type = 'Returned';
}
$totals['Quantity'] = 0;
$totals['Cost'] = 0;
$totals['Sell'] = 0;
$totals['Vat'] = 0;
$totals['Incl'] = 0;

?><!DOCTYPE html>
<html>
<head>
	<title>Goods <?php echo $type; ?> Voucher</title>
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
<h1 style="font-weight: 400;">Goods <?php echo $type; ?> Voucher - <?php echo $acc; ?> - <?php echo $reference; ?></h1>
<div style="text-align: right;"><?php echo $site->sitename; ?></div>
<div style="text-align: right;">Reference: <?php echo $reference; ?></div>
<div style="text-align: right;">Processed on <?php echo $date.' by ' .$processed; ?></div>
<div style="text-align: right;">Printed at <?php echo date('Y-m-d H:i:s'); ?></div>

<!-- Table -->

<table cellpadding="0" cellspacing="0">

<?php 

		if(!empty($rows)){
			foreach ($rows['lines'] as $index=>&$line) {


					?>

					<tr><td colspan="8"><?php echo $line['Product']; ?></td></tr>
						<tr>
							<td style="text-align: left;">IMEI</td>
							<td style="text-align: right; width: 80px;">Quantity</td>
							<td style="text-align: right; width: 80px;">Cost</td>
							<td style="text-align: right; width: 80px;">Line Cost</td>
						</tr>
					<?php					


					$subtotals = array();


						?>
						<tr>
							<td style="text-align: left;">   </td>
							<td style="text-align: right; width: 80px;"><?php echo $line['Quantity']; ?></td>
							<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $line['Cost']/$line['Quantity']); ?></td>
							<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $line['Cost']); ?></td>
						</tr>
						<?php

						if(!isset($subtotals['Quantity'])){
							$subtotals['Quantity'] = 0;
						}
						if(!isset($subtotals['Cost'])){
							$subtotals['Cost'] = 0;
						}


						$subtotals['Quantity'] += $line['Quantity'];
						$subtotals['Cost'] += $line['Cost'];



						$totals['Quantity'] += $line['Quantity'];
						$totals['Cost'] += $line['Cost'];

					?>

					<tr class="midline">
						<td style="text-align: left;"></td>
						<td style="text-align: right; width: 80px;"><?php echo $subtotals['Quantity']; ?></td>
						<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $subtotals['Cost']/$subtotals['Quantity']); ?></td>
						<td style="text-align: right; width: 80px;"><?php echo money_format('%i', $subtotals['Cost']); ?></td>
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
			<th style="text-align: right; width: 80px;"><?php echo $totals['Quantity']; ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', ( $totals['Cost'] > 0 ? $totals['Cost']/$totals['Quantity'] : '0' ) ); ?></th>
			<th style="text-align: right; width: 80px;"><?php echo money_format('%i', $totals['Cost']); ?></th>
		</tr>
	</tfoot>
</table>
</body>
</html>













