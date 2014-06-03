<?php
/*

Caldoza Engine ------------------------

File	:	templates/products-list.php
Created	: 	2013-12-04

*/


$sitename = $db->get_var("SELECT `sitename` FROM `sites` WHERE `guid` = '".$user->siteguid."';");
$suppliers = $db->get_results("SELECT * FROM `community` WHERE `companyguid` = '".$user->cguid."' AND `communitytype` = 1 AND `live` = 1 ;");



?>
<div class="col-sm-12" id="reports-panels">
	<div class="row">
		<div class="col-sm-12">
			<div class="btn-group btn-group-justified reports-nav">
				<a href="#sales-report-panel" class="btn btn-primary">Sales</a>
				<a href="#inventory-report-panel" class="btn btn-default">Inventory</a>
				<a href="#suppliers-report-panel" class="btn btn-default">Suppliers</a>
				<a href="#staff-report-panel" class="btn btn-default">Staff</a>
				<a href="#cashup-report-panel" class="btn btn-default">Cash Up</a>
			</div>
		</div>
	</div>
	<br>

	<?php 
	// SALES REPORT PANEL
	?>
	<div class="row report-panel" id="sales-report-panel">
		<div class="col-sm-5">
			<div class="report-panel-list">
				<div class="panel panel-default">
					<div class="list-group">
						<a href="#category-gp-report" class="list-group-item active">
							<h4 class="list-group-item-heading">Category GP</h4>
							<p class="list-group-item-text">Gross Profit</p>
						</a>
						<a href="#gp-sales-report" class="list-group-item">
							<h4 class="list-group-item-heading">GP Sales</h4>
							<p class="list-group-item-text">Gross Profit</p>
						</a>
						<a href="#sales-audit-trail-report" class="list-group-item">
							<h4 class="list-group-item-heading">Sales Audit Trail</h4>
							<p class="list-group-item-text">Audit</p>
						</a>
					</div>

				</div>
			</div>
		</div>
		<div class="col-sm-7">







			<div class="report-setup-panel" id="category-gp-report">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="category-gp-sales" data-type="sale">Run Category GP Report</button>
			</div>





			
			<div class="report-setup-panel" id="gp-sales-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
						<a href="#" class="list-group-item report-field-item has-filter">
							<span class="pull-right text-muted" data-type="filter"></span>
							Filter
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="gp-sales" data-type="sale">Run GP Sales Report</button>
			</div>





			
			<div class="report-setup-panel" id="sales-audit-trail-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
						<a href="#" class="list-group-item report-field-item has-filter">
							<span class="pull-right text-muted" data-type="filter"></span>
							Filter
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="sales-audit-trail" data-type="sale">Run Sales Audit Trail Report</button>
			</div>





			
		</div>
	</div>


	<?php 
	// INVENTORY REPORT PANEL
	?>
	<div class="row report-panel" id="inventory-report-panel" style="display:none;">
		<div class="col-sm-5">
			<div class="report-panel-list">
				<div class="panel panel-default">
					<div class="list-group">
						<a href="#adjustments-report" class="list-group-item">
							<h4 class="list-group-item-heading">Adjustments</h4>
							<p class="list-group-item-text">Movement</p>
						</a>
						<a href="#goods-received-report" class="list-group-item">
							<h4 class="list-group-item-heading">Goods Received</h4>
							<p class="list-group-item-text">Movement</p>
						</a>
						<a href="#imei-audit-report" class="list-group-item">
							<h4 class="list-group-item-heading">IMEI Audit</h4>
							<p class="list-group-item-text">Audit</p>
						</a>
						<a href="#imei-in-stock-report" class="list-group-item">
							<h4 class="list-group-item-heading">IMEI In Stock</h4>
							<p class="list-group-item-text">In Stock</p>
						</a>
						<a href="#in-stock-report" class="list-group-item">
							<h4 class="list-group-item-heading">In Stock</h4>
							<p class="list-group-item-text">In Stock</p>
						</a>
						<a href="#inventory-count-report" class="list-group-item">
							<h4 class="list-group-item-heading">Inventory Count</h4>
							<p class="list-group-item-text">Movement</p>
						</a>
						<a href="#orders-report" class="list-group-item">
							<h4 class="list-group-item-heading">Orders</h4>
							<p class="list-group-item-text">Movement</p>
						</a>
						<a href="#product-audit-report" class="list-group-item">
							<h4 class="list-group-item-heading">Product Audit</h4>
							<p class="list-group-item-text">Audit</p>
						</a>
						<a href="#transfers-report" class="list-group-item">
							<h4 class="list-group-item-heading">Transfers</h4>
							<p class="list-group-item-text">Movement</p>
						</a>
					</div>

				</div>
			</div>
		</div>
		<div class="col-sm-7">







			<div class="report-setup-panel" id="adjustments-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="adjustments" data-movementtype="Adjustment" data-type="movement" data-movetype="9,10">Run Adjustments Report</button>
			</div>






			<div class="report-setup-panel" id="goods-received-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="grv" data-movementtype="Goods Received Voucher" data-movetype="1" data-type="movement">Run Goods Received Report</button>
			</div>






			<div class="report-setup-panel" id="imei-audit-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item report-field-item has-filter"  data-title="IMEI">
							<span class="pull-right text-muted" data-type="imei"></span>
							IMEI
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="" >Run IMEI Audit Report</button>
			</div>






			<div class="report-setup-panel" id="imei-in-stock-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="list-group-item report-field-item has-filter">
							<span class="pull-right text-muted" data-type=""></span>
							Filter
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="">Run IMEI In Stock Report</button>
			</div>






			<div class="report-setup-panel" id="in-stock-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="list-group-item report-field-item list-check">
							<span class="pull-right text-muted" data-type="toggle" data-field="show_costs"><i class="glyphicon glyphicon-ok"></i></span>
							Show Costs
						</a>
						<a href="#" class="list-group-item report-field-item has-filter">
							<span class="pull-right text-muted" data-type="filter"></span>
							Filter
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="onhand" data-type="onhand">Run In Stock Report</button>
			</div>






			<div class="report-setup-panel" id="inventory-count-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="inventory-count" data-type="movement" data-movetype="6">Run Inventory Count Report</button>
			</div>






			<div class="report-setup-panel" id="orders-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="order" data-type="movement" data-movetype="0" data-movementtype="Order">Run Orders Report</button>
			</div>






			<div class="report-setup-panel" id="product-audit-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
						<a href="#" class="list-group-item trigger report-field-item" data-call="products" data-cache-session="products" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Product" data-template="#product-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="productguid" data-guid=""></span>
							Product
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="product-audit" data-type="product-audit">Run Product Audit Report</button>
			</div>



			



			<div class="report-setup-panel" id="transfers-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="transfers" data-type="movement" data-movetype="3,4" data-movementtype="Transfer">Run Transfers Report</button>
			</div>






		</div>
	</div>


	<?php 
	// SUPPLIERS REPORT PANEL
	?>
	<div class="row report-panel" id="suppliers-report-panel" style="display:none;">
		<div class="col-sm-5">
			<div class="report-panel-list">
				<div class="panel panel-default">
					<div class="list-group">
						<a href="#age-analysis-report" class="list-group-item">
							<h4 class="list-group-item-heading">Age Analysis</h4>
							<p class="list-group-item-text">Audit</p>
						</a>
						<a href="#supplier-goods-vouchers-report" class="list-group-item">
							<h4 class="list-group-item-heading">Supplier Goods Vouchers</h4>
							<p class="list-group-item-text">Audit</p>
						</a>
					</div>

				</div>
			</div>
		</div>
		<div class="col-sm-7">







			<div class="report-setup-panel" id="age-analysis-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="suppliers" data-cache-session="suppliers" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Supplier" data-template="#supplier-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="accguid" data-guid="<?php echo $suppliers[0]->guid; ?>"><?php echo $suppliers[0]->descr; ?></span>
							Supplier
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="age-analysis" data-type="movement">Run Age Analysis Report</button>
			</div>






			<div class="report-setup-panel" id="supplier-goods-vouchers-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="supplier-goods-received" data-movetype="1" data-type="movement">Run Supplier Goods Vouchers Report</button>
			</div>






		</div>
	</div>


	<?php 
	// STAFF REPORT PANEL
	?>
	<div class="row report-panel" id="staff-report-panel" style="display:none;">
		<div class="col-sm-5">
			<div class="report-panel-list">
				<div class="panel panel-default">
					<div class="list-group">
						<a href="#audit-logs-report" class="list-group-item">
							<h4 class="list-group-item-heading">Audit Logs</h4>
							<p class="list-group-item-text">Audit</p>
						</a>
						<a href="#staff-gp-sales-report" class="list-group-item">
							<h4 class="list-group-item-heading">Staff GP Sales</h4>
							<p class="list-group-item-text">Sales</p>
						</a>
					</div>

				</div>
			</div>
		</div>
		<div class="col-sm-7">







			<div class="report-setup-panel" id="audit-logs-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="staff-audit" data-type="audit">Run Audit Logs Report</button>
			</div>





			<div class="report-setup-panel" id="staff-gp-sales-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="staff-gp-sales" data-type="sale" >Run Staff GP Sales Report</button>
			</div>






		</div>
	</div>


	<?php 
	// CASHUP REPORT PANEL
	?>
	<div class="row report-panel" id="cashup-report-panel" style="display:none;">
		<div class="col-sm-5">
			<div class="report-panel-list">
				<div class="panel panel-default">
					<div class="list-group">
						<a href="#cash-up-reprint-report" class="list-group-item">
							<h4 class="list-group-item-heading">Cash Up Re-Print</h4>
							<p class="list-group-item-text">Audit</p>
						</a>
						<a href="#cash-up-summary-report" class="list-group-item">
							<h4 class="list-group-item-heading">Cash Up Summary</h4>
							<p class="list-group-item-text">Audit</p>
						</a>
						<a href="#payout-report" class="list-group-item">
							<h4 class="list-group-item-heading">Pay Outs</h4>
							<p class="list-group-item-text">Audit</p>
						</a>
					</div>

				</div>
			</div>
		</div>
		<div class="col-sm-7">







			<div class="report-setup-panel" id="cash-up-reprint-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="cashup-reprint" data-type="cashup" data-modal="true">Run Cash Up Re-Print Report</button>
			</div>





			<div class="report-setup-panel" id="cash-up-summary-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="cashup-summary" data-type="cashup">Run Cash Up Summary Report</button>
			</div>





			<div class="report-setup-panel" id="payout-report" style="display:none;">
				<div class="panel panel-default">
					<div class="list-group report-setup-options" data-call="general">
						<a href="#" class="list-group-item trigger report-field-item" data-call="sites" data-cache-session="sites" data-active-class="currentline" data-group="selector" data-modal="selector_modal" data-modal-title="Select Site" data-template="#site-list-selector-tmpl">
							<span class="pull-right text-muted" data-type="siteguid" data-guid="<?php echo $user->siteguid; ?>"><?php echo $sitename; ?></span>
							Store
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="start_date"><?php echo date('Y-m-d'); ?></span>
							Start Date
						</a>
						<a href="#" class="has-date-picker list-group-item report-field-item">
							<span class="pull-right text-muted" data-type="end_date"><?php echo date('Y-m-d'); ?></span>
							End Date
						</a>
					</div>
				</div>
				<button type="button" class="btn btn-lg btn-block btn-primary btn-process-report" data-template="payouts" data-type="payouts">Run Pay Outs Report</button>
			</div>






		</div>
	</div>

</div>


<span class="trigger" id="manual_modal" data-modal="genmodal" data-modal-title="" data-modal-content="#movement-list-selector-tmpl"></span>
<script type="text/html" id="movement-list-selector-tmpl">
<div class="list-group site-guid-selector">
	{{#if data}}
		{{#each data}}
		<a href="{{../url}}&guid={{guid}}" class="list-group-item" data-guid="{{guid}}">
			<h4 class="list-group-item-heading">{{../type}} {{direction}} - {{acc}} - {{refnr}}</h4>
			<p class="list-group-item-text">{{datetime}} - R {{excl}}</p>
		</a>
		{{/each}}
	{{else}}
		<div class="list-group-item">No Movements Found</div>
	{{/if}}
</div>
</script>
<script type="text/html" id="site-list-selector-tmpl">
<div class="list-group site-guid-selector">
	{{#each sites}}
	<a href="#" class="list-group-item set-guid" onclick="set_guid_field(this); return false;" data-guid="{{guid}}">{{site_name}}</a>
	{{/each}}
</div>
</script>
<script type="text/html" id="supplier-list-selector-tmpl">
<div class="list-group">
	{{#each suppliers}}
	<a href="#" class="list-group-item set-guid" onclick="set_guid_field(this); return false;" data-guid="{{guid}}">{{descr}}</a>
	{{/each}}
</div>
</script>
<script type="text/html" id="suppliers-list-selector-tmpl">
<div class="list-group">
	{{#each suppliers}}
	<a href="{{../url}}&accguid={{accguid}}" class="list-group-item" data-accguid="{{accguid}}">{{acc}}</a>
	{{/each}}
</div>
</script>

<script type="text/html" id="product-list-selector-tmpl">
<div class="list-group">
	{{#each products}}
	<a href="#" class="list-group-item set-guid" onclick="set_guid_field(this); return false;" data-guid="{{guid}}">{{descr}}</a>
	{{/each}}
</div>
</script>

<script type="text/html" id="cashup-list-selector-tmpl">
<div class="list-group">
	{{#if data}}
		{{#each data}}
		<a href="{{../url}}&guid={{guid}}" class="list-group-item" data-guid="{{guid}}">
			<h4 class="list-group-item-heading">{{../type}} {{cashier}} - {{datetime}}</h4>
			<p class="list-group-item-text">{{devicename}}</p>
		</a>
		{{/each}}
	{{else}}
		<div class="list-group-item">No Cash Ups Found</div>
	{{/if}}
</div>
</script>

<script type="text/javascript">


jQuery(function($){


	$('#reports-panels').on('click','.reports-nav a', function(e){

		e.preventDefault();

		var clicked = $(this);

		$('.reports-nav a').removeClass('btn-primary').addClass('btn-default');
		clicked.removeClass('btn-default').addClass('btn-primary');

		$('.report-panel,.report-setup-panel').hide();

		$( clicked.attr('href') ).show().find('.list-group-item').first().trigger('click');

	});

	$('#reports-panels').on('click','.report-panel-list a', function(e){

		e.preventDefault();

		var clicked = $(this);

		$('.report-panel-list a').removeClass('active');
		clicked.addClass('active');

		$('.report-setup-panel').hide();

		$( clicked.attr('href') ).show();

	});



	// Fetch DATA

	$('#reports-panels').on('click', '.btn-process-report', function(e){

		e.preventDefault();


		var clicked = $(this),
			parent = clicked.parent(),
			fields = parent.find('.report-field-item span'),
			data = {
				template : clicked.data('template')
			},
			type = clicked.data('type'),
			end = false;




			fields.each(function(k,v){
				var field = $(v),
					type = field.data('type'),
					name =field.parent().clone();

					name.find('span').remove();


				if(type === 'siteguid' || type === 'accguid' || type === 'productguid'){
					
					data[type] = field.attr('data-guid');

				}else if(type === 'toggle'){
					
					type = field.data('field');

					if(field.html().length){
						data[type] = 1;
					}else{
						data[type] = 0;
					}
					
				}else{
					data[type] = field.html();
				}
				
				if(!data[type] && type === 'siteguid'){
					alert('Requires ' + name.text() );
					end = true;
					return;
				}

			});

			if(end){
				return;
			}

			var qs = '';

			for(var key in data) {
				var value = data[key];
				qs += encodeURIComponent(key) + "=" + encodeURIComponent(value) + "&";
			}


			if( ( type === 'movement' && clicked.data('template') !== 'age-analysis' ) || clicked.data('modal') ){

				if(type === 'movement'){
					var movetype,
						movelabel = clicked.data('movementtype');

					console.log(clicked.data('movetype'));
					console.log(typeof clicked.data('movetype'));
					if(typeof clicked.data('movetype') === 'number'){
						movetype = '&movetype=' + clicked.data('movetype');
					}else{
						movetype = '&movetype[]=' + clicked.data('movetype').split(',').join('&movetype[]=');
					}

					qs += movetype;
				}

				// set generic list template
				$('#manual_modal').attr('data-modal-content', '#movement-list-selector-tmpl');
				$('#manual_modal').data('modalContent', '#movement-list-selector-tmpl');

				// Switch to alter grouping for list template
				if(clicked.data('template') === 'supplier-goods-received'){
					// add new html source
					$('#manual_modal').attr('data-modal-content', '#suppliers-list-selector-tmpl');
					$('#manual_modal').data('modalContent', '#suppliers-list-selector-tmpl');

				} else if(clicked.data('template') === 'cashup-reprint'){
					// add new html source
					$('#manual_modal').attr('data-modal-content', '#cashup-list-selector-tmpl');
					$('#manual_modal').data('modalContent', '#cashup-list-selector-tmpl');

				}



				$.get('http://api.humble.co.za/1.1/' + $.cookie('token') + '/report/' + type + '?' + qs, function(data){
					
					//console.log(data);
					// get list template
					$('#manual_modal').attr('data-modal-title', 'Select Movement').trigger('click');
					$('#manual_modal').data('modalTitle', 'Select Movement').trigger('click');

					// get body and convert to template
					var template = Handlebars.compile( $('#genmodal_baldrickModalBody').html() );

					if(clicked.data('template') === 'supplier-goods-received'){

						if(data.data){
							data.suppliers = {};
							
							for( var guid in data.data){
								data.suppliers[data.data[guid].accguid] = data.data[guid];
							}
						}
					}

					data.type = movelabel;
					data.url = 'http://api.humble.co.za/1.1/' + $.cookie('token') + '/pdf-generate/' + type + '?' + qs;

					$('#genmodal_baldrickModalBody').html( template( data ) );					



				});

			}else{
				console.log('http://api.humble.co.za/1.1/' + $.cookie('token') + '/pdf-generate/' + type + '?' + qs);
				window.location = 'http://api.humble.co.za/1.1/' + $.cookie('token') + '/pdf-generate/' + type + '?' + qs;
			}

			
			//http://api.humble.co.za/1.1/' + $.cookie('token') + '/pdf-generate/' + type + '?' + qs
		
		//console.log(qs);


	});



    $('#reports-panels').on('click', '.report-field-item', function(e){

    	e.preventDefault();
    	//$('.popover').remove();

    });

	$('#reports-panels').popover({
		selector: '[rel=popselect]',
		html: true,
		animation: false,
		placement: "bottom",
		content: function(){

			var field = $(this),
				template 	=  Handlebars.compile( $('#' + field.data('templ') ).html() ),
				data		=	JSON.parse(sessionStorage[field.data('list')]);

				
				

			return template( data );
		}
	}).on('shown.bs.popover', function(){
		
	});





	// bind filter 	
	$('#reports-panels').on('click', '.has-filter', function(e){
		
		var clicked = $(this),
			field = clicked.find('span'),
			value = prompt( clicked.data('title') ? clicked.data('title') : 'Filter' );

			field.html( value );

	});

	// biund toggle
	$('#reports-panels').on('click', '.list-check', function(e){
		var clicked = $(this),
			field = clicked.find('span');

		if(field.html().length){
			field.html('');
		}else{
			field.html('<i class="glyphicon glyphicon-ok"></i>');
		}
	});

    $('.has-date-picker').datepicker({
    	format: "yyyy-mm-dd",
    	autoclose: true,
    	todayHighlight: true,
    	orientation: "right",
    }).on('changeDate', function(data){

    	var date = data.format(),
    		field = $(this),
    		value = field.find('span');

    		value.html( date );

    });



});



// bind set site
function set_guid_field(el){


	var row 	= jQuery(el),
		field	= jQuery('.currentline'),
		value 	= field.find('span');

	value.attr('data-guid', row.data('guid'));
	value.html( row.html() );

	jQuery('#selector_modal_baldrickModal').modal("hide");

};




</script>






























