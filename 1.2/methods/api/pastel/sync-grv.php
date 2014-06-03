<?php

global $user;

$user = new stdClass;
// FOR PROCESSING grvs SYNC


// customer builder
$translate_field = array(
	//"datetime"		=> "DueDate",
	"datetime"		=> "DueDate",
	"guid"			=>	"DocumentNumber",
	//"dbguid"			=> "",
	//"movetype"		=> "",
	//"movestate"		=> "",
	"acc_pastelid"	=> "SupplierId",
	//"acc"			=> "",
	"refnr"			=> "Reference",
	//"deviceGUID"	=> "",
	//"deviceName"	=> "",
	//"userguid"		=> "",
	"excl"			=> "Exclusive",
	"vat"			=> "Tax",
	"incl"			=> "Total",
	//"direction"		=> "",
);


// field types.
$translate_lines = array(
	//"guid"			=> "",
	//"line"			=> "",
	"descr"			=> "Description",
	"qty"			=> "Quantity",
	"linecost"		=> "Exclusive",
	"linevat"		=> "Tax",
	//"lineincl"		=> "", // manual
	"pastelid"		=> "SelectionId", // manual
	"vat"			=> "TaxPercentage"
);

/*

HUMBLE


            [guid] => 2C018AA4-F8C2-4A2C-90F4-EFADD02BDEEC
            [companyguid] => b4ef0994-8288-11e3-9b7f-005056ba42b8
            [siteguid] => e6f6a24f-ada3-11e3-bb1e-005056ba5bac
            [datetime] => 2014-03-25 11:30:29
            [dbguid] => 
            [movetype] => 1
            [movestate] => 1
            [accguid] => 815BB2AB-BD00-4495-B1451332ECCAD807
            [acc] => Socket Mobile USA
            [refnr] => Inv 4
            [deviceGUID] => 43A616F0-6BC0-4D23-B2C4-F77886ED99CE
            [deviceName] => Rodneys iPad
            [userguid] => 8da7f4b2-8289-11e3-9b7f-005056ba42b8
            [excl] => 6989.84
            [vat] => 978.58
            [incl] => 7968.42
            [direction] => IN
            [pastelid] => 


LINES
            [guid] => 2C018AA4-F8C2-4A2C-90F4-EFADD02BDEEC
            [line] => 0
            [productguid] => bc0b3a50-9795-11e3-bb1e-005056ba5bac
            [descr] => Charging Cradle 7Di/7Pi/7Xi (Black)
            [qty] => 10
            [linecost] => 635.44
            [linevat] => 14.00
            [lineincl] => 649.44
            [serial] => 
            [movementguid] => 


PASTEL

                    [DueDate] => 2014-03-05T00:00:00Z
                    [SupplierId] => 285386
                    [ID] => 19919284
                    [Date] => 2014-03-05T00:00:00Z
                    [Inclusive] => 
                    [DiscountPercentage] => 0
                    [DocumentNumber] => SIV0000012
                    [Reference] => test42
                    [Discount] => 0
                    [Exclusive] => 864.2
                    [Tax] => 120.99
                    [Rounding] => 0
                    [Total] => 985.19
                    [AmountDue] => 985.19
                    [Printed] => 

LINES

                    [SelectionId] => 2121423
                    [TaxTypeId] => 1
                    [ID] => 18133908
                    [Description] => Shipping Fee
                    [LineType] => 0
                    [Quantity] => 10
                    [UnitPriceExclusive] => 86.42
                    [UnitPriceInclusive] => 98.52
                    [TaxPercentage] => 0.14
                    [DiscountPercentage] => 0
                    [Exclusive] => 864.2
                    [Discount] => 0
                    [Tax] => 120.99
                    [Total] => 985.19


*/

$translate_field_types = array(
	'live' 						=> 'boolean',
	'pastelid' 					=> 'int',
	'SupplierId' 				=> 'int',
	'Inclusive' 				=> 'float',
	'Exclusive' 				=> 'float',
	'Tax' 						=> 'float',
	'AmountDue' 				=> 'float',
	'UnitPriceExclusive' 		=> 'float',
	'UnitPriceInclusive' 		=> 'float',
	'TaxPercentage' 			=> 'float',
	'Total'			 			=> 'float',
	

);


$sites = $db->get_results( "SELECT * FROM `sites` WHERE `pasteluser` != '' AND `pastelpass` != '' AND `pastelid` != ''; " );

foreach( $sites as $site){
	
	// set user & pass
	$user->pastel_user = $site->pasteluser;
	$user->pastel_pass = $site->pastelpass;

	// pastel args
	$args = array( 
		'companyid'	=> $site->pastelid,

	);

	// get site's grvs

	$query = "SELECT
	`mh`.*,
	`acc`.`pastelid` as `acc_pastelid`

		FROM `mh`
		
		LEFT JOIN `pasteltranslate` AS `acc` ON (`mh`.`accguid` = `acc`.`itemguid` AND `acc`.`siteguid` = '".$site->guid."')

		WHERE 
		`mh`.`pastelid` = 0
		AND
		(`mh`.`movetype`= 1 OR `mh`.`movetype`= 2) 
		AND
		`mh`.`companyguid` = '".$site->coguid."';";

	//dump($query);

	// humble grvs
	$grvs = $db->get_results( $query, ARRAY_A);

	if(empty($grvs)){
		return;
	}
	foreach($grvs as &$grv){

		// if supplier pastel ID is missing, skip and wait for suipplier sync
		if(empty($grv['acc_pastelid'])){
			continue;
		}

		// reset insert object
		$insert = array();

		// uses the traslate_fiel array for usable fields

		// ADDITIONALS
		$insert['Date'] = date('Y-m-d H:i:s');
		$insert['Inclusive'] = false;
		$insert['Rounding']	= 0;
		$insert['AmountDue'] = (float) $grv['incl'];
		// FROM HEADER

		foreach($translate_field as $cfield=>$sfield){

			// create customer object to send
			if(isset($translate_field_types[$sfield])){
				settype($grv[$cfield], $translate_field_types[$sfield]);
			}

			$insert[$sfield] = $grv[$cfield];

		}

		//dump($insert);
		// FROM LINES
		$linesquery = "SELECT
			`ml`.*,
			`products`.`vat`,
			`pasteltranslate`.`pastelid`

		FROM `ml`

		LEFT JOIN `pasteltranslate` ON (`ml`.`productguid` = `pasteltranslate`.`itemguid` AND `pasteltranslate`.`siteguid` = '".$site->guid."')
		LEFT JOIN `products` ON (`ml`.`productguid` = `products`.`guid` AND `products`.`companyguid` = '".$site->coguid."')

			WHERE 
				`ml`.`guid` = '".$grv['guid']."';";

		// run query 
		$lines = $db->get_results($linesquery, ARRAY_A);
		if(empty($lines)){
			continue;
		}
		$insert['Lines'] = array();
		foreach ($lines as $lineid => $line) {
			
			$insert['Lines'][$lineid] = array(
				"LineType"	=> 0
			);

			if(!empty($line['vat'])){
				$insert['Lines'][$lineid]['TaxTypeId'] = 1;
			}else{
				$insert['Lines'][$lineid]['TaxTypeId'] = 0;
			}

			foreach ($translate_lines as $hfield => $pfield) {

				if(isset($translate_field_types[$pfield])){
					settype($line[$hfield], $translate_field_types[$pfield]);
				}

				$insert['Lines'][$lineid][$pfield] = $line[$hfield];
			}
			// UNIT INCL
			//$unitInc = $line['line']
			// VAT ammount

			// ADD unit price explusive
			$insert['Lines'][$lineid]['UnitPriceExclusive'] = (float) $line['linecost'] / $line['qty'];
			$insert['Lines'][$lineid]['UnitPriceInclusive'] = (float) $line['lineincl'] / $line['qty'];

			//dump(  );
			

		}

		// send to pastel
		//dump($insert);
		//$insert = array();

		$insert_result = do_pastel_call( 'SupplierInvoice/save', $args, $insert );
		//dump($insert_result);

		if(isset($insert_result['ID'])){
			// Add ID 
			$db->update('mh', array('pastelid'	=>	$insert_result['ID']), array('guid' => $grv['guid'] , 'companyguid' => $site->coguid) );

		}else{
			dump($insert_result);
		}

	}



}

