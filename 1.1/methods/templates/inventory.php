<?php
/*

Caldoza Engine ------------------------

File	:	templates/products-list.php
Created	: 	2013-12-04

*/

?>
<div class="col-md-1 col-lrg-1 trigger" data-call="products" data-cache-session="products" data-event="none" data-autoload="true" data-type="json" data-load-element="#app-window">
	<button class="btn btn-lg btn-info btn-block" id="add-order-button">Order</button>
	<button class="btn btn-lg btn-danger btn-block" id="add-grv-button">GRV</button>
	<button class="btn btn-lg btn-success btn-block multi-site-required" id="add-transfer-button" style="display:none;">Transfer</button>
	<button class="btn btn-lg btn-warning btn-block" id="add-adjustment-button">Adjust</button>
	<button class="btn btn-lg btn-inverse btn-block" id="add-count-button">Count</button>
</div>
<div class="col-md-5 col-lrg-5 trigger" data-call="ean" data-cache-session="eans" data-event="none" data-autoload="true" data-type="json" data-load-element="#app-window">
	<div id="left-panel" style="max-height: 500px; overflow: auto;">
		<div class="list-group" id="inventory-list">
		</div>
	</div>
</div>
<div class="col-md-6 col-lrg-6">
	<div id="inv-right-panel">
	</div>
</div>
<input type="hidden" id="scanevent" class="trigger" data-event="click" data-call="ean" data-cache-session="eans" data-active-class="currentbutton" data-callback="do_scancode">
<input type="hidden" id="scancountevent" class="trigger" data-event="click" data-call="ean" data-cache-session="eans" data-active-class="currentbutton" data-callback="do_scancode_count">
<script type="text/html" id="order-tmpl">
<a href="#" class="list-group-item">
	<h4 class="list-group-item-heading">Order <span class="from-line"></span></h4>
	<p class="list-group-item-text date-line"></p>
</a>
</script>
<script type="text/html" id="adjustment-tmpl">
<a href="#" class="list-group-item">
	<h4 class="list-group-item-heading">Adjust</h4>
	<p class="list-group-item-text date-line"></p>
</a>
</script>
<script type="text/html" id="grv-tmpl">
<a href="#" class="list-group-item">
	<h4 class="list-group-item-heading"><span class="grv-line">Goods Received Voucher</span> <span class="from-line"></span></h4>
	<p class="list-group-item-text date-line"></p>
</a>
</script>
<script type="text/html" id="transfer-tmpl">
<a href="#" class="list-group-item">
	<h4 class="list-group-item-heading">Transfer</span> <span class="from-line"></span></h4>
	<p class="list-group-item-text date-line"></p>
</a>
</script>
<script type="text/html" id="count-tmpl">
<a href="#" class="list-group-item trigger" data-call="stocktake" data-group="counter" data-callback="do_stocktack_start" data-event="none" data-autoload="true" data-template="#count-form-tmpl">
	<h4 class="list-group-item-heading">Inventory Count</span></h4>
	<p class="list-group-item-text detail-line"></p>
</a>
</script>
<script type="text/html" id="order-form-tmpl">
<div class="panel-form">
	<div class="list-group main-panel-form">
		<a href="#" class="list-group-item supplier-line trigger" data-call="suppliers" data-cache-session="suppliers" data-group="supplierline" data-active-class="currentline" data-modal="suppliers_modal" data-modal-title="Select Supplier" data-template="#suppliers-tmpl">
			<span class="pull-right text-muted"></span>
			Supplier
		</a>
		<div class="list-group-item reference-line" style="color:#555555;">
			<span class="pull-right" style="margin: -4px 0px 0px; width: 250px;"><input type="text" class="text-right form-control input-sm reference-number"></span>
			Reference
		</div>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted date-line"></span>
			Date
		</a>
		<a href="#" class="items-line list-group-item trigger" data-callback="slide_items">
			<span class="pull-right text-muted glyphicon glyphicon-chevron-right"></span>
			Items
		</a>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted">R<span class="running-total">0.00</span></span>
			Total Excl VAT
		</a>
		
		<div class="btn-group btn-group-justified process-buttons">
				<a class="btn btn-lg btn-default trigger" data-before="do_confirm" data-message="Are you sure you want to delete this?" data-callback="delete_pack" style="border-radius: 0px 0px 0px 4px;">Delete</a>
				<a class="btn btn-lg btn-primary order-process trigger" data-message="Are you sure you want to process this?" data-before="build_order_process" style="border-radius: 0px 0px 4px;">Process</a>			
		</div>

	</div>
	<div class="panel panel-primary" style="display:none;">
		<div class="panel-heading">
			<span class="trigger" data-callback="close_items" style="cursor: pointer;"><span class="glyphicon glyphicon-chevron-left"></span> Items</span>			
			<button class="pull-right btn btn-default btn-humble trigger" data-call="products" data-cache-session="products" data-active-class="currentbutton" data-modal="products_modal" data-modal-title="Select Item" data-template="#products-tmpl" style="margin: -4px 0px 0px; color: #fff; border-color: #fff;"><span class="glyphicon glyphicon-plus"></span></button>
		</div>
		<div class="list-group panel-items-list">
		</div>
	</div>
</div>
</script>
<script type="text/html" id="grv-form-tmpl">
<div class="panel-form">
	<div class="list-group main-panel-form">
		<a href="#" class="list-group-item supplier-line trigger" data-call="suppliers" data-cache-session="suppliers" data-group="supplierline" data-active-class="currentline" data-modal="suppliers_modal" data-modal-title="Select Supplier" data-template="#suppliers-tmpl">
			<span class="pull-right text-muted"></span>
			Supplier
		</a>
		<div class="list-group-item reference-line" style="color:#555555;">
			<span class="pull-right" style="margin: -4px 0px 0px; width: 250px;"><input type="text" class="text-right form-control input-sm reference-number"></span>
			Reference
		</div>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted date-line"></span>
			Date
		</a>
		<div class="list-group-item dtrigger" data-callback="slide_items">
			
			<div class="btn-group pull-right">
					<button class="btn btn-xs btn-primary active_movetype trigger" data-type="1" data-callback="set_void" style="padding: 5px 10px; margin: -4px 0px 0px;">Goods Received</button>
					<button class="btn btn-xs btn-default trigger" data-type="2" data-callback="set_void" style="padding: 5px 10px; margin: -4px 0px 0px;">Goods Returned</button>
			</div>
			Stock Direction
		</div>
		<a href="#" class="items-line list-group-item trigger" data-callback="slide_items">
			<span class="pull-right text-muted glyphicon glyphicon-chevron-right"></span>
			Items
		</a>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted"><span class="running-total">0.00</span></span>
			Total Excl VAT
		</a>
		
		<div class="btn-group btn-group-justified process-buttons">			
				<a class="btn btn-lg btn-default trigger" data-before="do_confirm" data-message="Are you sure you want to delete this?" data-callback="delete_pack" style="border-radius: 0px 0px 0px 4px;">Delete</a>
				<a class="btn btn-lg btn-primary trigger" data-message="Are you sure you want to process this?" data-before="build_grv_process" style="border-radius: 0px 0px 4px;">Process</a>			
		</div>

	</div>
	<div class="panel panel-primary" style="display:none;">
		<div class="panel-heading">
			<span class="trigger" data-callback="close_items" style="cursor: pointer;"><span class="glyphicon glyphicon-chevron-left"></span> Items</span>
			<button class="pull-right btn btn-default btn-humble trigger" data-complete="focus_search" data-call="products" data-cache-session="products" data-active-class="currentbutton" data-modal="products_modal" data-modal-title="Select Item" data-template="#products-tmpl" style="margin: -4px 0px 0px; color: #fff; border-color: #fff;"><span class="glyphicon glyphicon-plus"></span></button>
		</div>
		<div class="list-group panel-items-list">
		</div>
	</div>
</div>
</script>
<script type="text/html" id="transfer-form-tmpl">
<div class="panel-form">
	<div class="list-group main-panel-form">
		<a href="#" class="list-group-item supplier-line trigger" data-call="sites" data-cache-session="sites" data-group="supplierline" data-active-class="currentline" data-modal="sites_modal" data-modal-title="Select Store" data-template="#sites-tmpl">
			<span class="pull-right text-muted"></span>
			Store
		</a>
		<div class="list-group-item reference-line" style="color:#555555;">
			<span class="pull-right" style="margin: -4px 0px 0px; width: 250px;"><input type="text" class="text-right form-control input-sm reference-number"></span>
			Reference
		</div>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted date-line"></span>
			Date
		</a>
		<div class="list-group-item dtrigger" data-callback="slide_items">
			
			<div class="btn-group pull-right">
					<button class="btn btn-xs btn-primary active_movetype trigger" data-type="3" data-callback="set_void" style="padding: 5px 10px; margin: -4px 0px 0px;">Transfer In</button>
					<button class="btn btn-xs btn-default trigger" data-type="4" data-callback="set_void" style="padding: 5px 10px; margin: -4px 0px 0px;">Transfer Out</button>
			</div>
			Stock Direction
		</div>
		<a href="#" class="items-line list-group-item trigger" data-callback="slide_items">
			<span class="pull-right text-muted glyphicon glyphicon-chevron-right"></span>
			Items
		</a>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted"><span class="running-total">0.00</span></span>
			Total Excl VAT
		</a>
		
		<div class="btn-group btn-group-justified process-buttons">			
				<a class="btn btn-lg btn-default trigger" data-before="do_confirm" data-message="Are you sure you want to delete this?" data-callback="delete_pack" style="border-radius: 0px 0px 0px 4px;">Delete</a>
				<a class="btn btn-lg btn-primary trigger" data-message="Are you sure you want to process this?" data-before="build_transfer_process" style="border-radius: 0px 0px 4px;">Process</a>			
		</div>

	</div>
	<div class="panel panel-primary" style="display:none;">
		<div class="panel-heading">
			<span class="trigger" data-callback="close_items" style="cursor: pointer;"><span class="glyphicon glyphicon-chevron-left"></span> Items</span>
			<button class="pull-right btn btn-default btn-humble trigger" data-complete="focus_search" data-call="products" data-cache-session="products" data-active-class="currentbutton" data-modal="products_modal" data-modal-title="Select Item" data-template="#products-tmpl" style="margin: -4px 0px 0px; color: #fff; border-color: #fff;"><span class="glyphicon glyphicon-plus"></span></button>
		</div>
		<div class="list-group panel-items-list">
		</div>
	</div>
</div>
</script>
<script type="text/html" id="adjustment-form-tmpl">
<div class="panel-form">
	<div class="list-group main-panel-form">
		<div class="list-group-item reference-line" style="color:#555555;">
			<span class="pull-right" style="margin: -4px 0px 0px; width: 250px;"><input type="text" class="text-right form-control input-sm reference-number"></span>
			Reference
		</div>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted date-line"></span>
			Date
		</a>
		<div class="list-group-item dtrigger" data-callback="slide_items">
			
			<div class="btn-group pull-right">
					<button class="btn btn-xs btn-primary active_movetype trigger" data-type="9" data-callback="set_void" style="padding: 5px 10px; margin: -4px 0px 0px;">Adjust In</button>
					<button class="btn btn-xs btn-default trigger" data-type="10" data-callback="set_void" style="padding: 5px 10px; margin: -4px 0px 0px;">Adjust Out</button>
			</div>
			Stock Direction
		</div>
		<a href="#" class="items-line list-group-item trigger" data-callback="slide_items">
			<span class="pull-right text-muted glyphicon glyphicon-chevron-right"></span>
			Items
		</a>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted"><span class="running-total">0.00</span></span>
			Total Excl VAT
		</a>
		
		<div class="btn-group btn-group-justified process-buttons">			
				<a class="btn btn-lg btn-default trigger" data-before="do_confirm" data-message="Are you sure you want to delete this?" data-callback="delete_pack" style="border-radius: 0px 0px 0px 4px;">Delete</a>
				<a class="btn btn-lg btn-primary trigger" data-message="Are you sure you want to process this?" data-before="build_adjustment_process" style="border-radius: 0px 0px 4px;">Process</a>			
		</div>

	</div>
	<div class="panel panel-primary" style="display:none;">
		<div class="panel-heading">
			<span class="trigger" data-callback="close_items" style="cursor: pointer;"><span class="glyphicon glyphicon-chevron-left"></span> Items</span>
			<button class="pull-right btn btn-default btn-humble trigger" data-complete="focus_search" data-call="products" data-cache-session="products" data-active-class="currentbutton" data-modal="products_modal" data-modal-title="Select Item" data-template="#products-tmpl" style="margin: -4px 0px 0px; color: #fff; border-color: #fff;"><span class="glyphicon glyphicon-plus"></span></button>
		</div>
		<div class="list-group panel-items-list">
		</div>
	</div>
</div>
</script>
<script type="text/html" id="count-form-tmpl">
	<div class="list-group main-panel-form">
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted">{{percentage_completed}}</span>
			Percentage Completed
		</a>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted">{{inventory_cost_difference}}</span>
			Inventory Cost Difference
		</a>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted">{{items_to_count}}</span>
			Items to Count
		</a>
		<a href="#" class="list-group-item">
			<span class="pull-right text-muted">{{items_with_differences}}</span>
			Items with Differences
		</a>
		<a href="#" class="items-line list-group-item trigger" data-callback="slide_items">
			<span class="pull-right text-muted glyphicon glyphicon-chevron-right"></span>
			Items
		</a>
		<div class="list-group-item" style="padding: 30px 10px;">
			<a href="http://api.humble.co.za/1.1/<?php echo $params['token']; ?>/pdf-generate/difference-report" class="btn btn-default btn-lg btn-block">Differences Report</a>
		</div>
		<div class="btn-group btn-group-justified process-buttons">			
				<a class="btn btn-lg btn-default trigger" id="delete_stocktake" data-before="do_confirm" data-message="Are you sure you want to delete this?" data-callback="delete_count" style="border-radius: 0px 0px 0px 4px;">Delete</a>
				<a class="btn btn-lg btn-primary trigger" data-takeguid="{{guid}}" data-before="build_count_process" style="border-radius: 0px 0px 4px;">Process</a>			
		</div>

	</div>
	<div class="panel panel-primary stock-items-panel" style="display:none; overflow:visible;">
		<span class="trigger" data-autoload="true" data-group="auto-settings-nav" data-call="products" data-cache-session="products" data-template="#inv-products-list-tmpl" data-target=".inv-product-search"></span>
		<div class="panel-heading">
			<span class="trigger" data-callback="close_items" style="cursor: pointer;"><span class="glyphicon glyphicon-chevron-left"></span> Items</span>
		</div>

		<div class="input-group panel-body">
		<input class="form-control count-item-search" id="take-product-search">
		<div class="input-group-btn">
			<button type="button" class="take-search-btn item-search-toggle btn btn-primary" data-type="take">Take Items</button>
			<button type="button" class="item-search-toggle btn btn-default" data-type="inv">Inventory Items</button>
		</div>
		</div>
		<div class="list-group panel-items-list inv-product-search" style="min-height: 250px; display:none;">

		</div>
		<div class="main-take-list list-group list-sorted panel-items-list" style="min-height: 250px;">
		{{#each items}}
			<div class="list-group-item list-item count-item-line search-item-line cat_{{productcat}}" data-guid="{{productguid}}" data-descr="{{productdescr}}">
				<span class="pull-right"><span class="current-state-line" data-counted="{{counted}}" data-onhand="{{onhand}}"></span> <a id="stocktake-line-{{productguid}}" href="#setqty" data-productguid="{{productguid}}" data-takeguid="{{../guid}}" class="glyphicon glyphicon-info-sign set-qty-item" style="margin-left: 10px; text-decoration: none;"></a></span>
				{{productdescr}}
			</div>
		{{/each}}
		</div>
		<div class="panel-footer">
			<div class="btn-group dropup">
				<button type="button" class="btn btn-default dropdown-toggle trigger" data-callback="cat_sorter" data-call="categories" data-group="dropups_inv" data-cache-session="categories" data-target="#stocktake-cat-select" data-template="#stocktake-cat-tmpl" data-active-class="open" data-active-element="_parent">
				<span class="selected-cat">All Categories</span>
				</button>
				<ul class="dropdown-menu list-sorted" role="menu" id="stocktake-cat-select">
				</ul>
			</div>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-default dropdown-toggle trigger" data-call="categories" data-group="dropups_inv" data-cache-session="categories" data-target="#stocktake-cat-select" data-template="#stocktake-cat-tmpl" data-active-class="open" data-active-element="_parent">
				<span class="selected-state">All States</span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li class="trigger" data-autoload="true" data-group="dropups_state" data-callback="switch_count_state" data-state="all"><a href="#">All States</a></li>
					<li class="divider"></li>
					<li class="trigger" data-group="dropups_state" data-callback="switch_count_state" data-state="correct"><a href="#">Correctly Counted</a></li>
					<li class="trigger" data-group="dropups_state" data-callback="switch_count_state" data-state="different"><a href="#">With Differences</a></li>
				</ul>
			</div>

			<div class="btn-group pull-right">
					<button class="scan-direction btn btn-primary trigger" data-scantype="p" data-callback="set_count_direction">Positive Scan</button>
					<button class="scan-direction btn btn-default trigger" data-scantype="n" data-callback="set_count_direction">Negative Scan</button>
			</div>			

		</div>
	</div>
</script>
<script type="text/html" id="stocktake-cat-tmpl">
	<li class="trigger" data-group="dropups" data-callback="switch_count_category" data-cat="all"><a href="#">All Categories</a></li>
	<li class="divider" >0a</li>
	{{#each categories}}
		<li class="trigger list-item" data-group="dropups" data-callback="switch_count_category" data-cat="cat_{{cat}}"><a href="#">{{category}}</a></li>
	{{/each}}
</script>
<script type="text/html" id="suppliers-tmpl">
	<div class="list-group">
		{{#each suppliers}}
		<a href="#" class="list-group-item trigger" data-guid="{{guid}}" data-callback="set_supplier" data-dismiss="modal">{{descr}}</a>
		{{/each}}
	</div>
</script>
<script type="text/html" id="sites-tmpl">
	<div class="list-group">
		{{#each sites}}
		<a href="#" class="list-group-item trigger hide_{{guid}}" data-guid="{{guid}}" data-callback="set_supplier" data-dismiss="modal">{{site_name}}</a>
		{{/each}}
	</div>
</script>
<script type="text/html" id="products-tmpl">
	<div class="has-feedback trigger" data-event="none" data-callback="focus_search" data-autoload="true">
		<input type="text" class="form-group form-control search-this trigger" data-callback="search_this" data-event="keyup">
		<span class="glyphicon glyphicon-search form-control-feedback"></span>
	</div>
	<div class="list-group">
		{{#each products}}
		<a href="#" class="list-group-item trigger live{{live}}" data-guid="{{guid}}" data-vat="{{vat}}" data-cost="{{cost}}" data-descr="{{descr}}" data-stockcode="{{stockcode}}" data-callback="add_product" data-dismiss="modal">
			<span class="pull-right text-muted">Cost: {{cost}}</span>
			{{descr}}
			<small class="list-group-item-text text-muted" style="display:block;">{{stockcode}}</small>
		</a>
		{{/each}}
	</div>
</script>
<script type="text/html" id="item-line-tmpl">
	<a href="#" class="list-group-item stock-item-line sc-{{stockcode}}" data-guid="{{guid}}" data-vat="{{vat}}" rel="popover">
		<h4 class="list-group-item-heading">{{descr}}</h4>
		<span class="list-group-item-text" style="width: 70px; display: inline-block;">Qty: <span class="qty-line">1</span></span>
		<span class="list-group-item-text" style="width: 130px; display: inline-block;">Cost: <span class="cost-line">{{cost}}</span></span>
		<span class="list-group-item-text" style="width: 130px; display: inline-block;">Line Cost: <span class="cost-line-total">{{cost}}</span></span>

	</a>
</script>
<script type="text/html" id="item-edit-tmpl">
<div class="form-horizontal">
	<div class="form-group">
		<div class="col-sm-2"></div>
		<div class="col-sm-10">
			<button class="btn btn-xs btn-default btn-block delete-item">Delete</button>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Qty</label>
		<div class="col-sm-10">
			<input type="text" class="form-control input-qty">
		</div>
	</div>
	<div class="form-group has-product-rights">
		<label class="col-sm-2 control-label">Cost</label>
		<div class="col-sm-10">
			<input type="text" class="form-control input-cost">
		</div>
	</div>
	<div class="form-group" style="margin-bottom: 5px;">
		<div class="col-sm-2"></div>
        <div class="col-sm-10">
			<button class="btn btn-xs btn-primary btn-block close-edit">Close</button>
		</div>
	</div>
</div>
</script>

<script id="inv-products-list-tmpl" type="text/html">
	{{#each products}}
	<div class="live{{live}} inv-item-result list-group-item list-item count-item-line search-item-line cat_{{cat}}" data-guid="{{guid}}" data-descr="{{descr}}" style="display:none;">
		<span class="pull-right hide"><span class="current-state-line" data-counted="0" data-onhand="{{on_hand}}"></span> <a id="stocktake-line-{{guid}}" href="#setqty" data-productguid="{{guid}}" class="glyphicon glyphicon-info-sign set-qty-item" style="margin-left: 10px; text-decoration: none;"></a></span>
		{{descr}}
	</div>	
	{{/each}}
</script>

<script type="text/javascript">

var scancode = '', clearscancode;

if(typeof windowEvents['invScanner'] === 'undefined'){
	
	windowEvents['invScanner'] = true;	

	window.addEventListener('keydown', function(e){

		if(!jQuery('#scanevent').length || !jQuery('.icon-inv').hasClass('active')){
			return;
		}


		if(e.keyCode !== 13){
			scancode += String.fromCharCode( e.keyCode );
		}else if(e.keyCode === 13 && scancode.length){
			e.preventDefault();
			//console.log(scancode);

			/// stop if stock take panel is open!!!
			if(jQuery('.stock-items-panel').hasClass('currentpanel')){
				jQuery('#scancountevent').val(scancode).trigger('click');
			}else{
				/// standard item
				jQuery('#scanevent').val(scancode).trigger('click');

			}			
			scancode = '';
			clearTimeout(clearscancode);
		}
		if(clearscancode){
			clearTimeout(clearscancode);
		}
		clearscancode = setTimeout(function(){
			scancode = '';
		}, 250);

	});
}
	
function switch_count_state(el, e){
	e.preventDefault();
	var clicked = jQuery(el),
		parent = clicked.closest('.panel.panel-primary'),
		lines,
		wrap = clicked.closest('.btn-group.dropup');

	prod_sorter(parent);

	lines = parent.find('.count-item-line');

	jQuery('.current-set-state').removeClass('current-set-state');
	clicked.addClass('current-set-state');

	wrap.removeClass('open');
	wrap.find('button').html(clicked.text());
	
	lines.hide();
	
	

	console.log(clicked.data('state'));
	lines.each(function(k,v){

		var line = jQuery(v),
			status = line.find('.current-state-line'),
			onhand = parseInt(status.data('onhand')),
			counted = parseInt(status.data('counted')),
			type = 'all';

		if(onhand === counted){
			status.html('<span class="text-success">Counted Correctly: '+counted+'</span>');
			type = 'correct';

		}else if(onhand !== counted && counted > 0){
			if(counted > onhand){
				status.html('<span class="text-danger">Counted '+counted+', Over: ' + (counted - onhand) +'</span>' );
			}else{
				status.html('<span class="text-danger">Counted '+counted+', Short: ' + (onhand - counted) +'</span>' );
			}
			type = 'different';
		}else{
			status.html('<span class="text-warning">On Hand '+onhand+', None Counted</span>' );
		}
		
		if(clicked.data('state') === 'correct' && type == 'correct'){
			line.show();
		}
		if(clicked.data('state') === 'different' && type == 'different'){
			line.show();
		}
		if(clicked.data('state') === 'all'){
			line.show();
		}
	});
	// set by state


}

function cat_sorter(obj){

	var panel		= obj.params.trigger.parent().find('.list-sorted'),
		listkeys	= [],
		listobjs	= {},
		items		= panel.find('li.list-item');

	items.each(function(index,el){
		var item = jQuery(el);

		listkeys.push(item.text().trim());
		listobjs[item.text().trim()] = item;

	});
	
	listkeys.sort();

	for(var v in listkeys){
		listobjs[listkeys[v]].appendTo(panel);
	}

}

function prod_sorter(pn){


	var panels		= pn.find('.list-sorted');

	panels.each(function(k,v){

		var panel = jQuery(v),
			listkeys	= [],
			listobjs	= {},
			items		= panel.find('div.list-item');

			

		items.each(function(index,el){
			var item = jQuery(el);
			
			listkeys.push(item.data('descr'));
			listobjs[item.data('descr')] = item;

		});
		
		listkeys.sort();
		console.log(listobjs);
		for(var v in listkeys){
			listobjs[listkeys[v]].appendTo(panel).show();
		}
	});
}


function set_count_direction(el, e){
	var clicked = jQuery(el);

	clicked.parent().children().removeClass('btn-primary').addClass('btn-default');
	clicked.removeClass('btn-default').addClass('btn-primary');

}


function switch_count_category(el,e){
	e.preventDefault();


	var clicked = jQuery(el),
		cat = clicked.data('cat');

	jQuery('.count-item-line').addClass('hide');

	if(cat === 'all'){
		jQuery('.count-item-line').removeClass('hide');
	}else{	
		jQuery('.count-item-line.'+cat).removeClass('hide');
	}
	jQuery('.selected-cat').html(clicked.text());
	jQuery('.btn-group.dropup.open').removeClass('open');


}
	function do_confirm(el){
		if(confirm($(el).data('message'))){
			return true;
		}
		return false;
	}

	function check_scanevent(el, e){
		return true;
	}

	function do_scancode(obj){
		var barcode = obj.params.trigger.val();
		obj.params.trigger.val('');

		if(barcode.length){

			if(obj.data.total > 0){
				for( var i in obj.data.ean ){
					
					if( obj.data.ean[i].ean === barcode ){

						var req = jQuery('<span>', {
							"class"				: "trigger",
							"data-call" 		: "products",
							"data-cache-session": "products",
							"data-callback"		: "capture_scan",
							"data-guid"			: obj.data.ean[i].product_guid
						});

						req.appendTo(jQuery('body'));
						baldrickTrigger();
						req.trigger('click').remove();

					}
				}
			}

		}
	}

	// bind qty
	jQuery('body').on('click scan', '.set-qty-item', function(e){
		
		console.log(e.type);

		e.preventDefault();

		var clicked		= jQuery(this),
			line		= clicked.prev(),
			onhand		= line.data('onhand'),
			counted		= line.data('counted'),
			takeguid	= clicked.data('takeguid'),
			productguid = clicked.data('productguid'),
			imei		= "N/A",
			new_count,
			qty;

		if(e.type === 'click'){
			new_count = parseInt( prompt('Please set QTY', counted) );
		}else{
			if(jQuery('.scan-direction.btn-primary').data('scantype') === 'p'){
				new_count = parseInt( counted ) + 1;
			}else if(jQuery('.scan-direction.btn-primary').data('scantype') === 'n'){
				new_count = parseInt( counted ) - 1;
				jQuery('.scan-direction.btn-default').trigger('click');
			}
		}

		if( !isNaN(new_count)){
			// if serialized:
			if(line.data('serialized')){
				imei = prompt('Scan Serial');
				if(!imei.length){
					return;
				}
			}
			// get difference
			qty = new_count-counted;

			line.data('counted', new_count);
			//do_line_count(new_count);
			do_line_count(productguid, takeguid, qty, imei);
		}
			
	})

	function do_line_count(productguid, takeguid, qty, imei){

		// SEND

		var send = {
			"guid"			:	gen_guid(),
			"productguid"	:	productguid,
			"takeguid"		:	takeguid,
			"qty"			:	qty,
			"imei"			:	"N/A"
		};

		jQuery.post("http://api.humble.co.za/1.1/"+jQuery.cookie('token')+'/stocktake', send, function(res){
			console.log(res);
		});

		jQuery('.current-set-state').trigger('click');




	}

	function do_scancode_count(obj){
		var barcode = obj.params.trigger.val();
		obj.params.trigger.val('');

		if(barcode.length){

			if(obj.data.total > 0){
				for( var i in obj.data.ean ){
					
					if( obj.data.ean[i].ean === barcode ){

						jQuery('#stocktake-line-'+obj.data.ean[i].product_guid).trigger('scan');

					}
				}
			}

		}
	}

	function capture_scan(obj){
		var guid = obj.params.trigger.data('guid'), template, curprod;
		
		if( obj.data.total > 0 ){

			for(var i in obj.data.products){

				var currentpanel = jQuery('.currentpanel'),
					currentparent = currentpanel.parent();

				if( obj.data.products[i].guid === guid ){

					curprod = jQuery('.currentpanel .sc-'+obj.data.products[i].stockcode);

					if(curprod.length){
						var qtyline = curprod.find('.qty-line'),
							costline = curprod.find('.cost-line'),
							costtotal = curprod.find('.cost-line-total'),
							qty = parseInt(qtyline.html()) + 1,
							cost = parseFloat(costline.html())* qty ;

							qtyline.html(qty);
							costtotal.html(cost.toFixed(2));
					}else{

						template = Handlebars.compile( jQuery('#item-line-tmpl').html() )
						jQuery('.currentpanel .list-group').append( template( obj.data.products[i] ) );
					}

					calculate_Panel_Totals(currentpanel.parent());

					break;
				}
			}

		}
	}

	function calculate_Panel_Totals(el){

		var lines = el.find('.main-take-list .stock-item-line'),
			runningtotal = el.find('.running-total'),
			paneltotal = 0;


		lines.each(function(k,v){

			var item = jQuery(v),
				qty = parseInt( item.find('.qty-line').html() ),
				cost = parseFloat( item.find('.cost-line').html() ),
				total = item.find('.cost-line-total'),
				linetotal = cost*qty;

				if(isNaN(linetotal)){
					linetotal = 0;
				}

				total.html( linetotal.toFixed(2) );

			paneltotal += linetotal;
		
		});

		runningtotal.html(paneltotal.toFixed(2));


	}

	function cache_products(){
		console.log(arguments);
	}
	
	function set_void(el){
		var clicked = jQuery(el)
			parentline = jQuery('#inventory-list .list-group-item.active'),
			typeline = parentline.find('.grv-line'),
			fromline = parentline.find('.from-line');

		clicked.parent().find('button').removeClass('btn-primary').removeClass('active_movetype').addClass('btn-default');

		clicked.addClass('btn-primary').addClass('active_movetype').removeClass('btn-default');

		if(typeline.html() !== 'GRV' && typeline.length){
			typeline.html(clicked.html() + ' Voucher');
		}else{
			if(fromline.length){
				if(clicked.data('type') === 1 || clicked.data('type') === 3 || clicked.data('type') === 9){
					fromline.html( fromline.html().replace('to', 'from') );
				}else{
					fromline.html( fromline.html().replace('from', 'to') );
				}
			}
		}

	}

	function focus_search(el){
		
		jQuery(el).find('.search-this').focus();
	}
	function search_this(el){
		var field = jQuery(el),
			str = field.val(),
			panel = field.parent().next(),
			items = panel.find('.list-group-item');

			items.hide();
			if(!str.length){
				items.show();
			}else{
				items.each(function(k,v){
					var item = jQuery(v),
						data = item.data();
					
					for( var val in data){

						//if(typeof data[val] === 'string'){
							if(data[val].toString().toLowerCase().indexOf(str.toLowerCase()) > -1){
								item.show();
							}
						//}else if(typeof data[val] === 'number'){
							//if(parseFloat(str) )
						//}

					}

				});
				
			}

	}

	function delete_pack(el){
		var clicked = jQuery(el);
			parent = clicked.parent().parent().parent(),
			itemline = jQuery('.'+parent.prop('id'));

			parent.slideUp(100, function(){
				parent.remove();
				itemline.remove();
			});
	}
	function delete_count(el){
		var clicked = jQuery(el);
			parent = clicked.parent().parent().parent(),
			itemline = jQuery('.'+parent.prop('id'));

			parent.slideUp(100, function(){
				parent.remove();
				itemline.remove();
			});
			jQuery('#add-count-button').show();
	}

	function set_supplier(el){
		var clicked = jQuery(el),
			guid = clicked.data('guid'),
			currentline = jQuery('#inv-right-panel .list-group-item.currentline'),
			parentline = jQuery('#inventory-list .list-group-item.active');

			currentline.attr('data-guid', guid).data('guid', guid).find('span').html( clicked.html() );
			parentline.find('.from-line').html( 'from ' + clicked.html() );
			parentline.find('.grv-line').html( 'GRV' );

	}

	function add_product(el){
		var clicked = jQuery(el),
			data = clicked.data(),
			template,
			panel = jQuery('.currentpanel').parent(),
			curprod = jQuery('.currentpanel .sc-'+data.stockcode);

			if(curprod.length){
				var qtyline = curprod.find('.qty-line'),
					costline = curprod.find('.cost-line'),
					costtotal = curprod.find('.cost-line-total'),
					qty = parseInt(qtyline.html()) + 1,
					cost = parseFloat(costline.html())* qty ;

					qtyline.html(qty);
					costtotal.html(cost.toFixed(2));
			}else{
				template = Handlebars.compile( jQuery('#item-line-tmpl').html() )
				jQuery('.currentpanel .list-group').append( template( data ) );
			}

			calculate_Panel_Totals(panel);

	}

	function slide_items(el){
		var clicked = jQuery(el);
			panel	= clicked.parent(),
			items	= panel.next();

		jQuery('.currentpanel').removeClass('currentpanel');

		panel.slideUp(100);
		items.slideDown(100).addClass('currentpanel');

		
	}

	function close_items(el){
		var clicked = jQuery(el),
			items	= jQuery('.currentpanel'),
			panel	= items.prev();

		panel.slideDown(100);
		items.slideUp(100).removeClass('currentpanel');


	}


	jQuery('#add-order-button').on('click', function(){

		var template		= jQuery( jQuery('#order-tmpl').html() ),
			formtemplate	= jQuery( jQuery('#order-form-tmpl').html() ),
			id 				= 'frm' + parseInt(Math.random() * 10000000),
			date 			= new Date();

		template.attr('href', '#' + id).addClass(id);
		template.find('.date-line').html('Date: '+ date.toDateString() + ' ' + date.toLocaleTimeString());
		template.appendTo( jQuery('#inventory-list') );

		formtemplate.find('.date-line').html( date.toDateString() + ' ' + date.toLocaleTimeString() );

		formtemplate.prop('id', id).appendTo( jQuery('#inv-right-panel') ).hide();

		baldrickTrigger();
	});
	jQuery('#add-transfer-button').on('click', function(){

		var template		= jQuery( jQuery('#transfer-tmpl').html() ),
			formtemplate	= jQuery( jQuery('#transfer-form-tmpl').html() ),
			id 				= 'frm' + parseInt(Math.random() * 10000000),
			date 			= new Date();

		template.attr('href', '#' + id).addClass(id);
		template.find('.date-line').html('Date: '+ date.toDateString() + ' ' + date.toLocaleTimeString());
		template.appendTo( jQuery('#inventory-list') );

		formtemplate.find('.date-line').html( date.toDateString() + ' ' + date.toLocaleTimeString() );

		formtemplate.prop('id', id).appendTo( jQuery('#inv-right-panel') ).hide();

		baldrickTrigger();
	});
	jQuery('#add-adjustment-button').on('click', function(){

		var template		= jQuery( jQuery('#adjustment-tmpl').html() ),
			formtemplate	= jQuery( jQuery('#adjustment-form-tmpl').html() ),
			id 				= 'frm' + parseInt(Math.random() * 10000000),
			date 			= new Date();

		template.attr('href', '#' + id).addClass(id);
		template.find('.date-line').html('Date: '+ date.toDateString() + ' ' + date.toLocaleTimeString());
		template.appendTo( jQuery('#inventory-list') );

		formtemplate.find('.date-line').html( date.toDateString() + ' ' + date.toLocaleTimeString() );

		formtemplate.prop('id', id).appendTo( jQuery('#inv-right-panel') ).hide();

		baldrickTrigger();
	});
	jQuery('#add-count-button').on('click', function(){
		jQuery(this).hide();
		var template		= jQuery( jQuery('#count-tmpl').html() ),
			id 				= 'frm' + parseInt(Math.random() * 10000000),
			formtemplate	= jQuery('<div class="panel-form">'),
			date 			= new Date();

		template.attr('href', '#' + id).addClass(id).attr('data-target', '#' + id);
		template.find('.date-line').html('Date: '+ date.toDateString() + ' ' + date.toLocaleTimeString());
		template.appendTo( jQuery('#inventory-list') );
		
		formtemplate.prop('id', id).appendTo( jQuery('#inv-right-panel') );

		baldrickTrigger();
	});

	jQuery('#add-grv-button').on('click', function(){

		var template		= jQuery( jQuery('#grv-tmpl').html() ),
			formtemplate	= jQuery( jQuery('#grv-form-tmpl').html() ),
			id 				= 'frm' + parseInt(Math.random() * 10000000),
			date 			= new Date();

		template.attr('href', '#' + id).addClass(id);
		template.find('.date-line').html('Date: '+ date.toDateString() + ' ' + date.toLocaleTimeString());
		template.appendTo( jQuery('#inventory-list') );

		formtemplate.find('.date-line').html( date.toDateString() + ' ' + date.toLocaleTimeString() );

		formtemplate.prop('id', id).appendTo( jQuery('#inv-right-panel') ).hide();

		baldrickTrigger();

	});

	jQuery('#inventory-list').on('click', '.list-group-item', function(e){
		e.preventDefault();
		var clicked = jQuery(this),
			currentpanel = jQuery('.currentpanel');

		if(currentpanel.length){
			currentpanel.slideUp();
			currentpanel.prev().slideDown();
		}

		// close open panels


		jQuery('#left-panel .list-group-item').removeClass( 'active' );
		
		clicked.addClass( 'active' );

		jQuery('#inv-right-panel .panel-form').hide();
		jQuery('.supplier-line').removeClass('curr');
		jQuery( clicked.attr( 'href' ) ).show().find('.supplier-line');


	})

	
	jQuery('body').popover({
		selector: '[rel=popover]',
		html: true,
		animation: false,
		placement: "bottom",
		content: function(){

			var item = jQuery(this),
				qty = item.find('.qty-line'),
				cost = item.find('.cost-line'),
				linetotal = item.find('.cost-line-total'),
				template = jQuery( jQuery('#item-edit-tmpl').html() ),
				templ_qty = template.find('.input-qty'),
				templ_cost = template.find('.input-cost'),
				templ_sell = template.find('.input-sell'),
				panel = item.parent().parent().parent();

				templ_qty.val( qty.html() );
				templ_cost.val( cost.html() );
				templ_sell.val( linetotal.html() );

				templ_qty.on('keyup', function(){
					qty.html( this.value );
					calculate_Panel_Totals(panel);
				});
				templ_cost.on('keyup', function(){
					cost.html( parseFloat( this.value ).toFixed(2) );
					calculate_Panel_Totals(panel);
				});
				templ_sell.on('keyup', function(){
					sell.html( parseFloat( this.value ).toFixed(2) );
					calculate_Panel_Totals(panel);
				});

				template.find('.close-edit').on('click', function(){
					item.trigger('click');
				});
				template.find('.delete-item').on('click', function(){
					item.trigger('click').remove();
					calculate_Panel_Totals(panel);
				});

			return template;
		}
	}).on('shown.bs.popover', function(){
		if(!jQuery('#dashboard-nav').hasClass('product-rights')){
			jQuery('.has-product-rights').remove();
		}
	})


function build_count_process(el){
	console.log(el);

	if(confirm('Are you sure you want to process this stocktake?')){

		var clicked = jQuery(el),
			takeguid = clicked.data('takeguid');

		jQuery.get('http://api.humble.co.za/1.1/'+jQuery.cookie('token')+'/stocktake/commit', function(res){
			if(res.message){
				if(res.message !== 'OK'){
					alert(res.message);
				}else{
					delete_count(jQuery('#delete_stocktake')[0]);
					product_sync();
				}
			}
		})

	}

	return false;
}

function build_order_process(el){
	return build_process(el, 'order');
}
function build_grv_process(el){
	return build_process(el, 'grv');
}
function build_transfer_process(el){
	return build_process(el, 'transfers');
}
function build_adjustment_process(el){
	return build_process(el, 'adjustments');
}

function build_process(el, type){
	var clicked			= jQuery(el),
		parent			= clicked.closest('.panel-form'),
		move_type		= parent.find('.active_movetype').data('type'),
		supplier		= parent.find('.supplier-line'),
		reference_ln	= parent.find('.reference-line'),
		reference		= parent.find('.reference-number').val(),
		date			= parent.find('.date-line').html(),
		excl_total		= parseFloat( parent.find('.running-total').html() ),
		items_ln		= parent.find('.items-line'),
		items			= parent.find('.stock-item-line'),
		buttons			= parent.find('.process-buttons'),
		stop			= false,
		data 			= {
			"header"	: {
				"movestate" 	: type === 'order' ? 1 : move_type === 1 ? 1 : -1,
				"acc" 			: supplier.find('span').html(),
				"excl"			: excl_total,
				"vat"			: null,
				"movetype"		: type === 'order' ? 0 : move_type,
				"direction"		: type === 'order' ? "IN" : move_type === 1 ? "IN" : "OUT",
				"refnr"			: reference,
				"accguid"		: supplier.data('guid'),
				"datetime"		: date,
				"incl"			: null,
			},
			"lines"		: []
		};

		// check for a supplier
		if( !supplier.data('guid') && supplier.length ){
			stop = true;
			supplier.addClass('list-group-item-danger');
		}else{
			supplier.removeClass('list-group-item-danger');
		}
		// check for a reference
		if( reference.length < 1 ){
			stop = true;
			reference_ln.addClass('list-group-item-danger');

		}else{
			reference_ln.removeClass('list-group-item-danger');
		}
		// check for items
		if( items.length < 1 ){
			stop = true;
			items_ln.addClass('list-group-item-danger');

		}else{
			items_ln.removeClass('list-group-item-danger');
		}


		if(stop === true){
			alert('Missing values- please correct the fields highlighted in red.');
			return false;
		}
		if(!do_confirm(el)){
			return false;
		}
		buttons.slideUp(200);
		/// build lines
		items.each(function(k,v){
			var item 		= jQuery(v),
				qty			= parseFloat( item.find('.qty-line').html() ),
				desc		= item.find('.list-group-item-heading').html(),
				cost		= parseFloat( item.find('.cost-line').html() ),
				linecost	= parseFloat( item.find('.cost-line-total').html() ),
				vat_per		= parseFloat( item.data('vat') ),
				line_vat	= vat_per > 0 ? vat_per * linecost / 100 : 0 ,
				line;

				// add tax 
				data.header.vat += line_vat;

				// add line to lines
				line = {
					"line"			: data.lines.length,
					"productguid"	: item.data('guid'),
					"linevat"		: line_vat,
					"unitcost"		: cost,
					"linecost"		: linecost,
					"descr"			: desc,
					"qty"			: qty,
					"lineincl"		: linecost + line_vat,
				};

				data.lines.push( line );


		});

		// update incl
		data.header.incl += data.header.excl + data.header.vat;


		data = JSON.stringify(data);
		
		$.post( 'http://api.humble.co.za/1.1/' + jQuery.cookie('token') + '/movement', {data: data, web: true}, function(res){
			if( res.message === 'OK'){
				if(type === 'order'){
					// create a GRV
					var template		= jQuery( jQuery('#grv-tmpl').html() ),
						formtemplate	= jQuery( jQuery('#grv-form-tmpl').html() ),
						id 				= 'frm' + parseInt(Math.random() * 10000000),
						date 			= new Date();

					template.attr('href', '#' + id).addClass(id);
					template.find('.date-line').html('Date: '+ date.toDateString() + ' ' + date.toLocaleTimeString());
					template.appendTo( jQuery('#inventory-list') );

					formtemplate.find('.date-line').html( date.toDateString() + ' ' + date.toLocaleTimeString() );

					formtemplate.prop('id', id).appendTo( jQuery('#inv-right-panel') ).hide();					

					var new_grv_items = formtemplate.find('.panel-items-list');					
					items.appendTo(new_grv_items);

					// add supplier
					var new_supplierline = formtemplate.find('.supplier-line'),
						new_reference = formtemplate.find('.reference-number');

					new_reference.val(reference);

					new_supplierline.attr('guid', supplier.data('guid')).data('guid', supplier.data('guid')).find('span').html( supplier.find('span').html() );

					template.find('.list-group-item-heading').html('<span class="grv-line">GRV</span> <span class="from-line">from '+supplier.find('span').html()+'</span>');

					calculate_Panel_Totals(formtemplate);
				}				
				jQuery('.' + parent.attr('id') ).slideUp(function(){
					jQuery(this).remove();
				});
				parent.slideUp('200', function(){
					jQuery(this).remove();
				});
				var report = 'http://api.humble.co.za/1.1/' + jQuery.cookie('token') + '/pdf-generate/movement?template='+type+'&guid='+res.guid;
				console.log(report);
				window.location = report;
				// trigger a product sync
				product_sync();
			}else{
				//alert(res.message);
			}
		});

		return false;

}





site_count_check();
setInterval(site_count_check, 10000);
function site_count_check(){

	var site_global = JSON.parse( sessionStorage['sites'] );

	if(site_global.total > 1){
		jQuery('.multi-site-required').show();
	}else{
		jQuery('.multi-site-required').hide();
	}


}







jQuery('body').on('click', '.item-search-toggle', function(e){

	var clicked = jQuery(this);

	jQuery('.item-search-toggle').addClass('btn-default').removeClass('btn-primary');

	clicked.removeClass('btn-default').addClass('btn-primary');

	jQuery('.live0').remove();

	if(clicked.data('type') === 'take'){
		jQuery('.inv-product-search').hide();
		jQuery('.main-take-list').show();
	}else{
		jQuery('.inv-product-search').show();
		jQuery('.main-take-list').hide();

		var items = jQuery('.inv-product-search').find('.search-item-line');

		items.each(function(k,v){

			var item = jQuery(v);

			if(jQuery('.main-take-list').find('[data-guid="'+item.data('guid')+'"]').length){
				item.remove();
			}

		})

	}

})




// bind search count
jQuery('body').on('keyup', '.count-item-search', function(){



	var wrap = jQuery(this),		
		str = wrap.val(),		
		panel = wrap.closest('.stock-items-panel'),
		type = panel.find('.item-search-toggle.btn-primary').data('type'),
		list;

		jQuery('.inv-product-search').hide();
		jQuery('.main-take-list').hide();


		if(type === 'take'){

			jQuery('.main-take-list').show();
			list = panel.find('.panel-items-list');			
		}else{

			jQuery('.inv-product-search').show();
			list = panel.find('.inv-product-search');
			list.find('.search-item-line').show().removeClass('hide');

		}

	var items = list.find('.search-item-line'),
		nada = jQuery('<div class="list-group-item no-item-found">No results for "'+str+'"</div>'),
		count = 0;





		list.find('.no-item-found').remove();
		console.log(items);
		items.hide();
		if(!str.length){
			items.show();
			//jQuery('.list-group-item.active').trigger('click');
			count = items.length;
		}else{
			items.each(function(k,v){
				var item = jQuery(v),
					data = item.data('descr'),
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




jQuery('body').on('click', '.inv-item-result', function(){

	var ref = jQuery('.main-take-list').find('.set-qty-item').first().data('takeguid'),
		clicked = jQuery(this),
		panel = jQuery('.main-take-list');
		
		console.log(ref);
		
		clicked.removeClass('inv-item-result');

		clicked.find('a').attr('data-takeguid', ref);
		clicked.find('.hide').removeClass('hide');

		clicked.appendTo(panel);

		jQuery('.inv-product-search').hide();
		jQuery('.main-take-list').show();		

		
		jQuery('.take-search-btn').trigger('click');
		jQuery('#take-product-search').val('').trigger('keyup');

})




</script>




