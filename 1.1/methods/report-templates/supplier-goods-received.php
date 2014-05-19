<?php


// Row Fields to use 

$rowfields = array(
	'datetime'		=>	'Date',
	'acc'			=>	'Supplier',
	'movetype'		=>	'Movement',
	'refnr'			=>	'Invoice Nr',
	'excl'			=>	'Excl',
	'vat'			=>	'VAT',
	'incl'			=>	'Incl',
);

$rows = array();
if(!empty($data['data'])){
	// Setup an index for each line
	$index = 0;
	foreach($data['data'] as &$row){
		//dump($row,0);
		$row = (array) $row;

		foreach($row as $field=>&$value){
			if(is_array( $value )){
				// ignore array since its single level
				continue;
			}

			// set the movetype to the field
			if($field === 'movetype'){
				$value = $movetypes[$value]->movedescr;
			}

		}


		// go over the fields requested build the rows array
		foreach($rowfields as $field=>$Label){
			$rows[$index][$field] = $row[$field];
		}

		$index++;
	}
}

// Build Totals
$totals = array(
	'excl'	=> 0,
	'vat'	=> 0,
	'incl'	=> 0,
);

?><!DOCTYPE html>
<html>
<head>
	<title>Supplier Goods Received</title>
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
<h1 style="font-weight: 400;">Supplier Goods Received</h1>
<div style="text-align: right;"><?php echo $site->sitename; ?></div>
<div style="text-align: right;">From <?php echo $startdate; ?> to <?php echo $enddate; ?></div>
<div style="text-align: right;">Printed at <?php echo date('Y-m-d H:i:s'); ?></div>

<!-- Table -->

<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
		<?php
		// go over each field requested and make headings

		// setup alignments ans size
		$align = array();

		foreach($rowfields as $field=>$Label){

			// settings for alignment
			if(is_int($rows[0][$field])){
				// is a number.
				$align[$field] = 'text-align: center; width: 80px;';
			}elseif( false !== strpos( $rows[0][$field], '.' )){
				$align[$field] = 'text-align: right; width: 80px;';
			}elseif( date('Y-m-d H:i:s', strtotime($rows[0][$field])) == $rows[0][$field] ){
				// is a date - might be a hack - but should work :)
				$align[$field] = 'text-align: left; width: 170px;';
			}else{
				$align[$field] = 'text-align: left';
			}

		?>
			<th style="<?php echo $align[$field]; ?>"><?php echo $Label; ?></th>
		<?php
		}
		?>
		</tr>
	</thead>
<?php 

		if(!empty($rows)){
			foreach ($rows as $guid=>&$row) {
			?>
			<tr>
			<?php foreach($row as $field=>$value){

				// if there is a total for this field - add it
				if(isset($totals[$field])){
					$totals[$field] += $value;
					// since its in the totals - its a money format
					$value = money_format( "%i", $value);
				}


			?>
				<td style="<?php echo $align[$field]; ?>"><?php echo $value; ?></td>
			<?php
			}
			?>
			</tr>
			<?php

			}
		}

?>
	<tbody>
	<tr><td colspan="8">&nbsp;</td></tr>
	</tbody>
	<?php
	// if there are totals then build the totals row.
	if(!empty($totals)){
	?>
	<tfoot>
		<tr>
			<?php
			// count colum vs rows			
			if(count($totals) !== count($rowfields)){
				// if the totals have less columns then span the first to push the columns to the right.
				echo "<th colspan=\"" . ( count( $rowfields ) - count( $totals ) ) . "\"></th>\r\n";
			}

			// go over all totals and output thier value
			foreach($totals as $field=>$value){
				// totals will always be numeric (float) so its safe to apply money format
				echo "<th style=\"" . $align[$field] . "\">" . money_format( '%i', $value ) . "</th>\r\n";
			}

			?>
		</tr>

	</tfoot>
	<?php } ?>
</table>
</body>
</html>













