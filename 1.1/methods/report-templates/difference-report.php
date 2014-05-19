<?php


$rows = array();
//dump($data);
if(!empty($data['items'])){
	foreach($data['items'] as $linekey => &$line){

		if(empty($products[$line['productguid']])){
			continue;
		}

		//dump($line);
		if(abs($line['onhand'] - $line['counted']) !== 0){
			$rows[$categories[$products[$line['productguid']]->cat]->category][$linekey] = $line;
		}

	}
}


$totals['onhand']		= 0;
$totals['counted']		= 0;
$totals['cost']			= 0;
$totals['costdiff']		= 0;

?><!DOCTYPE html>
<html>
<head>
	<title>Inventory | Differences</title>
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
.midcat td{
  font-size: 20px;	
}

</style>
<body>

<!-- heading -->
<h1 style="font-weight: 400;">Inventory | Differences</h1>
<div style="text-align: right;"><?php echo $site->sitename; ?></div>
<div style="text-align: right;">Printed at <?php echo date('Y-m-d H:i:s'); ?></div>

<!-- Table -->

<table cellpadding="0" cellspacing="0">
	<tbody>
<?php 

		if(!empty($rows)){
			ksort($rows);
			foreach ($rows as $cat=>&$lines) {

				?>
				<tr class="midcat"><td colspan="7"><?php echo $cat; ?></td></tr>

				<tr class="midhead">
					<td style="text-align: left;">Product</td>
					<td style="text-align: left;">IMEI</td>
					<td style="text-align: right;">On Hand</td>
					<td style="text-align: right; width: 100px;">Counted</td>
					<td style="text-align: right; width: 100px;">Difference</td>
					<td style="text-align: right; width: 100px;">Cost</td>
					<td style="text-align: right; width: 170px;">Cost Difference</td>
				</tr>

				<?php
				$sub_totals['onhand']		= 0;
				$sub_totals['counted']		= 0;
				$sub_totals['cost']			= 0;
				$sub_totals['costdiff']		= 0;

				foreach ($lines as $key => &$line) {
					//dump($line);
					?>
					<tr>
						<td style="text-align: left;"><?php echo $line['productdescr']; ?></td>
						<td style="text-align: left;"><?php echo $line['imei']; ?></td>
						<td style="text-align: right; width: 100px;"><?php echo $line['onhand']; ?></td>
						<td style="text-align: right; width: 100px;"><?php echo $line['counted']; ?></td>
						<td style="text-align: right; width: 100px;"><?php echo abs($line['onhand'] - $line['counted']); ?></td>
						<td style="text-align: right; width: 100px;"><?php echo money_format('%i', $line['cost']); ?></td>
						<td style="text-align: right; width: 170px;"><?php echo money_format('%i', $line['costdiff']); ?></td>
					</tr>
					<?php

					$sub_totals['onhand']		+= $line['onhand'];
					$sub_totals['counted']		+= $line['counted'];
					$sub_totals['cost']			+= $line['cost'];
					$sub_totals['costdiff']		+= $line['costdiff'];


					$totals['onhand']		+= $line['onhand'];
					$totals['counted']		+= $line['counted'];
					$totals['cost']			+= $line['cost'];
					$totals['costdiff']		+= $line['costdiff'];


				}

			?>
			<tr class="midline"><td colspan="7"></td></tr>
			<tr class="">
				<th style="text-align: left;"></th>
				<th style="text-align: left;"></th>
				<th style="text-align: right; width: 100px;"><?php echo $sub_totals['onhand']; ?></th>
				<th style="text-align: right; width: 100px;"><?php echo $sub_totals['counted']; ?></th>
				<th style="text-align: right; width: 100px;"><?php echo abs($sub_totals['onhand'] - $sub_totals['counted']); ?></th>
				<th style="text-align: right; width: 100px;"><?php echo money_format('%i', $sub_totals['cost']); ?></th>
				<th style="text-align: right; width: 170px;"><?php echo money_format('%i', $sub_totals['costdiff']); ?></th>
			</tr>

			<?php

			}
		}

?>

	<tr><td colspan="7">&nbsp;</td></tr>
	</tbody>
	<tfoot>
		<tr>
			<th style="text-align: left;"></th>
			<th style="text-align: left;"></th>
			<th style="text-align: right; width: 100px;"><?php echo $totals['onhand']; ?></th>
			<th style="text-align: right; width: 100px;"><?php echo $totals['counted']; ?></th>
			<th style="text-align: right; width: 100px;"><?php echo abs($totals['onhand'] - $totals['counted']); ?></th>
			<th style="text-align: right; width: 100px;"><?php echo money_format('%i', $totals['cost']); ?></th>
			<th style="text-align: right; width: 170px;"><?php echo money_format('%i', $totals['costdiff']); ?></th>
		</tr>
	</tfoot>
</table>
</body>
</html>













