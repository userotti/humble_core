<?php


// Row Fields to use 

//dump($data,0);

$report_title = "Multi Level Template";

$rowfields = array(
	'datetime'			=>	'Date',
	'cash'				=>	'Cash',
	'declarecash'		=>	'Decl Cash',
	'ccard'				=>	'C/Card',
	'declareccard'		=>	'Decl C/Card',
	'dcard'				=>	'D/Card',
	'declaredcard'		=>	'Decl D/Card',
	'acc'				=>	'Account',
	'declareacc'		=>	'Decl Account',
	'declareacc'		=>	'Decl Account',

);

$blocks = array();
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

			// set the movetype to the field
			if($field === 'cashierguid'){
				$value = $users[$value]->fname.' '.$users[$value]->sname;
			}

		}

		// go over the fields requested build the blocks array
		foreach($rowfields as $field=>$Label){
			$blocks[$row['cashierguid']][$index][$field] = $row[$field];
		}

		$index++;
	}
}

//dump($rows);
// Build Totals
$totals = array(
	'cash'				=>	0,
	'declarecash'		=>	0,
	'ccard'				=>	0,
	'declareccard'		=>	0,
	'dcard'				=>	0,
	'declaredcard'		=>	0,
	'acc'				=>	0,
	'declareacc'		=>	0,
	'declareacc'		=>	0
);

$subtitletmpl = $totals;

?><!DOCTYPE html>
<html>
<head>
	<title><?php echo $report_title; ?></title>
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
<h1 style="font-weight: 400;"><?php echo $report_title; ?></h1>
<div style="text-align: right;"><?php echo $site->sitename; ?></div>
<div style="text-align: right;">From <?php echo $startdate; ?> to <?php echo $enddate; ?></div>
<div style="text-align: right;">Printed at <?php echo date('Y-m-d H:i:s'); ?></div>

<!-- Table -->

<table cellpadding="0" cellspacing="0">
<?php foreach ($blocks as $title => $rows) { ?>




	<thead>
		<tr>
			<td colspan="<?php echo count($rowfields); ?>"><?php echo $title; ?></td>
		</tr>
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
			
			$subtotals = $subtitletmpl;


			foreach ($rows as $guid=>&$row) {
			?>
			<tr>
			<?php foreach($row as $field=>$value){

				// if there is a subtotal for this field - add it
				if(isset($subtotals[$field])){
					$subtotals[$field] += $value;
				}


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


	// Subtitle if there are any.
	// if there are subtotals then build the subtotals row.
	if(!empty($subtotals)){
	?>
	<tbody>
		<tr class="midline">
			<?php
			// count colum vs rows			
			if(count($subtotals) !== count($rowfields)){
				// if the subtotals have less columns then span the first to push the columns to the right.
				echo "<td colspan=\"" . ( count( $rowfields ) - count( $subtotals ) ) . "\"></td>\r\n";
			}

			// go over all subtotals and output thier value
			foreach($subtotals as $field=>$value){
				// totals will always be numeric (float) so its safe to apply money format
				echo "<td style=\"" . $align[$field] . "\">" . money_format( '%i', $value ) . "</td>\r\n";
			}

			?>
		</tr>

	</tfoot>
	<?php }

}

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













