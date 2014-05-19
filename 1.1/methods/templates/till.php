<?php

/*

Caldoza Engine ------------------------

File	:	templates/till.php
Created	: 	2014-01-13

*/





$channel = $db->get_var("SELECT `channelguid` FROM `companies` WHERE `guid` = '".$user->cguid."' ");
$saletypes = $db->get_results("SELECT * FROM `saleTypes` WHERE `channel` = '".$channel."';");

?>
<span class="trigger" data-call="products" data-cache-session="products" data-cache="true" data-event="none" data-autoload="true" data-type="json" data-load-element="#app-window"></span>
<span class="trigger" data-call="ean" data-cache-session="eans" data-cache="true" data-event="none" data-autoload="true" data-type="json" data-load-element="#app-window"></span>

<div id="tender-complete-panel" class="row" style="display:none;">
	<div style="min-height: 445px;" class="col-md-6 col-lg-6 col-md-offset-3 col-lg-offset-3">
		<div class="panel panel-default" style="margin-top: 150px;">
			<div class="panel-body text-center">
				<p class="lead"><i class="glyphicon glyphicon-thumbs-up"></i> Sale Complete</p>
				<span class="complete-change-line"></span>
				<br>
				<button class="btn btn-lg btn-primary text-center btn-balk" id="till-new-sale">New Sale</button>

			</div>
		</div>
	</div>
</div>
<div id="tender-panel" class="row" style="display:none;">
	<div class="col-md-6 col-lg-6" style="min-height: 445px;">
		<button type="button" class="btn btn-black" id="back-tender">Back</button>

		<span id="user-selector">
		</span>
		<hr>
		<div id="tender-summary"></div>
		<div class="row tender-email">
			<div class="col-md-12 col-lg-12">
				<h3>Email Invoice?</h3>
			</div>
		</div>
		<div class="row tender-email">
			
			<div class="col-md-6 col-lg-6">
				<input class="form-control" type="text" id="customer-name" placeholder="Customer Name">
			</div>
			<div class="col-md-6 col-lg-6">
				<input class="form-control" type="text" id="customer-email" placeholder="Customer Email Address">
			</div>
		</div>
	</div>
	<div class="col-md-6 col-lg-6">
		<div class="panel panel panel-body panel-info" style="min-height: 380px;">
			<h4>Tender</h4>
			<div id="tender-list-panel"></div>
			<div id="tender-type-selector" class="btn-group-vertical btn-block" style="display:none;">
				<button data-type="cash" class="tender-select btn btn-humble btn-default">Cash</button>
				<button data-type="dcard" class="tender-select btn btn-humble btn-default">Debit Card</button>
				<button data-type="ccard" class="tender-select btn btn-humble btn-default">Credit Card</button>
				<button data-type="acc" class="tender-select btn btn-humble btn-default trigger" data-callback="focus_search" data-modal="acc_select" data-group="acc-sel" data-call="customers" data-cache-session="customers" data-active-class="npe" data-template="#acc-list-tmpl" data-target="#customers-list">Customer Account</button>
			</div>
			<div id="tender-value-selector" class="input-group" style="display:none;">
				<span class="input-group-btn">
					<button id="cancel-tender-input" class="btn btn-default btn-humble" type="button">Cancel</button>
				</span>
				<input id="add-tender-input" type="text" class="form-control" style="text-align: center;">
				<span class="input-group-btn">
					<button class="btn btn-default btn-humble" type="button" id="capture-tender">Add Tender</button>
				</span>
			</div>
			<button class="btn btn-default btn-humble btn-lg btn-block" id="add-tender-button">Add Tender</button>	
		</div>
		<button id="complete-tender" type="button" disabled="disabled" class="btn btn-default btn-lg btn-block" style="text-shadow: none;">Complete Sale</button>
	</div>
	<div class="col-md-1 col-lg-1">
	</div>
	<div class="col-md-5 col-lg-5">
	</div>
</div>
<div id="till-panel" class="row">
	<?php if( count( $saletypes ) > 1){ ?>
	<div class="col-md-1 col-lg-1 sale-type-tray">
		<?php foreach($saletypes as $type){ ?>
		<div class="panel panel-default panel-till draggable" data-guid="<?php echo $type->guid; ?>">
			<div class="panel-body" style="background-color: #<?php echo $type->color; ?>;"><?php echo $type->title; ?></div>
		</div>
		<?php } ?>
	</div>
	<div class="col-md-8 col-lg-8">
		<?php }else{ ?>
		<div class="col-md-9 col-lg-9">
			<?php } ?>
			<div class="well well-sm well-till">
				<div class="row" style="margin-bottom: 10px;">
					<div class="col-md-12 col-lg-12">
						<button class="btn btn-humble btn-block btn-default add-basket-inner" onclick="addBasketType();">Add Basket</button>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 col-lg-6 basket-case" id="leftcolumn-till" style="min-height: 90px;">
						<?php if(count($saletypes) === 1){ ?>
						<div id="basket_01" class="subdraggable item-basket active-item-basket" data-guid="<?php echo $saletypes[0]->guid; ?>">
							<div class="panel panel-default panel-sm saletype" data-color="<?php echo $saletypes[0]->color; ?>" data-type="<?php echo $saletypes[0]->descr; ?>">
								<div class="panel-body" style="background-color: #<?php echo $saletypes[0]->color; ?>;">
									<button type="button" class="trigger btn btn-danger btn-xs pull-right" data-callback="remove_panel" data-type="basket" data-name="<?php echo $saletypes[0]->title; ?>" style="padding:0 5px">×</button>
									<h4><?php echo $saletypes[0]->title; ?></h4>
									<div class="basket-merge basket-case"></div>

									<button type="button" class="btn btn-default btn-humble btn-block trigger add-product active-basket" data-modal-title="Add Product" data-active-class="active-basket" data-group="baskets" data-call="products" data-cache-session="products" data-modal="addprod" data-callback="cache_product_list" data-template-url="template/select-product-list" data-summary="true" data-before="check_cache">Add Product</button>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
					<div class="col-md-6 col-lg-6 basket-case" style="min-height: 90px;" id="rightcolumn-till">
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-lg-3 till-total">
			<div id="selected-item-panel" style="height: 361px; padding:0" class="well well-sm">
			</div>
			<div class="panel panel-default panel-sm">
				<div class="panel-body panel-info">
					<p>Sub Total</p>
					<h3 class="text-right">R <span id="running-total">0.00</span></h3>				
				</div>
				<button id="tender-sale" type="button" class="btn btn-lg btn-block" disabled="true" style="border-radius: 0; text-shadow: none;">Tender</button>
			</div>
		</div>
		<div class="printer-status" style="float:left;">
			<button id="printer-online" style="display:none;cursor: default;clear:both;" type="button" class="btn btn-link last-rec disabled">Printer Online</button>
			<button id="printer-online-reprint" style="display:none;cursor: cursor:default;clear:both;" type="button"  class="btn btn-link last-rec">Print Last Receipt</button>
			<span id="printer-offline" style="display:none;clear:both; display: inline-block; margin-top: 5px;" class="text-muted">Printer offline</span>
			<span id="printer-notset" style="display:none;clear:both; display: inline-block; margin-top: 5px;" class="text-muted">No Printer set</span>
		</div>
		
		<button style="cursor:default;" type="button"  class="btn btn-link pop-draw" style="clear:both;">Open Cash Drawer</button>
		<button style="cursor:default;" type="button"  class="btn btn-link" onclick="start_payout();" data-modal="payout" data-modal-content="#payout-tmpl" data-modal-title="Pay Out" style="clear:both;">Pay Out</button>

	</div>

	<input type="hidden" id="tillscanevent" class="trigger" data-event="click" data-call="ean" data-cache-session="eans" data-active-class="currentbutton" data-callback="do_scancode_till">

	<span class="trigger" data-request="http://localhost:9200/" data-poll="5000" data-group="print_check_poller" data-before="check_printerStatus" data-callback="check_printerStatus" data-error="check_printerStatus" data-timeout="5000"></span>

	<?php foreach($saletypes as $type){ ?>
	<script type="text/html" class="basket-template" id="<?php echo $type->descr; ?>-tmpl" data-name="<?php echo $type->title; ?>">
		<div data-guid="<?php echo $type->guid; ?>">
			<div class="panel panel-default panel-sm saletype" data-type="<?php echo $type->descr; ?>" data-color="<?php echo $type->color; ?>">
				<div class="panel-body" style="background-color: #<?php echo $type->color; ?>;">
					<button type="button" class="trigger btn btn-danger btn-xs pull-right" data-callback="remove_panel" data-type="basket" data-name="<?php echo $type->title; ?>" style="padding:0 5px">×</button>

					<h4><?php echo $type->title; ?></h4>
					<div class="basket-merge basket-case"></div>

					<button type="button" class="btn btn-default btn-humble btn-block trigger add-product active-basket" data-modal-title="Add Product" data-active-class="active-basket" data-group="baskets" data-call="products" data-cache-session="products" data-modal="addprod" data-callback="cache_product_list" data-template-url="template/select-product-list" data-summary="true" data-before="check_cache">Add Product</button>
				</div>
			</div>
		</div>
	</script>
	<?php } ?>

	<script type="text/html" id="product-line-tmpl">
		<div class="basket-item-wrap">
			<div class="basket-item panel panel-default {{stockcode}}" data-stockcode="{{stockcode}}" data-guid="{{guid}}" data-cost="{{cost}}" data-vat="{{vat}}">
				<div class="panel-body panel-default" style="cursor: pointer;">
					<button type="button" class="trigger btn btn-danger btn-xs pull-right" data-callback="remove_panel" data-type="line" style="padding:0 5px">×</button>
					<button type="button" class="btn btn-humble btn-xs pull-right" style="padding:0 5px"><i class="glyphicon glyphicon-info-sign"></i></button>

					<small class="product-title">{{descr}}</small><br>
					<small><span class="item-count">1</span> x R<span class="sell-price">{{sell}}</span> = R<span class="item-sub-total">{{sell}}</span></small>
				</div>
			</div>
		</div>
	</script>
	<script type="text/html" id="product-line-si-tmpl">
		<div class="basket-item-wrap">
			<div class="basket-item panel panel-default {{stockcode}}" data-stockcode="{{stockcode}}" data-guid="{{guid}}" data-cost="{{cost}}" data-vat="{{vat}}">
				<div class="panel-body panel-default" style="cursor: pointer;">
					<button type="button" class="trigger btn btn-danger btn-xs pull-right" data-callback="remove_panel" data-type="line" style="padding:0 5px">×</button>
					<span class="text-muted" style="padding:0 5px"><i class="glyphicon glyphicon-info-sign"></i></span>

					<small class="product-title">{{descr}}</small><br>
					<small class="product-serial">{{serial}}</small><br>
					<small><span class="item-count" style="display:none;">1</span>R<span class="sell-price">{{sell}}</span><span class="item-sub-total" style="display:none;">{{sell}}</span></small>
				</div>
			</div>
		</div>
	</script>
	<script type="text/html" id="tariff-line-tmpl">
		<div class="input-group">
			<input type="text" class="form-control" data-event="keyup" data-before="filter_products">
			<span class="input-group-btn">
				<button class="btn btn-default btn-humble"  type="button">Search</button>
			</span>
		</div>
		<br>
		<table class="table table-condensed table-hover" id="products-table" style="">
			<tbody id="table_product">
				{{#each tariffs}}
				<tr data-active-class="success" data-tariff="{{guid}}" data-title="{{tariff_description}}" data-load-element="#addprod_baldrickModalBody" data-callback="addToActivate" class="trigger pagination-item {{rowclass @index}}">
					<td>{{tariff_description}}</td>
				</tr>
				{{/each}}
			</tbody>
		</table>
		<div class="panel-body">
			<ul id="paginator"></ul>
		</div>
	</script>
	<script id="acc-list-tmpl" type="text/html">
	<div class=" has-feedback">
		<input class="form-control customer-list-search" id="customer-search">
		<span class="glyphicon glyphicon-search form-control-feedback"></span>
	</div>
		<br>
		<div class="list-group">
		{{#each customers}}
		<a href="#" class="list-group-item trigger" data-before="add_acc_to_sale" data-guid="{{guid}}">{{descr}}</a>
		{{/each}}
		</div>
	</script>
	<script type="text/html" id="keypad-tmpl">
		<h4 style="" class="text-center panel panel-heading">8</h4>
		<div class="row" style="">
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block">1</button></div>
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block">2</button></div>
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block">3</button></div>
		</div>
		<div class="row">
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block">4</button></div>
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block">5</button></div>
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block">6</button></div>
		</div>
		<div class="row">
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block">7</button></div>
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block">8</button></div>
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block">9</button></div>
		</div>
		<div class="row">
			<div class="col-xs-4 col-xs-offset-4" style="padding: 5px;"><button class="btn btn-primary btn-block">0</button></div>
			<div class="col-xs-4" style="padding: 5px;"><button class="btn btn-primary btn-block"><</button></div>
		</div>
	</script>

	<script type="text/html" id="user-selector-tmpl">

			<span id="sale-agent" data-agentguid="<?php echo $user->uguid; ?>"></span>
			<div class="btn-group">
				<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
					<span class="active-agent"><?php echo $user->fname.' '.$user->sname; ?></span> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
				{{#each users}}
					<li><a href="#" onclick="setagent(this); return false;" data-guid="{{guid}}">{{first_name}} {{last_name}}</a></li>
				{{/each}}
				</ul>
			</div>

	</script>
	<script type="text/html" id="payout-tmpl">
		<div class="form-group">
			<label for="payout-reason">Enter Pay Out Reason</label>
			<input type="text" class="form-control" id="payout-reason">
		</div>
		<div class="form-group">
			<label for="payout-amount">Enter Payout Amount</label>
			<input type="text" class="form-control" id="payout-amount">
		</div>
	</script>
	<script type="text/javascript">



		var productlist = {
				total	: 0
			},
			tenderrequired = 0,
			tillscancode='',
			cleartillscancode,
			printerid,
			use_draw = 0,
			auto_lock = 0,
			email_reciepts = 0,
			small_print = 0,
			print_a4_receipts = 0;

		// interval based
		setInterval(function(){
			if(localStorage.getItem('use_cash_drawer')){
				use_draw = parseInt(localStorage.getItem('use_cash_drawer'));
				if(use_draw === 1){
					jQuery('.pop-draw').show();
				}else{
					jQuery('.pop-draw').hide();
				}
			}else{
				jQuery('.pop-draw').hide();
			}
			if(localStorage.getItem('print_a4_receipts')){
				print_a4_receipts = parseInt(localStorage.getItem('print_a4_receipts'));
			}
			if(localStorage.getItem('autolock_after_sale')){
				auto_lock = parseInt(localStorage.getItem('autolock_after_sale'));
			}
			if(localStorage.getItem('email_reciepts')){
				email_reciepts = parseInt(localStorage.getItem('email_reciepts'));
			}
			if(localStorage.getItem('print_small_fonts')){
				small_print = parseInt(localStorage.getItem('print_small_fonts'));
			}

			if(email_reciepts === 0){
				jQuery('.tender-email').hide();
			}else{
				jQuery('.tender-email').show();
			}

			/// set printer id
			if(localStorage.getItem('humble-printer')){
				printerid = localStorage.getItem('humble-printer');
			}

		}, 1000);
		
		// payout process
		function start_payout(){

			var reason, amount;

			if(reason = prompt('Enter Pay Out Reason')){
				if(amount = prompt('Enter Pay Out Amount', '0.00')){
					//var payout = 
					var send = {
						reason: reason,
						amount: amount
					};

					jQuery.post('http://api.humble.co.za/1.1/'+jQuery.cookie('token')+'/payout', send, function(res){
						if(res.message === 'OK'){
							var send = {
								printer: printerid,
								slip: res.pop
							};
							
							jQuery.post('http://localhost:9200/', send, function(res){
								console.log(res);
							}).fail(function(){
								alert('Printer is not Online');
							});

							//alter cashup
							if(jQuery.cookie('cashup')){
								cashuplog = JSON.parse(jQuery.cookie('cashup'));
								cashuplog.cash -= parseFloat(amount);
								jQuery.cookie('cashup', JSON.stringify(cashuplog), { expires: 365 });
							}						
						}
					})
				}
			}

		}


		if(typeof windowEvents['tillScanner'] === 'undefined'){

			windowEvents['tillScanner'] = true;	

			window.addEventListener('keydown', function(e){
				
				if(!jQuery('#tillscanevent').length || !jQuery('.icon-till').hasClass('active')){
					return;
				}				

				if(e.keyCode !== 13){
					tillscancode += String.fromCharCode( e.keyCode );
				}else if(e.keyCode === 13 && tillscancode.length){
					e.preventDefault();

					jQuery('#tillscanevent').val(tillscancode).trigger('click');
					tillscancode = '';
					clearTimeout(cleartillscancode);
				}
				if(cleartillscancode){
					clearTimeout(cleartillscancode);
				}
				cleartillscancode = setTimeout(function(){
					tillscancode = '';
				}, 250);

			});
		}


		function do_scancode_till(obj){

			var barcode = obj.params.trigger.val(), found = false;
			obj.params.trigger.val('');

			if(barcode.length){

				if(obj.data.total > 0){
					for( var i in obj.data.ean ){

						if( obj.data.ean[i].ean === barcode && !obj.data.ean[i].disabled ){

							if(!jQuery('.item-basket').length){
								addBasketType();
							}

							var req = jQuery('<span>', {
								"class"				: "trigger",
								"data-call" 		: "products",
								"data-cache-session": "products",
								"data-callback"		: "addToBasket",
								"data-guid"			: obj.data.ean[i].productguid
							});

							req.appendTo(jQuery('body'));
							baldrickTrigger();
							req.trigger('click').remove();
							found = true;
							break;
						}
					}
				}

				if(!found){
					jQuery('#selected-item-panel').html('<h3 class="alert alert-warning text-center">ITEM NOT FOUND</h3>');
					setTimeout(function(){
						jQuery('#selected-item-panel h3').fadeOut(300);
					}, 2000);
				}
			}
		}

		function addBasketTypeAs(type){
			var basket 	= $( $(type).html() ),
			id		= 'basket_' + $('.subdraggable').length + 1;




			basket.attr('id', id).find('.add-product.trigger').attr('data-reference', id);
		// replace {{channels}}


		basket.addClass('subdraggable').appendTo( $(event.target) );

	}


	jQuery(document).ready(function($) {

		buildDroppers();
	});

	function add_acc_to_sale(el){
		var clicked = jQuery(el),
			guid = clicked.data('guid'),
			input = jQuery('#add-tender-input');

			input.attr('data-guid', guid);

		jQuery('#acc_select_baldrickModal').modal('hide');
		return false;
	}

	function calculateTotal(complete){

		var newtotal = 0,
		running = jQuery('#running-total'),
		tenderout = jQuery('#tender-sale'),
		subitems = jQuery('.basket-item'),
		addtender = jQuery('#add-tender-input'),
		tendered = jQuery('#tendered-line'),
		tenderedlines = jQuery('.tender-values');

		if(subitems.length){
			subitems.each(function(k,v){
				var line = jQuery(v),
				sell = parseFloat( line.find('.sell-price').html() ),
				qty = parseFloat( line.find('.item-count').html() ),
				sub = line.find('.item-sub-total'),
				linetotal = sell * qty,
				subline = parseFloat( linetotal ).toFixed(2);
				if(subline !== 'NaN'){
					sub.html( subline );
				}else{
					sub.html( '0.00' );
				}
				newtotal += linetotal;
			});
			tenderout.prop('disabled', false).addClass('btn-primary');
		}else{
			tenderout.prop('disabled', true).removeClass('btn-primary');
		}
		runningTotal = newtotal.toFixed(2);
		if(runningTotal !== 'NaN'){
			running.html(runningTotal);
			addtender.val( runningTotal );
		}else{
			running.html('0.00');
			addtender.val( '0.00' );
		}

	// tender lines:
	var tenderlines = 0;
	tenderedlines.each(function(k,v){
		tenderlines += parseFloat( jQuery(v).html());
	});
	if( runningTotal === tenderlines){
		tenderrequired = 0;
	}else{
		// add requirement

		tenderrequired = runningTotal - tenderlines;
	}
	if( runningTotal <= tenderlines){
		jQuery('#complete-tender').addClass('btn-primary').prop('disabled', false);
	}else{
		jQuery('#complete-tender').removeClass('btn-primary').prop('disabled', true);
	}
	// add tender lines
	
	if(!tendered.length && tenderedlines.length){
		jQuery('#tender-summary').append('<h3 id="tendered-line" class="panel panel-default"><div class="panel-body panel-info"><span class="pull-right">R<span id="tendered-total">'+tenderlines.toFixed(2)+'</span></span>Tendered</div></h3>');
	}else if(tendered.length && tenderedlines.length){		
		jQuery('#tendered-total').html(tenderlines.toFixed(2));
	}else{
		jQuery('#tendered-line').remove();
	}

	jQuery('#tendered-change-line').remove();
	
	if(runningTotal < tenderlines){
		// add in change
		var change = tenderlines - runningTotal
		jQuery('#tender-summary').append('<h3 id="tendered-change-line" class="panel panel-default"><div class="panel-body panel-danger"><span class="pull-right">R<span id="tendered-change">'+change.toFixed(2)+'</span></span>Change</div></h3>');

		jQuery('.complete-change-line').html('<h3 class="panel panel-default text-left" id="completed-tendered-change-line"><div class="panel-body panel-danger"><span class="pull-right">R<span id="tendered-change">'+change.toFixed(2)+'</span></span>Change</div></h3>');
	}else{
		if(!complete){
			jQuery('.complete-change-line').html('');
		}
	}

	baldrickTriggers();


}
function addToActivate(obj){
	jQuery('#addact_baldrickModal').modal('hide');
	var tariff = jQuery(obj),
	active = jQuery('.active-activation').parent();

	active.append( '<small>'+tariff.data('title')+'</small>' );

}
function addToBasket(obj){

	jQuery('#addprod_baldrickModal').modal('hide');

	var guid = obj.params.trigger.data('guid');

	if( obj.data.total > 0 ){

		for(var i in obj.data.products){

			if( obj.data.products[i].guid === guid ){

				var current_total = jQuery('#running-total'),
				basket = jQuery('.active-basket'),
				item = basket.prev(),
				newtotal = 0,
				others = item.find('.' + obj.data.products[i].stockcode),
				template,
				count = 1;



				if( others.length && obj.data.products[i].si === "0"){
					var qty = others.find( '.item-count' );
							//total = others.find( '.item-sub-total' );

							qty.html( parseFloat( qty.html() ) + count );
						//total.html( ( parseFloat( total.html() ) + parseFloat( obj.data.sell ) ).toFixed(2) );




					}else{
						var si = 0;
						if(obj.data.products[i].si){
							si = parseInt(obj.data.products[i].si);
						}

						if(si){
							var go = true;
							
							obj.data.products[i].serial = prompt('Serial Number');

							if(typeof obj.data.products[i].serial !== 'string'){
								return;
							}
							if(!obj.data.products[i].serial.length){
								return;
							}
							jQuery('.product-serial').each(function(k,v){
								var serial = jQuery(v).html();
								if(serial === obj.data.products[i].serial){
									alert('Sorry, that serial number is already used');
									go = false;									
								}
							});



							if(!go){
								return;
							}

							template = Handlebars.compile( $('#product-line-si-tmpl').html() );
						}else{
							template = Handlebars.compile( $('#product-line-tmpl').html() );
						}
						
						var out = template( obj.data.products[i] );

						item.append( out );

					}

					if(!jQuery('#till-panel').is(':visible')){
						jQuery('#tender-complete-panel').slideUp(100);
						jQuery('#tender-panel').slideUp(100);
						jQuery('#till-panel').slideDown(100);
					}

					calculateTotal();




					break;
				}
			}

		}

	}

	function cache_product_list(obj){

		productlist = obj.rawData;
		set_pagination(obj);
	}

	function check_cache(){

		if( productlist.total ){


		//return false;
	}

}

function remove_tenderline(obj){
	jQuery(obj).parent().remove();
	calculateTotal();
}
function remove_panel(obj){

	var log, clicked = jQuery(obj);
	
	if(clicked.data('type') === 'line'){
		//Removed Sale Line: (Bumper Pack Of Chews 500g) qty (8) @ (45.0) imei (N/A)
		var product = clicked.parent().find('.product-title').html(),
			qty = clicked.parent().find('.item-count').html(),
			price = clicked.parent().find('.sell-price').html();
		log = "Removed Sale Line: ("+product+") qty ("+qty+") @ ("+ parseFloat(price).toFixed(2)+") imei (N/A)";

	}else if(clicked.data('type') === 'basket'){
		log = "Removed basket";
	}

	clicked.parent().parent().parent().remove();
	calculateTotal();
	
	if(log){	
		log_action(log);
	}
}

jQuery('body').on('click', '#tender-sale', function(){

	// build summary

	var types = jQuery('.saletype'),
	holder = jQuery('#tender-summary'),
	subtotal = jQuery('#running-total').html();

	holder.empty();
	types.each(function(k,v){
		var sale = jQuery(v),
		color = sale.data('color'),
		type = sale.data('type'),
		title = sale.find('h4').html();


		holder.append('<div class="panel panel-default"><div class="panel-body" style="background-color: #'+color+';">'+title+'</div></div>');

	});
	
	holder.append('<h3 class="panel panel-default"><div class="panel-body panel-info"><span class="pull-right">R'+subtotal+'</span>Sub Total</div></h3>');


	/// add agents
	var agenttmpl = Handlebars.compile( jQuery('#user-selector-tmpl').html() ),
		userlist = JSON.parse( sessionStorage['users'] ),
		selector = jQuery('#user-selector');

	selector.html( agenttmpl(userlist) );

	jQuery('#till-panel').fadeOut(100, function(){
		jQuery('#tender-panel').slideDown(100);	
	});

	calculateTotal();
	
});
jQuery('body').on('click', '#back-tender', function(){
	jQuery('#tender-panel').slideUp(100, function(){
		jQuery('#till-panel').fadeIn(100);	
	});
});

jQuery('body').on('click', '.basket-item', function(){
	var item = jQuery(this),
	title = item.find('.product-title').html(),
	sell = item.find('.sell-price'),
	qty = item.find('.item-count'),
	cursell = sell.html(),
	curqty = qty.html(),
	panel = jQuery('#selected-item-panel');

	panel.html('<div class="panel-heading"><h3 class="panel-title text-center">'+title+'</h3></div><br>');
	panel.append( '<button id="qty-set" class="btn btn-info btn-block" style="border-radius: 0px;margin-bottom: 5px;">Change Qty</button><div class="set-qty panel-body input-group" style="display:none; width: 80px; margin: 0px auto 5px;"><input id="new-qty" value="'+qty.html()+'" type="text" class="form-control text-center"></div>' );
	if(jQuery('#dashboard-nav').hasClass('product-rights')){
		panel.append( '<button id="sell-set" class="btn btn-info btn-block has-product-rights" style="border-radius: 0px;margin-bottom: 5px;">Change Sell</button><div class="set-sell panel-body input-group" style="display:none; width: 100px; margin: 0px auto 5px;"><input id="new-sell" value="'+sell.html()+'" type="text" class="form-control text-right"></div>' );
	}
	jQuery('#new-qty').on('keyup', function(){
		qty.html( parseFloat(this.value) );
		calculateTotal();
	});
	jQuery('#new-sell').on('keyup', function(){
		sell.html( parseFloat(this.value) );
		calculateTotal();
	});
	jQuery('#new-qty').on('blur', function(){
		jQuery('.set-qty').slideUp(100);
		if(qty.html() !== curqty){
			//Changed (sell) on (Test Item 1 Non Serialized) from (100.0) to (0)
			log_action('Changed (qty) ('+item.find('.product-title').html()+') from ' + curqty + ' to ' + qty.html());
		}

	});
	jQuery('#new-sell').on('blur', function(){
		jQuery('.set-sell').slideUp(100);
		if(sell.html() !== cursell){
			//Changed (sell) on (Test Item 1 Non Serialized) from (100.0) to (0)
			log_action('Changed (sell) ('+item.find('.product-title').html()+') from ' + cursell + ' to ' + sell.html());
		}
	});
	jQuery('body').on('click', '#qty-set', function(){

		var keypad = jQuery('#keypad-tmpl').html();

		jQuery('.set-sell').slideUp(100);
		//jQuery('.set-qty').slideDown(100).html(keypad);
		jQuery('.set-qty').slideDown(100);
		jQuery('#new-qty').focus().select();

	});
	jQuery('body').on('click', '#sell-set', function(){
		jQuery('.set-qty').slideUp(100);	
		jQuery('.set-sell').slideDown(100);	
		jQuery('#new-sell').focus().select();	
	});

	baldrickTriggers();

});


jQuery('.well-till').on('click', function(){
	jQuery('#selected-item-panel').empty();
});


jQuery('#add-tender-button').on('click', function(){
	jQuery(this).slideUp(100);
	jQuery('#tender-type-selector').slideDown(100);
});
jQuery('#cancel-tender-input').on('click', function(){
	jQuery('#tender-type-selector').slideUp(100);
	jQuery('#tender-value-selector').slideUp(100);
	jQuery('#add-tender-button').slideDown(100);
});
jQuery('.tender-select').on('click', function(){
	var clicked = jQuery(this);

	jQuery('#capture-tender').attr('data-type', clicked.attr('data-type') );

	jQuery('#tender-type-selector').slideUp(100);	
	jQuery('#tender-value-selector').slideDown(100);	
	jQuery('#add-tender-input').focus().select();
});

jQuery('#add-tender-input').on('keyup', function(e){
	if(e.which === 13){
		$('#capture-tender').trigger('click');
	}else if(e.which === 27){
		$('#cancel-tender-input').trigger('click');
	}
});

jQuery('.tender-select').on('click', function(){
	var tendetemp = (tenderrequired.toFixed(2) >= 0 ? tenderrequired.toFixed(2) : '0.00' );
	jQuery('#tender-type-selector').slideUp(100);	
	jQuery('#tender-value-selector').slideDown(100);	
	jQuery('#add-tender-input').val(tendetemp).focus().select();
	console.log(tendetemp);
});

jQuery('#capture-tender').on('click', function(){
	var clicked = jQuery(this),
	type = clicked.attr('data-type');
	list = jQuery('#tender-list-panel'),
	input = jQuery('#add-tender-input'),
	tender = parseFloat(input.val()),
	acc = input.data('guid') ? " data-guid=\""+input.data('guid')+"\" " : "" ;


	types = {
		'dcard' 	: 'Debit Card',
		'ccard'	: 'Credit Card',
		'cash'	: 'Cash',
		'acc': 'Customer Account'
	};

	if(tender != tenderrequired){
		log_action('Changed Tender ('+types[type]+') from ' + tenderrequired + ' to ' + tender);
	}


	list.append('<div style="color: #333333;" class="breadcrumb"><button type="button" class="btn btn-danger btn-xs trigger pull-right" data-callback="remove_tenderline" style="padding: 1px 5px 0px; margin-left: 10px;"><i class="glyphicon glyphicon-remove-circle"></i></button> <span class="pull-right">R<span class="tender-values" data-type="'+type+'" data-name="'+ types[type] +'" '+acc+'>'+ tender.toFixed(2) +'</span></span>'+ types[type] +'</div>');

	jQuery('#tender-type-selector').slideUp(100);
	jQuery('#tender-value-selector').slideUp(100);
	jQuery('#add-tender-button').slideDown(100);

	input.val('0.00');

	calculateTotal();
});

function baldrickTriggers(){
	jQuery('.trigger').baldrick({
		helper	:	{
			event	:	function(obj){
				var $trigger = $(obj),
				call = $trigger.data('call');

				if(call){
					$trigger.data('call', call.replace("{{channel}}", jQuery.cookie('channel') ));
				}							
				if($trigger.data('call')){
					$trigger.attr('data-request', 'http://api.humble.co.za/1.1/'+jQuery.cookie('token')+'/'+$trigger.data('call'));
				}
			}
		}
	});
}


function buildDroppers(){
	jQuery( ".draggable" ).draggable({
		appendTo: "body",
		helper: "clone"
	});

	jQuery( ".basket-case" ).sortable({
		connectWith: ".basket-case",
		placeholder: "till-basket-sort-helper",
		update: function(event , ui){

			var parent = ui.item.parent();

			if(ui.item.hasClass('subdraggable') && $(this).hasClass('basket-merge') ){
				var dropped = $(this);
				ui.item.find('.basket-merge').children().appendTo( parent );
				ui.item.remove();
			}

			setActiveBasket( parent.parent().parent().parent() );

			baldrickTriggers();
			buildDroppers();
		}
	});

	jQuery( ".basket-merge" ).sortable({
		connectWith: ".basket-merge",
		helper: "clone",
	});

	jQuery( ".sbasket-case" ).droppable({
		greedy: true,
		drop: function( event, ui ) {


			var basket 	= jQuery( jQuery(ui.draggable.data('type')).html() ),
			id		= 'basket_' + jQuery('.subdraggable').length + 1;

			basket.attr('id', id).find('.add-product.trigger').attr('data-reference', id);
			basket.addClass('subdraggable').appendTo( jQuery(event.target) );

			baldrickTriggers();
			buildDroppers();
		}
	});
	/*jQuery( ".basket-merge" ).droppable({
		greedy: true,
		helper: 'clone',
		drop: function( event, ui ) {
			if(ui.draggable.hasClass('basket-item-wrap')){
				return false;
			}
			ui.draggable.find('.basket-merge').children().appendTo($(this));
			ui.draggable.remove();

			//$( $(ui.draggable.data('type')).html() ).addClass('col-md-6').addClass('col-lg-6').appendTo( $(this).find('.basket-case').first() );
			buildDroppers();
		}
	});*/


};


function addBasketType(){


	var baskets = jQuery('.basket-template'),column;


	$('.active-basket').removeClass('active-basket');
	$('.active-item-basket').removeClass('active-item-basket');

	if(baskets.length === 1){
		var basket 	= jQuery( baskets.html() ),
		id		= 'basket_' + jQuery('.subdraggable').length + 1;			

		basket.attr('id', id).find('.add-product.trigger').attr('data-reference', id);

		if( $('#leftcolumn-till').children().length <= $('#rightcolumn-till').children().length ){
			column = $('#leftcolumn-till');
		}else{
			column = $('#rightcolumn-till');
		}

		basket.addClass('subdraggable').addClass('item-basket').addClass('active-item-basket').appendTo( column );

		baldrickTriggers();

	}else{

	}

	buildDroppers();
	//var basket 	= jQuery( jQuery(type).html() ),
	//	id		= 'basket_' + jQuery('.subdraggable').length + 1;

	//basket.attr('id', id).find('.add-product.trigger').attr('data-reference', id);
	// replace {{channels}}


	//basket.addClass('subdraggable').appendTo( jQuery(event.target) );
}

function setActiveBasket(el){

	$('.active-item-basket').removeClass('active-item-basket');
	$('.active-basket').removeClass('active-basket');

	el.addClass('active-item-basket').find('.add-product').addClass('active-basket');

}

jQuery('body').on('click', '.item-basket', function(){

	setActiveBasket( jQuery(this) );

});

// cash drawer
// check setting
jQuery('#till-panel').on('click', '.pop-draw', function(){
	if(printerid){

		send = {
			printer: printerid,
			slip: "\u001bp07Q"
		};
		///
		
		jQuery.post('http://localhost:9200/', send, function(res){
			//console.log(res);
		}).fail(function(){
			alert('Printer is not Online');
		});
	};
});

jQuery('#till-panel').on('click', '.last-rec', function(){


	if(jQuery.cookie('lastsale')){
		
		sale = localStorage.getItem('lastsale');

		if(sale){

			var send = JSON.parse( sale );

			send.printer = printerid;
			///
			
			jQuery.post('http://localhost:9200/', send, function(res){
				console.log(res);
			}).fail(function(){
				alert('Printer is not Online');
			});
		};
	}


});


jQuery('#complete-tender').on('click', function(){
	// Complete Sale

	// Gather All Lines
	var sale	= {
		//"test"		: 	"true",
		//"imeis"		:	{},
		"header"	:	{
			"guid"			:	"",
			"incl"			:	0,
			"vat"			:	0,
			"excl"			:	0,
			"devicename"	: "webtill",
			"agent"			: jQuery('#sale-agent').data('agentguid')

		},
		"lines"		:	[],
		"tenders"	:	[],
		"small_print": small_print
	},
	hguid 		= gen_guid(),
	tenders		= jQuery('.tender-values'),
	lines 		= jQuery('.basket-item'),
	name		= jQuery('#customer-name').val(),
	email		= jQuery('#customer-email').val(),
	cashuplog;

	if(jQuery.cookie('cashup')){
		cashuplog = JSON.parse(jQuery.cookie('cashup'));
	}else{
		cashuplog 	= {
			cash	:	0,
			ccard	:	0,
			dcard	:	0,
			acc		:	0
		};
	}


	lines.each(function(k,v){

		var item 	= jQuery(v),
		parent 	= item.parent().parent().parent().parent().parent(),
		qty		= parseInt(item.find('.item-count').html()),
		cost	= parseFloat( item.data('cost') ) * qty,
		sell	= parseFloat( item.find('.sell-price').html() ) * qty,
		vat		= parseFloat( item.data('vat') ),
		changeguid,
		tenderline,
		vatline = sell - (sell/ ( (100+vat)/100 ) ),
		line 	= {
			"guid"			:	hguid,
			"line"			:	k,
			"productguid"	:	item.data('guid'),
			"stockcode"		:	item.data('stockcode'),
			"qty"			:	qty,
			"serial"		:	( item.find('.product-serial').length ? item.find('.product-serial').html() : 'N/A' ),
			"cost"			:	cost,
			"sell"			:	sell,
			"vat"			:	vatline,
			"bundletype"	:	"0",
			"tariffguid"	:	( item.data('tariffguid') ? item.data('tariffguid') : 'N/A' ),
			"tariffline"	:	"0",
			"rebate"		:	"0.00",
			"email"			:	(email.length ? email : 'N/A' ),
			"saletype"		:	parent.data('guid')
		};
			// add line total to header
			sale.header.incl += sell;
			sale.header.vat  += vatline;
			sale.header.excl += sell-vatline;


			sale.lines.push(line);
		});

	// add header
	sale.header.guid = hguid;


	// add tender values to header
	tenders.each(function(k,v){
		var tender	= jQuery(v),
		type	= tender.data('type'),
		name	= tender.data('name'),
		value	=  parseFloat( tender.html() );

		if(typeof sale.header[type] === 'undefined'){
			sale.header[type] = 0;
		}

		// Tender
		tenderline = {
			'saleguid'	: hguid,
			'cashierguid': jQuery.cookie('user_guid'),
			'category'	: type,
			'title'		: name,
			'amount'	: value
		};
		if(type === 'acc' && tender.data('guid')){
			tenderline.communityguid = tender.data('guid');
			// capture tender change guid
			changeguid = tender.data('guid');
		}
		
		sale.tenders.push(tenderline);

		sale.header[type] += value;

		cashuplog[type] += value;

	});

	// Add tendered total 
	sale.header.tender_total = parseFloat( jQuery('#tendered-total').html() );

	sale.header.tender_change = parseFloat( jQuery('#tendered-change').html() );
	if(sale.header.tender_change > 0){
		// add tender change line
		tenderline = {
			'saleguid'	: hguid,
			'cashierguid': jQuery.cookie('user_guid'),
			'category'	: 'change',
			'title'		: 'Change',
			'amount'	: -sale.header.tender_change
		};
		if(typeof changeguid !== 'undefined'){
			tenderline.communityguid = changeguid;
		}
		sale.tenders.push(tenderline);
	}

	jQuery('#main-panel').addClass('loading');

	// 
	jQuery.post('http://api.humble.co.za/1.1/'+jQuery.cookie('token')+'/sale', sale, function(result){
		if(result.message){

			jQuery('#main-panel').removeClass('loading');

			// clear out stuff!
			jQuery('#tender-summary').empty();
			jQuery('#tender-list-panel').empty();
			jQuery('.basket-case').empty();

			if(!jQuery('.sale-type-tray').length){
				addBasketType();
			}

			// show thanks screen
			jQuery('#tender-panel').slideUp(100);
			jQuery('#till-panel').slideUp(100);
			jQuery('#tender-complete-panel').slideDown(100);
			// tst 
			//jQuery.post('http://127.0.0.1:631/ipp/EPSON_TM_T20', result.slip, function(res){console.log(res)});
			
			
				//jQuery.get( "http://api.humble.co.za/1.1/receipt/" + result.guid, function( data ) {


			var send = {
				"printer"	:	0,
				"slip"		:	result.slip
			};

			localStorage.setItem('lastsale', JSON.stringify(send));

			if(printerid){
					send.printer = printerid;
					// GET SERVER - needed
					if(use_draw === 1){
						if(result.pop){
							send.slip += result.pop;
						}
					}

					jQuery.post('http://localhost:9200/', send, function(res){
						console.log(res);
					}).fail(function(){
						console.log('Printer is not Online');
					});

				//});
			}



			jQuery.cookie('lastsale', result.guid, {expires: 365, path: '/', domain: 'humble.co.za'});


				if(email.length && name.length){
				// send invoice

				var inv = {
					"recipient_email"		:	email,
					"recipient_name"		:	name,
					"message"				:	"Your invoice is attached.",
					"invoice"				:	result.guid
				};

				jQuery.post('http://api.humble.co.za/1.1/'+jQuery.cookie('token')+'/notification', inv, function(result){

				});

			}

			calculateTotal(true);
			jQuery.cookie('cashup', JSON.stringify(cashuplog), { expires: 365 });

			if(auto_lock === 1){
				jQuery('#tender-complete-panel').slideUp(100);
				jQuery('#lock-till').trigger('click');
			}
			// push a product sync.
			product_sync();

			if(print_a4_receipts === 1){
				window.location = "http://api.humble.co.za/1.1/service/invoice-pdf/" + result.guid;
			}

			/*
				var receipt =  window.open('','receiptWindow','width=100,height=100');
				var html = '<html><head><title>Print Your receipt</title></head><body><div id="receipt" style="height:100px;">' + $('<div />').append( "This is a receipt. deal with it." ).html() + '</div></body></html>';
				receipt.document.open();
				receipt.document.write(html);
				receipt.document.close();
				receipt.print();
				return false;			
				*/

				//{ expires: 7 }

				// set cashup ready

			}else{
				//alert('OOPS!');
			}
		})

calculateTotal();

	//strlog(hguid);


})

jQuery('#till-new-sale').on('click', function(){

	jQuery('#tender-complete-panel').slideUp(100);
	jQuery('#tender-panel').slideUp(100);
	jQuery('#till-panel').slideDown(100);
	jQuery('.complete-change-line').html('');
});

function strlog(str){

	jQuery('#tender-console').prepend('<p>'+str+'</p>');

}


function setagent(el){

	var saleagent = jQuery('#sale-agent'),
		selected = jQuery(el),
		label = jQuery('.active-agent');

	saleagent.attr('data-agentguid', selected.data('guid'));
	label.html(selected.html());


}



function check_printerStatus(el){

	
	if( typeof el.request === 'undefined'){
		
		if(!printerid){
			return false;
		}

		jQuery(el).data('status', printerid);
		return true;

	}else{

		var online = jQuery('#printer-online'),
			reprint = jQuery('#printer-online-reprint'),
			offline = jQuery('#printer-offline'),
			none = jQuery('#printer-notset');

		if(el.textStatus === "error"){

			reprint.hide();
			online.hide();
			offline.show();
			none.hide();

			return;
		}

		if(el.data.status){

			if(localStorage.getItem('lastsale')){
				online.hide();
				reprint.show();
			}else{
				online.show();
				reprint.hide();
			}
			offline.hide();
			none.hide();
		}else{
			online.hide();
			reprint.hide();
			offline.show();
			none.hide();
		}
		

	}
}

	jQuery('body').on('keyup', '.product_searcher', function(){


	});

	jQuery('body').on('keyup', '.customer-list-search', function(){

		var wrap = jQuery(this),
			str = wrap.val(),
			panel = wrap.parent().parent(),
			list = panel.find('.list-group'),
			items = panel.find('.list-group-item').not('.no-item-found'),
			nada = jQuery('<div class="list-group-item no-item-found">No results for "'+str+'"</div>'),
			count = 0;

			panel.find('.no-item-found').remove();
			
			items.hide();
			if(!str.length){
				
				//items.show();
				jQuery('.list-group-item.active').trigger('click');
				count = items.length;
			}else{
				
				items.each(function(k,v){
					
					var item = jQuery(v),
						data = item.data('title'),
						strpos = null;
						
						if(!data){
							data = item.html();
						}
					// do search
					strpos = data.toString().toLowerCase().indexOf(str.toLowerCase());

					if( strpos > -1){
						item.show();
						//item.html( data.substr(0, strpos) + '<span class="text-info">' + data.substr(strpos, str.length) + '</span>' + data.substr(strpos + str.length)  );
						count++;
					}

				});
				
			}
			if(count === 0){
				nada.appendTo(list);
				
			}

	});

function log_action(message){

	jQuery.post('http://api.humble.co.za/1.1/'+jQuery.cookie('token')+'/audit', {note: message}, function(res){
		console.log(res);
	});
}

</script>














