<?php


$rows = array();
//dump($data);
$imeis = array();
if(!empty($data['data'])){
	foreach($data['data'] as $takeguid => &$take){

		// =IMIES LIST
		foreach($take->moveimeis as $imeikey => &$imei){
			$imeis[$imei->productguid][] = $imei;

		}
		foreach($take->lines as $linekey => &$line){

			if(empty($products[$line->productguid])){
				continue;
			}
			
			$line->descr = $products[$line->productguid]->descr;

			//if(empty($imeis[$line->productguid])){
				$rows[$categories[$products[$line->productguid]->cat]->category][] = $line;
			/*}else{
				foreach($imeis[$line->productguid] as $imeiindex=>$imei){
					$line->imei = $imei->imei;
					$rows[$categories[$products[$line->productguid]->cat]->category][] = $line;
				}
			}*/

		}
	}
}


//dump($rows,0);


$totals['qty']		= 0;
$totals['unitcost']			= 0;
$totals['linecost']			= 0;
$totals['linevat']			= 0;
$totals['lineincl']			= 0;

?><!DOCTYPE html>
<html>
<head>
	<title>Inventory Count</title>
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
<h1 style="font-weight: 400;">Inventory Count</h1>
<div style="text-align: right;"><?php echo $site->sitename; ?></div>
<div style="text-align: right;">Printed at <?php echo date('Y-m-d H:i:s'); ?></div>

<!-- Table -->

<table cellpadding="0" cellspacing="0">
	<tbody>
<?php 

		if(!empty($rows)){
			//ksort($rows);
			foreach ($rows as $cat=>&$lines) {

				?>
				<tr class="midcat"><td colspan="7"><?php echo $cat; ?></td></tr>

				<tr class="midhead">
					<td style="text-align: left;">Product</td>
					<td style="text-align: left;">IMEI</td>
					<td style="text-align: right; width: 100px;">QTY</td>
					<td style="text-align: right; width: 100px;">Cost</td>
					<td style="text-align: right; width: 100px;">Line Cost</td>
					<td style="text-align: right; width: 170px;">Line Vat</td>
					<td style="text-align: right; width: 170px;">Line Incl</td>
				</tr>

				<?php
					$sub_totals['qty']				= 0;
					$sub_totals['unitcost']			= 0;
					$sub_totals['linecost']			= 0;
					$sub_totals['linevat']			= 0;
					$sub_totals['lineincl']			= 0;

				foreach ($lines as $key => &$line) {
					//dump($line);
					?>
					<tr>
						<td style="text-align: left;"><?php echo $line->descr; ?></td>
						<td style="text-align: left;"><?php echo $line->serial; ?></td>
						<td style="text-align: right; width: 100px;"><?php echo $line->qty; ?></td>
						<td style="text-align: right; width: 100px;"><?php echo money_format('%i', $line->unitcost); ?></td>
						<td style="text-align: right; width: 100px;"><?php echo money_format('%i', $line->linecost); ?></td>
						<td style="text-align: right; width: 100px;"><?php echo money_format('%i', $line->linevat); ?></td>
						<td style="text-align: right; width: 170px;"><?php echo money_format('%i', $line->lineincl); ?></td>
					</tr>
					<?php

					$sub_totals['qty']				+= $line->qty;
					$sub_totals['unitcost']			+= $line->unitcost;
					$sub_totals['linecost']			+= $line->linecost;
					$sub_totals['linevat']			+= $line->linevat;
					$sub_totals['lineincl']			+= $line->lineincl;

					$totals['qty']				+= $line->qty;
					$totals['unitcost']			+= $line->unitcost;
					$totals['linecost']			+= $line->linecost;
					$totals['linevat']			+= $line->linevat;
					$totals['lineincl']			+= $line->lineincl;


				}

			?>
			<tr class="midline"><td colspan="7"></td></tr>
			<tr class="">
				<th style="text-align: left;"></th>
				<th style="text-align: left;"></th>
				<th style="text-align: right; width: 100px;"><?php echo $sub_totals['qty']; ?></th>
				<th style="text-align: right; width: 100px;"><?php echo money_format('%i', $sub_totals['unitcost']); ?></th>
				<th style="text-align: right; width: 100px;"><?php echo money_format('%i', $sub_totals['linecost']); ?></th>
				<th style="text-align: right; width: 100px;"><?php echo money_format('%i', $sub_totals['linevat']); ?></th>
				<th style="text-align: right; width: 170px;"><?php echo money_format('%i', $sub_totals['lineincl']); ?></th>
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
			<th style="text-align: right; width: 100px;"><?php echo $totals['qty']; ?></th>
			<th style="text-align: right; width: 100px;"><?php echo money_format('%i', $totals['unitcost']); ?></th>
			<th style="text-align: right; width: 100px;"><?php echo money_format('%i', $totals['linecost']); ?></th>
			<th style="text-align: right; width: 100px;"><?php echo money_format('%i', $totals['linevat']); ?></th>
			<th style="text-align: right; width: 170px;"><?php echo money_format('%i', $totals['lineincl']); ?></th>
		</tr>
	</tfoot>
</table>
</body>
</html>







