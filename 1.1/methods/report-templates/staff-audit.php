<?php


//dump($data);


$report_title = "Staff Audit Log";

// Row Fields to use 
//dump($data);
$rowfields = array(
	'datetime'		=>	'Date',
	'note'			=>	'Note',
);

$rows = array();
if(!empty($data['data'])){
	// Setup an index for each line
	$index = 0;
	foreach($data['data'] as $index=>&$row){
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

			if($field === 'movedir'){
				if($value == '1'){
					$value = 'In';
				}else{
					$value = 'Out';
					if(isset($row['qty'])){
						$row['qty'] = $row['qty'] - ( $row['qty'] * 2 );
					}

				}
			}

		}


		// go over the fields requested build the rows array
		foreach($rowfields as $field=>$Label){
			//dump($users[$row['cashierguid']]);
			$rows[$users[$row['cashierguid']]->fname.' '. $users[$row['cashierguid']]->sname][$index][$field] = $row[$field];
		}

		$index++;
	}
}

//dump($rows);

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
.midheadline td{
  border-bottom: 2px solid #000000;	
}
.userline td{
	border-top: 2px solid #000000;
	padding-top: 25px;
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
			foreach ($rows as $guid=>&$row) {
			?>
			<tbody>
				<tr class="userline"><td colspan="2"><?php echo $guid; ?></td></tr>
				<tr class="midheadline">
				<?php
				// go over each field requested and make headings

				// setup alignments ans size
				$align = array();
				
				$align['datetime'] = 'text-align: left; width: 200px;';
				$align['note'] = 'text-align: left';

				foreach($rowfields as $field=>$Label){
						
					

				?>
					<td style="<?php echo $align[$field]; ?>"><?php echo $Label; ?></td>
				<?php
				}
				?>
				</tr>
			
			<?php 
			foreach($row as $line){
				echo '<tr>';
				
				foreach($line as $field=>$value){
					
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
				echo '</tr>';
			}
			?>
			</tr>
			</tbody>
			<?php

			}
		}

?>

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













