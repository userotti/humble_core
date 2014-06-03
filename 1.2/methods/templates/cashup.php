<?php
/*

Caldoza Engine ------------------------

File	:	templates/products-list.php
Created	: 	2013-12-04

*/




?>
<div class="col-sm-4">
	<div id="left-panel">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Outstanding Cash Ups</h3>
			</div>
			<div class="list-group cashup-list-nav" data-call="general">
				<a href="#" class="list-group-item user-item">There are no outstanding cash ups.</a>
			</div>
		</div>
	</div>
</div>
<div class="col-sm-4">
	<div id="center-panel" style="display:none;">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Declaration</h3>
			</div>
			<div class="list-group cashup-list-nav" data-call="general">
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier="200" data-type="notes">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					R200
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier="100" data-type="notes">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					R100
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier="50" data-type="notes">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					R50
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier="20" data-type="notes">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					R20
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier="10" data-type="notes">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					R10
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier="5" data-type="coins">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					R5
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier="2" data-type="coins">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					R2
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier="1" data-type="coins">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					R1
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier=".5" data-type="coins">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					50c
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier=".2" data-type="coins">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					20c
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier=".1" data-type="coins">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					10c
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Quantity" data-multiplier=".05" data-type="coins">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					5c
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Amount" data-multiplier="1" data-type="cards" data-card="credit">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					Credit Card
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Amount" data-multiplier="1" data-type="cards" data-card="debit">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					Debit Card
				</a>
				<a href="#" class="list-group-item declaration-item" data-text="Amount" data-multiplier="1" data-type="accounts">
					<span class="pull-right text-muted"><span class="running-total">0</span></span>
					Account
				</a>
			</div>
		</div>
	</div>
</div>
<div class="col-sm-4">
	<div id="cashup-right-panel" style="display:none;">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Summary</h3>
			</div>
			<div class="list-group cashup-list-nav" data-call="general">
				<a href="#" class="list-group-item">
					<span class="pull-right text-muted"><span class="running-total type-notes">0</span></span>
					Notes
				</a>
				<a href="#" class="list-group-item">
					<span class="pull-right text-muted"><span class="running-total type-coins">0</span></span>
					Coins
				</a>
				<a href="#" class="list-group-item">
					<span class="pull-right text-muted"><span class="running-total type-cards">0</span></span>
					Cards
				</a>
				<a href="#" class="list-group-item">
					<span class="pull-right text-muted"><span class="running-total type-accounts">0</span></span>
					Accounts
				</a>
				<a href="#" class="list-group-item">
					<span class="pull-right text-muted"><span class="type-total">0</span></span>
					Total
				</a>
			</div>			
		</div>
		<button class="btn btn-primary btn-lg btn-block process-cashup">Process Cashup</button>
	</div>
</div>



<script type="text/javascript">



//jQuery(function(jQuery){

//var cashupProcess = function(){



	var cashup;



	function calculate_cashup_total(){

		var lines = jQuery('.declaration-item');


		var totals = {
			notes 	 : 0,
			coins 	 : 0,
			cards 	 : 0,
			accounts : 0,
			total 	 : 0
		};

		cashup = JSON.parse(jQuery.cookie('cashup'));

		cashup.declarecash	= 0;
		cashup.declareccard	= 0;
		cashup.declaredcard	= 0;
		cashup.declareacc	= 0;

		lines.each(function(k,v){

			var row 		= jQuery(v),
				number		= parseInt( row.find('.running-total').text() ),
				multiplier	= parseFloat( row.data('multiplier') ),
				type		= row.data('type');

				totals[type] += number*multiplier;

				totals.total += number*multiplier;

				//console.log(type);

				if(type === 'notes' || type === 'coins'){
					cashup.declarecash += number*multiplier;
					//console.log(cashup.declarecash);
				}else if(type == 'cards'){
					if(row.data('card') === 'credit' ){
						cashup.declareccard += number*multiplier;
					}else if(row.data('card') === 'debit' ){
						cashup.declaredcard += number*multiplier;
					}
				}else{
					cashup.declareacc += number*multiplier;
				}
				
		});	

		for( var type in totals){

			jQuery('.type-'+type).text(totals[type].toFixed(2));

		}

	}



	jQuery('#center-panel').on('click', '.declaration-item', function(e){

		e.preventDefault();
		var clicked = jQuery(this),
			preval = parseInt(clicked.find('.running-total').text()),
			value = prompt( jQuery(this).data('text'), preval );

		
		if(value === null || value === ''){
			return;
		}

		if(parseInt(value) >= 0){
			clicked.find('.running-total').text(parseInt(value));
		}else{
			return;
		}

		calculate_cashup_total();
	});



	// process
	jQuery('#cashup-right-panel').on('click', '.process-cashup', function(e){
		//process-cashup	
		e.preventDefault();

		if( confirm('Are you sure you want to process this Cash Up?')){

			//console.log(cashup);
			jQuery('#main-panel').addClass('loading');

			jQuery.post('http://api.humble.co.za/1.1/'+jQuery.cookie('token')+'/cashup', cashup, function(result){

				jQuery('#main-panel').removeClass('loading');

				if(result.message){

					if(result.message === 'OK'){
						// DONE! clear stuff
						jQuery.removeCookie('cashup');

						jQuery('.running-total').text('0');
						jQuery('.type-total').text('0');
						jQuery('.user-item').html('There are no outstanding cash ups.').removeClass('active');

						jQuery('#center-panel,#cashup-right-panel').slideUp(100);

						//redirect to report
						window.location = "http://api.humble.co.za/1.1/"+jQuery.cookie('token')+"/pdf-generate/cashup?template=cashup-print&guid=" + result.guid;


					}else{
						// not doen!
						//console.log(result);
					}

				}
			});

		};


	});
	

//};



//cashupProcess();




setInterval(function(){

	if(jQuery.cookie('cashup')){



		// show panels
		jQuery('#center-panel,#cashup-right-panel').slideDown(100);

			var users =  JSON.parse( sessionStorage['users'] ), user;

			for( var i = 0; i < users.users.length; i++){

				if(users.users[i].guid === jQuery.cookie('user_guid')){
					user = users.users[i].first_name + ' ' + users.users[i].last_name;
					break;
				}

			}

		jQuery('.user-item').html( user ).addClass('active');
	}
}, 1000);






/*



*/



</script>