<?php
/*

Caldoza Engine ------------------------

File	:	templates/products-list.php
Created	: 	2013-12-04

*/

//$categories = $db->get_results("SELECT * FROM `categories` WHERE `companyguid` = '".$user->cguid."';");
$producttypes = $db->get_results("SELECT * FROM `producttypes`;");

$pastelhash = $db->get_var("SELECT `pastelhash` FROM `sites` WHERE `guid` = '".$user->siteguid."' AND `coguid` = '".$user->cguid."'; ");

?>

<div class="col-sm-12" id="settings-panels">
	<div class="row">

		<div class="col-sm-3">
			<div id="left-panel">
				<div class="panel panel-default">
					<div class="list-group settings-list-nav" data-call="general">
						<a href="#general-panel" class="has-general-rights list-group-item <?php if(empty($user->general)){ echo 'hidden'; } ; ?>">General</a>
						<a href="#products-panel" class="has-product-rights list-group-item <?php if(!empty($user->products)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?>" data-callback="focus_search" data-group="settings-nav" data-call="products" data-cache-session="products" data-template="#products-list-tmpl" data-target="#products-list">Products</a>
						<a href="#categories-panel" class="has-product-rights list-group-item <?php if(!empty($user->products)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?>" data-callback="focus_search" data-group="settings-nav" data-call="categories" data-cache-session="categories" data-template="#categories-list-tmpl" data-target="#categories-list">Categories</a>
						<a href="#users-panel" class="list-group-item trigger" data-callback="focus_search" data-group="settings-nav" data-call="users" data-cache-session="users" data-template="#users-list-tmpl" data-target="#users-list">Users</a>
						<a href="#customers-panel" class="has-community-rights list-group-item <?php if(!empty($user->community)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?>" data-callback="focus_search" data-group="settings-nav" data-call="customers" data-cache-session="customers" data-template="#customers-list-tmpl" data-target="#customers-list">Customers</a>
						<a href="#suppliers-panel" class="has-community-rights  list-group-item <?php if(!empty($user->community)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?>" data-callback="focus_search" data-group="settings-nav" data-call="suppliers" data-cache-session="suppliers" data-template="#suppliers-list-tmpl" data-target="#suppliers-list">Suppliers</a>

						<a href="#sites-panel" class="has-general-rights list-group-item <?php if(!empty($user->general)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?>" data-callback="focus_search" data-group="settings-nav" data-call="sites" data-cache-session="sites" data-template="#sites-list-tmpl" data-target="#sites-list">Sites</a>
						
						<a href="#sage-pastel-panel" class="has-general-rights list-group-item <?php if(empty($user->general)){ echo 'hidden'; }; ?>">Sage Pastel</a>
					</div>

				</div>
			</div>
		</div>
		<div class="col-sm-9">
			<div id="settings-right-panel">
				<div id="general-panel" class="settings-panel" style="display:none;">
					
					<div class="panel panel-default">				
						<div class="list-group" style="max-height: 500px; overflow: auto;" id="general-list">
							<a href="http://api.humble.co.za/1.1/app_sync" data-cache-session="appsync" class="list-group-item trigger" data-modal="sync" data-modal-title="Cloud Sync" data-modal-animate="true">Cloud Sync</a>
							<a href="#" class="list-group-item local-settings" data-storekey="email_reciepts" onclick="toggle_field(this); return">
								<span class="pull-right text-muted"><i class="glyphicon glyphicon-ok"></i></span>
								Email Receipts
							</a>
							<a href="#" id="printer-trigger" class="list-group-item trigger" data-request="http://localhost:9200/" data-timeout="10000" data-callback="has_printer_check" data-error="printer_offline" data-template="#printers-list-tmpl" data-modal="printer" data-group="prnt" data-active-class="nope" data-modal-title="Receipt Printer Setup" data-modal-animate="true">
								<span class="pull-right text-muted printer-setup">Not Setup</span>
								Print Slip Receipts
							</a>
							<a href="#" class="list-group-item local-settings" data-storekey="print_small_fonts" onclick="toggle_field(this); return">
								<span class="pull-right text-muted"><i class="glyphicon glyphicon-ok"></i></span>
								Receipt Small Font (T88)
							</a>							
							<a href="#" class="list-group-item local-settings" data-storekey="print_a4_receipts" onclick="toggle_field(this); return">
								<span class="pull-right text-muted"><i class="glyphicon glyphicon-ok"></i></span>
								Print A4 Receipts
							</a>
							<a href="#" class="list-group-item local-settings" data-storekey="autolock_after_sale" onclick="toggle_field(this); return">
								<span class="pull-right text-muted"><i class="glyphicon glyphicon-ok"></i></span>
								Auto Lock After Sale
							</a>
							<a href="#" class="list-group-item local-settings" data-storekey="use_cash_drawer" onclick="toggle_field(this); return">
								<span class="pull-right text-muted"><i class="glyphicon glyphicon-ok"></i></span>
								Use Cash Drawer
							</a>
						</div>
					</div>

				</div>
				<div id="products-panel" class="settings-panel has-feedback" style="display:none;">
					<input class="form-control list-search" id="product-search">
					<span class="glyphicon glyphicon-search form-control-feedback" style="margin-top: -20px; margin-right: -15px;"></span>
					<br>
					<div class="panel panel-default">				
						<div class="list-group list-sorted" style="max-height: 500px; overflow: auto;" id="products-list">

						</div>				
					</div>
					<button class="btn btn-primary trigger" data-callback="new_product" data-modal-buttons="Create Product|updateProduct" data-modal-content="#product-new-tmpl" data-active-class="active" data-group="productslist" data-modal-animate="true" data-modal-title="New Product" data-target-insert="replace" data-modal="true" >Add Product</button>
				</div>
				<div id="categories-panel" class="settings-panel has-feedback" style="display:none;">
					<input class="form-control list-search" id="category-search">
					<span class="glyphicon glyphicon-search form-control-feedback" style="margin-top: -20px; margin-right: -15px;"></span>
					<br>
					<div class="panel panel-default">				
						<div class="list-group list-sorted" style="max-height: 500px; overflow: auto;" id="categories-list">

						</div>
					</div>
					<button type="button" class="btn btn-primary trigger" data-before="edit_category" data-method="POST" data-call="category/new" data-active-class="cat-active" data-callback="updateCategory">Add Category</button>
				</div>
				<div id="users-panel" class="settings-panel has-feedback" style="display:none;">
					<input class="form-control list-search" id="user-search">
					<span class="glyphicon glyphicon-search form-control-feedback" style="margin-top: -20px; margin-right: -15px;"></span>
					<br>
					<div class="panel panel-default">				
						<div class="list-group list-sorted" style="max-height: 500px; overflow: auto;" id="users-list">

						</div>
					</div>
					<?php if(!empty($user->users)){ ?>
					<button class="btn btn-primary trigger" data-callback="init_panel" data-modal-buttons="Create User|updateUser" data-modal-content="#user-new-tmpl" data-active-class="active" data-group="userslist" data-modal-animate="true" data-modal-title="New User" data-target-insert="replace" data-modal="true" >Add User</button>
					<?php } ?>
				</div>
				<div id="customers-panel" class="settings-panel has-feedback" style="display:none;">
					<input class="form-control list-search" id="customer-search">
					<span class="glyphicon glyphicon-search form-control-feedback" style="margin-top: -20px; margin-right: -15px;"></span>
					<br>

					<div class="panel panel-default">				
						<div class="list-group list-sorted" style="max-height: 500px; overflow: auto;" id="customers-list">

						</div>
					</div>
					<button type="button" class="btn btn-primary trigger" data-before="add_customer" data-method="POST" data-call="customer/new" data-active-class="customer-active" data-callback="updatedCustomer">Add Customer</button>
				</div>
				<div id="suppliers-panel" class="settings-panel has-feedback" style="display:none;">
					<input class="form-control list-search" id="supplier-search">
					<span class="glyphicon glyphicon-search form-control-feedback" style="margin-top: -20px; margin-right: -15px;"></span>
					<br>
					<div class="panel panel-default">				
						<div class="list-group list-sorted" style="max-height: 500px; overflow: auto;" id="suppliers-list">

						</div>
					</div>
					<button type="button" class="btn btn-primary trigger" data-before="add_supplier" data-method="POST" data-call="supplier/new" data-active-class="supplier-active" data-callback="updatedSupplier">Add Supplier</button>
				</div>
				<div id="sites-panel" class="settings-panel has-feedback" style="display:none;">
					<input class="form-control list-search" id="sites-search">
					<span class="glyphicon glyphicon-search form-control-feedback" style="margin-top: -20px; margin-right: -15px;"></span>
					<br>
					<div class="panel panel-default">				
						<div class="list-group list-sorted" style="max-height: 500px; overflow: auto;" id="sites-list">

						</div>
					</div>
					<button class="btn btn-primary trigger" data-modal-buttons="Create Site|updateSite" data-modal-content="#site-new-tmpl" data-active-class="active" data-group="sitelist" data-modal-animate="true" data-modal-title="New Site" data-target-insert="replace" data-modal="true" >Add Site</button>
				</div>
				<div id="sage-pastel-panel" class="settings-panel has-feedback" style="display:none;">
					<img src="https://www.pastelmybusiness.co.za/resources/images_0003/FlatImages/NewLogo_001.png">
					<br><br>
					<p class="alert alert-success">The humble till can automatically upload your data to your My Business Online Account. No need to recapture information.</p>

					<div id="pastel-wrap">
						<?php if(empty($pastelhash)){ ?>
						<form id="pastel-setup-form" class="form-inline trigger well well-sm" data-modal="pastel" data-template="#pastel-companies-tmpl" data-modal-title="Connect to Pastel" data-call="pastel-companies" role="form" method="POST">
							<div class="form-group">
								<label class="sr-only" for="pemail">Email address</label>
								<input type="email" class="form-control" name="email" id="pemail" placeholder="Pastel email">
							</div>
							<div class="form-group">
								<label class="sr-only" for="ppass">Password</label>
								<input type="password" class="form-control" name="pass" id="ppass" placeholder="Password">
							</div>
							<button type="submit" class="btn btn-primary">Connect</button>
						</form>
						<br>
						<div class="">
							<h4 class="text-success">Don't have a Sage Pastel Account?</h4>
							<a href="https://www.pastelmybusiness.co.za/Signup/Default.aspx" target="_blank">
								<img class="img-responsive" src="//till.humble.co.za/static/site/images/sagepastelad.jpg">
							</a>
							<?php }else{ ?>

								<button id="pastel-setup-btn" class="btn btn-success trigger" data-modal="pastel" data-template="#pastel-companies-tmpl" data-modal-title="Setup to Pastel" data-call="pastel-companies" data-method="POST">Change Company</button>

							<?php } ?>
						</div>
					</div>


				</div>
			</div>
		</div>
	</div>
</div>
<span id="ean_modal_manager" class="trigger" data-callback="ean_manager" data-modal="ean_manage" data-modal-title="Edit Barcodes" data-cache-session="eans" data-modal-content="#eans-selector-tmpl"></span>
<span id="category_modal_selector" class="trigger" data-call="categories" data-modal="category_select" data-modal-title="Select Category" data-cache-session="categories" data-template="#category-selector-tmpl"></span>
<span id="producttype_modal_selector" class="trigger" data-modal="producttype_select" data-modal-title="Select Product Type" data-modal-content="#producttype-selector-tmpl"></span>

<script id="eans-selector-tmpl" type="text/html">
	<div class="list-group" id="ean_manager_list">
		{{#each ean}}
			{{#unless disabled}}
			<a href="#" class="list-group-item {{../../class}}" data-productguid="{{../productguid}}" data-before="edit_ean" data-method="POST" data-call="ean/{{guid}}" data-target="self" data-target-insert="replace" data-template="#eans-list-tmpl" data-active-class="ean-active" data-ean="{{ean}}" data-callback="updateEan" data-group="eanlist"><span class="ean-delete btn btn-xs btn-danger pull-right" style="padding: 3px 8px 1px 7px; margin: -1px -9px 0px 0px;"><i class="ean-delete glyphicon glyphicon-remove"></i></span><span class="ean-text">{{ean}}</span></a>
			{{/unless}}
		{{/each}}
	</div>
	<button class="btn btn-block btn-primary {{class}}" data-before="edit_ean" data-productguid="{{productguid}}" data-method="POST" data-call="ean/new" data-target="#ean_manager_list" data-target-insert="append" data-template="#eans-list-tmpl" data-active-class="ean-active" data-callback="updateEan" data-group="eanlist" type="button">Add Barcode</button>
</script>
<script id="pastel-companies-tmpl" type="text/html">
	<div class="list-group" id="ean_manager_list">
	{{#unless error}}
		{{#each Results}}
			<a href="#" class="list-group-item trigger" data-company="{{Name}}" data-pastelid="{{ID}}" data-hash="{{../../hash}}" data-method="POST" data-call="setpastelweb" data-callback="setup_pastel" data-target="self" data-active-class="pastel-active">{{Name}}</a>
		{{/each}}
	{{else}}
		<div class="alert alert-danger">{{error}}</div>
	{{/unless}}
	</div>
</script>
<script id="category-selector-tmpl" type="text/html">
	<div class="list-group list-sorted">
		{{#each categories}}
		<a href="#" class="list-group-item" onclick="set_category(this); return false;" data-value="{{cat}}">{{category}}</a>
		{{/each}}
	</div>
</script>
<script id="producttype-selector-tmpl" type="text/html">
	<div class="list-group list-sorted">
		<?php foreach($producttypes as $producttype){ ?>
		<a href="#" class="list-group-item" onclick="set_producttype(this); return false;" data-value="<?php echo $producttype->producttype; ?>"><?php echo $producttype->productdescr; ?></a>
		<?php } ?>
	</div>
</script>



<script id="products-list-tmpl" type="text/html">
	{{#each products}}
	<a href="#" class="list-group-item trigger" data-call="product/{{guid}}" data-active-class="prod-active" data-group="productslist" data-modal-animate="true" data-modal-title="{{descr}}" data-callback="verify_checks" data-target-insert="replace" data-template="#product-edit-tmpl" data-modal="true" data-title="{{descr}}">{{descr}}</a>
	{{/each}}
</script>
<script id="customers-list-tmpl" type="text/html">
	{{#each customers}}
	<a href="#" class="list-group-item trigger"  data-before="add_customer" data-method="POST" data-call="customer/{{guid}}" data-active-class="customer-active" data-callback="updatedCustomer" data-title="{{descr}}">{{descr}}</a>
	{{/each}}
</script>
<script id="suppliers-list-tmpl" type="text/html">
	{{#each suppliers}}
	<a href="#" class="list-group-item trigger" data-before="add_supplier" data-method="POST" data-call="supplier/{{guid}}" data-active-class="supplier-active" data-callback="updatedSupplier" data-title="{{descr}}">{{descr}}</a>
	{{/each}}
</script>

<script id="categories-list-tmpl" type="text/html">
	{{#each categories}}
	<a href="#" class="list-group-item trigger" data-before="edit_category" data-method="POST" data-call="category/{{guid}}" data-active-class="cat-active" data-callback="updateCategory" data-group="categorylist" data-title="{{category}}">{{category}}</a>
	{{/each}}
</script>

<script id="eans-list-tmpl" type="text/html">
	{{#unless disabled}}
		<a href="#" class="list-group-item trigger" data-productguid="{{productguid}}" data-before="edit_ean" data-method="POST" data-call="ean/{{guid}}" data-target="#ean_manager_list" data-target-insert="append" data-active-class="ean-active" data-ean="{{ean}}" data-callback="updateEan" data-group="eanlist"><span class="ean-delete btn btn-xs btn-danger pull-right" style="padding: 3px 8px 1px 7px; margin: -1px -9px 0px 0px;"><i class="ean-delete glyphicon glyphicon-remove"></i></span><span class="ean-text">{{ean}}</span></a>
	{{/unless}}
</script>


<script id="users-list-tmpl" type="text/html">
	{{#each users}}
	<a href="#" class="list-group-item trigger user_{{guid}} <?php if(empty($user->users)){ ?> list_users_check <?php } ?>" data-call="user/{{guid}}" data-active-class="active-user" data-group="userlist" data-modal-animate="true" data-modal-title="{{fname}} {{sname}}" data-target-insert="replace" data-callback="check_user_setting" data-template="#user-edit-tmpl" data-modal="true">{{fname}} {{sname}}</a>
	{{/each}}
</script>

<script id="community-list-tmpl" type="text/html">
	{{#each community}}
	<a href="#" class="list-group-item trigger" data-call="community/{{guid}}" data-active-class="active" data-group="communitylist" data-modal-animate="true" data-modal-title="{{description}}" data-target-insert="replace" data-template="#user-edit-tmpl" data-modal="true">{{description}}</a>
	{{/each}}
</script>

<script id="sites-list-tmpl" type="text/html">
	{{#each sites}}
	<a href="#" class="list-group-item trigger" data-call="site/{{guid}}" data-active-class="active" data-group="siteslist" data-modal-animate="true" data-modal-title="{{site_name}}" data-target-insert="replace" data-template="#site-edit-tmpl" data-modal="true">{{site_name}}</a>
	{{/each}}
</script>
<script id="printers-list-tmpl" type="text/html">
	{{#each this}}
		{{#if Address}}
		<a href="#" class="list-group-item" onclick="set_printer(this); return false;" data-value="{{Address}}">{{Address}}</a>
		{{else}}
			<div class="form-group">
				<label for="manual-printer-ip" class="col-sm-5 control-label">Printer IP Address</label>
				<div class="col-sm-7">
					<input type="text" class="form-control" id="manual-printer-ip" placeholder="Printer IP Address">
				</div>
			</div>
			<hr>
			<div id="printer-search-list" class="list-group"></div>
			<br>
			<a href="#" class="btn btn-block btn-success trigger" data-request="http://localhost:9200/list" data-timeout="2000000" data-error="printer_offline" data-template="#printers-list-tmpl" data-group="prnt" data-active-class="nope" data-target="#printer-search-list">Find Printers</a>
		{{/if}}
	{{/each}}

</script>


<script id="category-edit-tmpl" type="text/html">
	<div class="list-group category-edit-form" data-guid="{{guid}}">

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this); return false;" data-prompt="SKU" data-required="true">
			<span class="pull-right text-muted" data-field="stockcode"></span>
			SKU
		</a>
	</div>
</script>


<script id="product-edit-tmpl" type="text/html">
	<div class="list-group product-edit-form" data-call="general" data-guid="{{guid}}">

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'sku'); return false;" data-inline="product" data-prompt="SKU" data-required="true">
			<span class="pull-right text-muted" data-field="stockcode">{{stockcode}}</span>
			SKU
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'descr'); return false;" data-inline="product" data-prompt="Description" data-required="true">
			<span class="pull-right text-muted" data-field="descr">{{descr}}</span>
			Description
		</a>

		<a href="#" class="list-group-item product-field-item" data-guid="{{guid}}" data-inline="product" onclick="ean_input_field(this); return false;" data-required="true">
			<span class="pull-right text-muted ean-count"></span>
			Barcodes
		</a>

		<a href="#" class="list-group-item product-field-item" data-inline="product" onclick="category_input_field(this); return false;" data-required="true">
			<span class="pull-right text-muted" data-field="cat" data-value="{{cat}}">{{category}}</span>
			Category
		</a>

		<a href="#" class="list-group-item product-field-item" data-inline="product" onclick="toggle_field(this); return false;">
			<span class="pull-right text-muted" data-field="si" data-value="{{si}}"><i class="glyphicon glyphicon-ok remove_{{si}}"></i></span>
			Serial Product
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-inline="product" data-prompt="Cost">
			<span class="pull-right text-muted" data-field="cost">{{cost}}</span>
			Cost
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-inline="product" data-prompt="Sell">
			<span class="pull-right text-muted" data-field="sell">{{sell}}</span>
			Sell Incl
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-inline="product" data-prompt="Gross Profit">
			<span class="pull-right text-muted gp-field-val" data-field="gp"></span>
			Gross Profit
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-inline="product" data-prompt="Gross Profit %">
			<span class="pull-right text-muted gp-field-perc" data-field="gpp"></span>
			Gross Profit %
		</a>


		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-inline="product" data-prompt="Mark Up %">
			<span class="pull-right text-muted markup-field" data-field="mup"></span>
			Mark Up %
		</a>


		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-inline="product" data-prompt="VAT">
			<span class="pull-right text-muted" data-field="vat">{{vat}}</span>
			VAT %
		</a>


		<a href="#" class="list-group-item product-field-item" data-inline="product" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="virtual" data-value="{{virtual}}"><i class="glyphicon glyphicon-ok remove_{{virtual}}"></i></span>
			Virtual Product
		</a>

		<a href="#" class="list-group-item product-field-item" data-inline="product" onclick="producttype_input_field(this); return false;" data-required="true">
			<span class="pull-right text-muted" data-field="producttype" data-value="{{producttype}}">{{product_type}}</span>
			Product Type
		</a>


		<a href="#" class="list-group-item product-field-item" data-inline="product" onclick="toggle_field(this); return false;">
			<span class="pull-right text-muted" data-field="printlabel" data-value="{{printlabel}}"><i class="glyphicon glyphicon-ok remove_{{printlabel}}"></i></span>
			Print Label
		</a>

		<a href="#" class="list-group-item product-field-item" data-inline="product" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="live" data-value="{{live}}"><i class="glyphicon glyphicon-ok remove_{{live}}"></i></span>
			Live Item
		</a>


	</div>
</script>

<script id="product-new-tmpl" type="text/html">
	<div class="list-group product-edit-form" data-call="general" data-guid="new">

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'sku'); return false;" data-prompt="SKU" data-required="true">
			<span class="pull-right text-muted" data-field="stockcode"></span>
			SKU
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'descr'); return false;" data-prompt="Description" data-required="true">
			<span class="pull-right text-muted" data-field="descr"></span>
			Description
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="category_input_field(this); return false;" data-required="true">
			<span class="pull-right text-muted new-product-category" data-field="cat" data-value=""></span>
			Category
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="toggle_field(this); return">			
			<span class="pull-right text-muted" data-field="si" data-value=""></span>
			Serial Product
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-prompt="Cost">
			<span class="pull-right text-muted" data-field="cost">0.00</span>
			Cost
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-prompt="Sell">
			<span class="pull-right text-muted" data-field="sell">0.00</span>
			Sell Incl
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-prompt="Gross Profit">
			<span class="pull-right text-muted gp-field-val" data-field="gp">0.00</span>
			Gross Profit
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-prompt="Gross Profit %">
			<span class="pull-right text-muted gp-field-perc" data-field="gpp">0.00</span>
			Gross Profit %
		</a>


		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-prompt="Mark Up %">
			<span class="pull-right text-muted markup-field" data-field="mup">0.00</span>
			Mark Up %
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="text_input_field(this, 'money'); return false;" data-prompt="VAT">
			<span class="pull-right text-muted" data-field="vat">14.00</span>
			VAT %
		</a>


		<a href="#" class="list-group-item product-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="virtual" data-value=""></span>
			Virtual Product
		</a>

		<a href="#" class="list-group-item product-field-item" onclick="producttype_input_field(this); return false;" data-required="true">
			<span class="pull-right text-muted" data-field="producttype" data-value="<?php echo $producttypes[0]->producttype; ?>"><?php echo $producttypes[0]->productdescr; ?></span>
			Product Type
		</a>


		<a href="#" class="list-group-item product-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="printlabel" data-value="1"><i class="glyphicon glyphicon-ok"></i></span>
			Print Label
		</a>


		<a href="#" class="list-group-item product-field-item hide" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="live" data-value="1"></span>
			Live Item
		</a>

	</div>
</script>


<script id="user-new-tmpl" type="text/html">
	<div class="list-group user-edit-form" data-call="general" data-guid="new">

		<a href="#" class="list-group-item user-field-item" onclick="text_input_field(this); return false;" data-prompt="First Name" data-required="true">
			<span class="pull-right text-muted" data-field="fname"></span>
			First Name
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="text_input_field(this); return false;" data-prompt="Last Name" data-required="true">
			<span class="pull-right text-muted" data-field="sname"></span>
			Last Name
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="text_input_field(this, 'upper'); return false;" data-prompt="Email" data-required="true">
			<span class="pull-right text-muted" data-field="email"></span>
			Email
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="password_input_field(this); return false;" data-required="true">
			<span class="pull-right text-muted" data-field="pword" data-value=""></span>
			Password
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="return false;" data-required="true">
			<span class="pull-right text-muted random-code" data-field="cashierpin"></span>
			Cashier PIN
		</a>
		<?php if(!empty($user->users)){ ?>
		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="basket" data-value=""></span>
			Allow Basket
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="community" data-value=""></span>
			Allow Community
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="general" data-value=""></span>
			Allow General
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="move" data-value=""></span>
			Allow Move
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="products" data-value=""></span>
			Allow Products
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="reports" data-value=""></span>
			Allow Reports
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="users" data-value=""></span>
			Allow Users
		</a>
		<?php } ?>

	</div>
</script>

<script id="site-edit-tmpl" type="text/html">
	<div class="list-group site-edit-form" data-call="general" data-guid="{{guid}}">

		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Site Name" data-required="true">
			<span class="pull-right text-muted" data-field="sitename">{{sitename}}</span>
			Site Name
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Address Line 1" data-required="true">
			<span class="pull-right text-muted" data-field="address1">{{address1}}</span>
			Address Line 1
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Address Line 2" data-required="true">
			<span class="pull-right text-muted" data-field="address2">{{address2}}</span>
			Address Line 2
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Address Line 3" data-required="true">
			<span class="pull-right text-muted" data-field="addr3">{{addr3}}</span>
			Address Line 3
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Fax Number" data-required="true">
			<span class="pull-right text-muted" data-field="fax">{{fax}}</span>
			Fax Number
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Email" data-required="true">
			<span class="pull-right text-muted" data-field="email">{{email}}</span>
			Email
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Telephone Number" data-required="true">
			<span class="pull-right text-muted" data-field="tel">{{tel}}</span>
			Telephone Number
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Tax Number" data-required="true">
			<span class="pull-right text-muted" data-field="vatnr">{{vatnr}}</span>
			Tax Number
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Registration Number" data-required="true">
			<span class="pull-right text-muted" data-field="regnr">{{regnr}}</span>
			Registration Number
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this, true); return false;" data-inline="sites" data-prompt="Country Code" data-required="true">
			<span class="pull-right text-muted" data-field="countrycode">{{countrycode}}</span>
			Country Code
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Slip Line 1" data-required="true">
			<span class="pull-right text-muted" data-field="slipline1">{{slipline1}}</span>
			Slip Line 1
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Slip Line 2" data-required="true">
			<span class="pull-right text-muted" data-field="slipline2">{{slipline2}}</span>
			Slip Line 2
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-inline="sites" data-prompt="Slip Line 3" data-required="true">
			<span class="pull-right text-muted" data-field="slipline3">{{slipline3}}</span>
			Slip Line 3
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="toggle_field(this); return">
			<span class="pull-right text-muted" data-field="live" data-value=""><i class="glyphicon glyphicon-ok remove_{{live}}"></i></span>
			Live Site
		</a>

		
	</div>
</script>
<script id="site-new-tmpl" type="text/html">
	<div class="list-group site-edit-form" data-call="general" data-guid="new">

		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Site Name" data-required="true">
			<span class="pull-right text-muted" data-field="sitename"></span>
			Site Name
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Address Line 1" data-required="true">
			<span class="pull-right text-muted" data-field="address1">N/A</span>
			Address Line 1
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Address Line 2" data-required="true">
			<span class="pull-right text-muted" data-field="address2">N/A</span>
			Address Line 2
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Address Line 3" data-required="true">
			<span class="pull-right text-muted" data-field="addr3">N/A</span>
			Address Line 3
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Fax Number" data-required="true">
			<span class="pull-right text-muted" data-field="fax">N/A</span>
			Fax Number
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Email" data-required="true">
			<span class="pull-right text-muted" data-field="email">N/A</span>
			Email
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Telephone Number" data-required="true">
			<span class="pull-right text-muted" data-field="tel">N/A</span>
			Telephone Number
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Tax Number" data-required="true">
			<span class="pull-right text-muted" data-field="vatnr">N/A</span>
			Tax Number
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Registration Number" data-required="true">
			<span class="pull-right text-muted" data-field="regnr">N/A</span>
			Registration Number
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this, true); return false;" data-prompt="Country Code" data-required="true">
			<span class="pull-right text-muted" data-field="countrycode">ZAF</span>
			Country Code
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Slip Line 1" data-required="true">
			<span class="pull-right text-muted" data-field="slipline1">N/A</span>
			Slip Line 1
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Slip Line 2" data-required="true">
			<span class="pull-right text-muted" data-field="slipline2">N/A</span>
			Slip Line 2
		</a>
		
		<a href="#" class="list-group-item site-field-item" onclick="text_input_field(this); return false;" data-prompt="Slip Line 3" data-required="true">
			<span class="pull-right text-muted" data-field="slipline3">N/A</span>
			Slip Line 3
		</a>
		
		<a href="#" class="site-field-item" onclick="toggle_field(this); return" style="display:none;">
			<span class="pull-right text-muted" data-field="live" data-value="1"></span>
			Live Item
		</a>

		
	</div>
</script>

<script id="user-edit-tmpl" type="text/html">
	<div class="list-group user-edit-form" data-call="general" data-guid="{{uguid}}" style="padding-bottom: 4px;">

		<a href="#" class="list-group-item user-field-item" onclick="text_input_field(this); return false;" data-inline="user" data-prompt="First Name" data-required="true">
			<span class="pull-right text-muted" data-field="fname">{{fname}}</span>
			First Name
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="text_input_field(this); return false;" data-inline="user" data-prompt="Last Name" data-required="true">
			<span class="pull-right text-muted" data-field="sname">{{sname}}</span>
			Last Name
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="text_input_field(this, 'upper'); return false;" data-inline="user" data-prompt="Email" data-required="true">
			<span class="pull-right text-muted" data-field="email">{{email}}</span>
			Email
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="password_input_field(this); return false;" data-inline="user" data-required="true">
			<span class="pull-right text-muted" data-field="pword" data-value="{{pword}}">*******</span>
			Password
		</a>
		<?php if(!empty($user->users)){ ?>
		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return" data-inline="user">
			<span class="pull-right text-muted" data-field="basket" data-value="{{basket}}"><i class="glyphicon glyphicon-ok remove_{{basket}}"></i></span>
			Allow Basket
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return" data-inline="user">
			<span class="pull-right text-muted" data-field="community" data-value="{{community}}"><i class="glyphicon glyphicon-ok remove_{{community}}"></i></span>
			Allow Community
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return" data-inline="user">
			<span class="pull-right text-muted" data-field="general" data-value="{{general}}"><i class="glyphicon glyphicon-ok remove_{{general}}"></i></span>
			Allow General
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return" data-inline="user">
			<span class="pull-right text-muted" data-field="move" data-value="{{move}}"><i class="glyphicon glyphicon-ok remove_{{move}}"></i></span>
			Allow Move
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return" data-inline="user">
			<span class="pull-right text-muted" data-field="products" data-value="{{products}}"><i class="glyphicon glyphicon-ok remove_{{products}}"></i></span>
			Allow Products
		</a>

		<a href="#" class="list-group-item user-field-item" onclick="toggle_field(this); return" data-inline="user">
			<span class="pull-right text-muted" data-field="reports" data-value="{{reports}}"><i class="glyphicon glyphicon-ok remove_{{reports}}"></i></span>
			Allow Reports
		</a>

		<a href="#" class="list-group-item user-field-item allow-user-setting" onclick="toggle_field(this); return" data-inline="user">
			<span class="pull-right text-muted" data-field="users" data-value="{{users}}"><i class="glyphicon glyphicon-ok remove_{{users}}"></i></span>
			Allow Users
		</a>
		<?php } ?>

		<a href="#" class="user-field-item reset-pin btn btn-block btn-default" onclick="init_panel(); return false;" data-inline="user" data-required="true" style="width: 98%; margin: 7px 0px 10px 5px;">
			<span class="pull-right text-muted random-code" data-field="cashierpin" style="display:none;">{{cashierpin}}</span>
			Reset Cashier PIN
		</a>

	</div>

</script>


	<script type="text/javascript">

	var type_char;

		function reload_printers(){
			jQuery('#printer-trigger').trigger('click');
		}

		function new_product(){

			var categories = JSON.parse( sessionStorage['categories'] ),
				field = jQuery('.new-product-category');

				field.attr('data-value', categories.categories[0].cat).html(categories.categories[0].category);

			return {};

		}

		function focus_search(){

			var active	= jQuery('.list-group-item.active');
				panel	= jQuery(active.attr('href'));
			
			<?php if(empty($user->users)){ ?>
				jQuery('.user_<?php echo $user->uguid; ?>').removeClass('list_users_check');
				jQuery('.list_users_check').not().remove();
			<?php } ?>

			panel.find('.list-search').focus();			
		}

		/*function updateUser(el){

			var clicked = jQuery(el);
				parent = clicked.closest('.modal-content'),
				guid = parent.find('.user-edit-form').data('guid'),
				fields = parent.find('.user-field-item span'),
				data = {},
				stop = false;

				fields.each(function(k,v){

					var field = jQuery(v),
						value = (typeof field.data('value') !== 'undefined' ? field.data('value') : field.html() );

						field.parent().removeClass('list-group-item-danger');

						if(value.length < 1 && field.parent().data('required')){
							field.parent().addClass('list-group-item-danger');
							stop = true;
							return;
						}else{
							if(field.data('field')){
								data[field.data('field')] = value;
							}
						}

				});

				if(stop){
					return;
				}else{


					// starting save
					jQuery('#true_baldrickModalFooter').slideUp(200);
					jQuery('#true_baldrickModalBody').addClass('loading');

					//console.log(data);
					
					// do create user
					jQuery.post("http://api.humble.co.za/1.1/" + jQuery.cookie('token') + "/user/" + guid, data, function(data){
						console.log(data);


						var users = JSON.parse( sessionStorage['users'] ),
							found = false;

							
						
						for(var i = 0; i < users.users.length; i++){
							if( users.users[i].guid === data.guid ){


								users.users[i] = data;
								found = true;
								break;
							}
						};

						if(!found){
							users.users[users.users.length] = data;
						}

						sessionStorage.setItem('users', JSON.stringify( users ) );

						console.log(users);

						jQuery(".settings-list-nav .list-group-item.active").trigger('click');

						jQuery('#true_baldrickModal').modal('hide');

					});

					// trigger 

				}


			}
		*/
		function updatedCustomer(obj){

			var customers = JSON.parse( sessionStorage['customers'] ),
				found = false;
			
			for(var i = 0; i < customers.customers.length; i++){
				if( customers.customers[i].guid === obj.data.guid ){


					customers.customers[i] = obj.data;
					found = true;
					break;
				}
			};

			if(!found){
				customers.customers[customers.customers.length] = obj.data;
			}

			sessionStorage.setItem('customers', JSON.stringify( customers ) );


			jQuery(".settings-list-nav .list-group-item.active").trigger('click');


		}

		function updatedSupplier(obj){

			var suppliers = JSON.parse( sessionStorage['suppliers'] ),
				found = false;
			
			for(var i = 0; i < suppliers.suppliers.length; i++){
				if( suppliers.suppliers[i].guid === obj.data.guid ){


					suppliers.suppliers[i] = obj.data;
					found = true;
					break;
				}
			};

			if(!found){
				suppliers.suppliers[suppliers.suppliers.length] = obj.data;
			}

			sessionStorage.setItem('suppliers', JSON.stringify( suppliers ) );


			jQuery(".settings-list-nav .list-group-item.active").trigger('click');


		}


		function updateUser(el, inline){

			var clicked = jQuery(el);
				parent = clicked.closest('.modal-content'),
				guid = parent.find('.user-edit-form').data('guid'),
				fields = parent.find('.user-field-item span'),
				data = {},
				stop = false;

				fields.each(function(k,v){

					var field = jQuery(v),
						value = (typeof field.data('value') !== 'undefined' ? field.data('value') : field.html() );

						field.parent().removeClass('list-group-item-danger');

						if(value.length < 1 && field.parent().data('required')){
							field.parent().addClass('list-group-item-danger');
							stop = true;
							return;
						}else{
							if(field.data('field')){
								data[field.data('field')] = value;
							}
						}

				});

				if(stop){
					return;
				}else{


					// starting save
					jQuery('#true_baldrickModalFooter').slideUp(200);
					jQuery('#true_baldrickModalBody').addClass('loading');

					//console.log(data);
					
					// do create user
					jQuery.post("http://api.humble.co.za/1.1/" + jQuery.cookie('token') + "/user/" + guid, data, function(data){

						var users = JSON.parse( sessionStorage['users'] ),
							found = false;

							
						
						for(var i = 0; i < users.users.length; i++){
							if( users.users[i].guid === data.guid ){


								users.users[i] = data;
								found = true;
								break;
							}
						};

						if(!found){
							users.users[users.users.length] = data;
						}

						sessionStorage.setItem('users', JSON.stringify( users ) );


						jQuery(".settings-list-nav .list-group-item.active").trigger('click');

						jQuery(".settings-list-nav .list-group-item.active").trigger('click');
						if(!inline){
							jQuery('#true_baldrickModal').modal('hide');
						}else{
							jQuery('#true_baldrickModalBody').removeClass('loading');
							// check if this is the current user.
							//nav-item-main
							if(data.uguid === jQuery.cookie('user_guid')){
								// setup tabs.
								
								// toggle till
								if(data.basket === '1'){
									jQuery('.nav-item-main.icon-till,.nav-item-main.icon-cashup').removeClass('hidden').addClass('trigger').show();
									baldrickTrigger();
								}else{
									jQuery('.nav-item-main.icon-till,.nav-item-main.icon-cashup').hide();
								}
								// toggle inventory
								if(data.move === '1'){
									jQuery('.nav-item-main.icon-inv').removeClass('hidden').addClass('trigger').show();
									baldrickTrigger();
								}else{
									jQuery('.nav-item-main.icon-inv').hide();
								}
								// toggle reports
								if(data.reports === '1'){
									jQuery('.nav-item-main.icon-reports').removeClass('hidden').addClass('trigger').show();
									baldrickTrigger();
								}else{
									jQuery('.nav-item-main.icon-reports').hide();
								}
								// toggle products
								if(data.products === '1'){
									jQuery('#dashboard-nav').addClass('product-rights');
									jQuery('.has-product-rights').removeClass('hidden').addClass('trigger').show();
									baldrickTrigger();
								}else{
									jQuery('#dashboard-nav').removeClass('product-rights');
									jQuery('.has-product-rights').hide();
								}
								// toggle products
								if(data.community === '1'){
									jQuery('.has-community-rights').removeClass('hidden').addClass('trigger').show();
									baldrickTrigger();
								}else{
									jQuery('.has-community-rights').hide();
								}
								// toggle products
								if(data.general === '1'){
									jQuery('.has-general-rights').removeClass('hidden').show();
								}else{
									jQuery('.has-general-rights').hide();
								}
								


							}
						}


					});

					// trigger 

				}


			}

		function updateSite(el, inline){

			var clicked = jQuery(el);
				parent = clicked.closest('.modal-content'),
				guid = parent.find('.site-edit-form').data('guid'),
				fields = parent.find('.site-field-item span'),
				data = {},
				stop = false;

				fields.each(function(k,v){

					var field = jQuery(v),
						value = (typeof field.data('value') !== 'undefined' ? field.data('value') : field.html() );

						field.parent().removeClass('list-group-item-danger');

						if(value.length < 1 && field.parent().data('required')){
							field.parent().addClass('list-group-item-danger');
							stop = true;
							return;
						}else{
							if(field.data('field')){
								data[field.data('field')] = value;
							}
						}

				});

				if(stop){
					return;
				}else{


					// starting save
					jQuery('#true_baldrickModalFooter').slideUp(200);
					jQuery('#true_baldrickModalBody').addClass('loading');

					//console.log(data);
					
					// do create user
					jQuery.post("http://api.humble.co.za/1.1/" + jQuery.cookie('token') + "/site/" + guid, data, function(data){
						console.log(data);

						// change site name
						jQuery('.' + data.guid).html(data.sitename);


						var sites = JSON.parse( sessionStorage['sites'] ),
							found = false;

							
						
						for(var i = 0; i < sites.sites.length; i++){
							if( sites.sites[i].guid === data.guid ){


								sites.sites[i] = data;
								found = true;
								break;
							}
						};

						if(!found){
							sites.sites[sites.sites.length] = data;
						}

						sessionStorage.setItem('sites', JSON.stringify( sites ) );


						jQuery(".settings-list-nav .list-group-item.active").trigger('click');

						if(!inline){
							jQuery('#true_baldrickModal').modal('hide');
						}else{
							jQuery('#true_baldrickModalBody').removeClass('loading');
						}


					});

					// trigger 

				}


			}

		function updateProduct(el, inline){

			var clicked = jQuery(el);
				parent = clicked.closest('.modal-content'),
				guid = parent.find('.product-edit-form').data('guid'),
				fields = parent.find('.product-field-item span'),
				data = {},
				stop = false;

				fields.each(function(k,v){

					var field = jQuery(v),
						value = (typeof field.data('value') !== 'undefined' ? field.data('value') : field.html() );

						field.parent().removeClass('list-group-item-danger');

						if(value.length < 1 && field.parent().data('required')){
							field.parent().addClass('list-group-item-danger');
							console.log(field.data('value'));
							stop = true;
							return;
						}else{
							if(field.data('field')){
								data[field.data('field')] = value;
							}
						}

				});

				if(stop){
					return;
				}else{


					// starting save
					jQuery('#true_baldrickModalFooter').slideUp(200);
					jQuery('#true_baldrickModalBody').addClass('loading');

					
					// do create product					
					jQuery.post("http://api.humble.co.za/1.1/" + jQuery.cookie('token') + "/product/" + guid, data, function(data){

						var products = JSON.parse( sessionStorage['products'] ),
							found = false;
						
						for(var i = 0; i < products.products.length; i++){
							if( products.products[i].guid === data.guid ){


								products.products[i] = data;
								found = true;
								break;
							}
						};

						if(!found){
							products.products[products.products.length] = data.product;
						}

						sessionStorage['products'] = JSON.stringify( products );


						jQuery(".settings-list-nav .list-group-item.active").trigger('click');
						if(!inline){
							jQuery('#true_baldrickModal').modal('hide');
						}else{
							jQuery('#true_baldrickModalBody').removeClass('loading');
						}

					});

					// trigger 

				}

				

		}


		jQuery('#settings-panels').on('click', '.settings-list-nav a', function(e){
			e.preventDefault();
			var clicked = jQuery(this),
				panel = jQuery(clicked.attr('href'));

			jQuery('.settings-list-nav a').removeClass('active');
			jQuery('.settings-panel').hide();
			panel.show();
			clicked.addClass('active');
			if(panel.find('.list-sorted').length){
				sort_list(panel);
			}
		});


		jQuery('#settings-panels').on('keyup', '#modal_product_descr', function(){
			var text = this.value,
				field = jQuery(this);



			//jQuery('#products-panel .list-group-item.active').text(text);


		});

	function sort_list(panel){
		var panel		= panel.find('.list-sorted'),
			listkeys	= [],
			listobjs	= {},
			items		= panel.find('.list-group-item');

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

	

	// bind search products
	jQuery('#settings-panels').on('keyup', '.list-search', function(){

		var wrap = jQuery(this),
			str = wrap.val(),
			panel = wrap.parent(),
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

	function password_input_field(el, ev){

		var clicked = jQuery(el),
			field = clicked.find('span'),
			input = jQuery('<input type="password" class="form-control inline-field">'),
			prev = field.data('value');

			field.html('');
			input.appendTo(field);

			input.focus();

			input.on('blur keypress', function(e){
				if(e.type === 'keypress'){
					if(e.which === 13){
						e.preventDefault();
						jQuery(this).blur();
					}
					return;
				}else{
					var value = this.value, pword = jQuery(this), mask = "";
					
					if(!this.value.length){
						value = prev;
					}

					field.data('value', value);

					for(s=0; s< value.length; s++){
						mask += "*"
					}

					field.html(mask);
				}

			});
		
	}


	function text_input_field(el, swi){
		var clicked = jQuery(el),
			field = clicked.find('span'),
			parent = clicked.parent();
		
		// quick check on whats bing clicked
		if( ( field.data('field') === 'gpp' || field.data('field') === 'mup' ) && parseFloat( parent.find('[data-field="cost"]').html() ) === 0){
			return;
		}

		var value = prompt( clicked.data('prompt'), field.text() ),
			tryagain = false;


		if(value){
			
			if(swi == 'sku'){
				//value = value.toUpperCase();
				var products = JSON.parse( sessionStorage['products'] );
				// check if sku is uniue
				if(products.products){
					for(var i = 0; i < products.products.length; i++){
						if(products.products[i].stockcode.toLowerCase() == value.toLowerCase() && parent.data('guid') !== products.products[i].guid ){
							tryagain = true;
						}	
					}
				}
				value = value.toUpperCase();

			}else if(swi == 'upper'){
				value = value.toUpperCase();
			}else if(swi == 'descr'){
				//value = value.toUpperCase();
				var products = JSON.parse( sessionStorage['products'] );
				// check if sku is uniue
				if(products.products){
					for(var i = 0; i < products.products.length; i++){
						if(products.products[i].descr.toLowerCase() == value.toLowerCase() && parent.data('guid') !== products.products[i].guid ){
							tryagain = true;
						}
					}
				}

			}

			if(tryagain){
				alert(clicked.data('prompt') + ' needs to be unique');
				text_input_field(el, swi);
				return;
			}
			clicked.removeClass('list-group-item-danger');

			// check not gpp and cost cluase
			if( field.data('field') === 'gpp' && parseFloat( value ) >= 100 && parseFloat( parent.find('[data-field="cost"]').html() ) > 0){
				alert('GP % can only be 100% if there is no cost.');
				text_input_field(el, swi);
				return;
			}


			if(field.data('value')){				
				// value attribute based
				field.attr('data-value', value);
			}
			// html inner based
			field.html(value);

			if(swi == 'money'){
				value = parseFloat( value ).toFixed(2);
				field.html(value);
				var cost_obj,
					p_cost 		= parent.find('[data-field="cost"]'),
					p_sellinc 	= parent.find('[data-field="sell"]'),
					mark_f 		= parent.find('.markup-field'),
					p_vat 		= parent.find('[data-field="vat"]'),
					gpv_f 		= parent.find('.gp-field-val'),
					gpp_f 		= parent.find('.gp-field-perc');

				// pull in clalc
				cost_obj = formMath(p_cost.html(), p_sellinc.html(), p_vat.html(), field.data('field'), value);
				//console.log(cost_obj);
				gpv_f.html( isNaN( cost_obj.gp.toFixed(2) ) ?  '0.00' : cost_obj.gp.toFixed(2) );
				gpp_f.html( isNaN( cost_obj.gpp.toFixed(2) ) ?  '0.00' : cost_obj.gpp.toFixed(2) );
				mark_f.html( isNaN( cost_obj.mup.toFixed(2) ) ?  '0.00' : cost_obj.mup.toFixed(2) );
				p_sellinc.html( isNaN( cost_obj.sellinc.toFixed(2) ) ?  '0.00' : cost_obj.sellinc.toFixed(2) );


			}

		}else{
			return;
		}
		
		// check for inline
		if(clicked.data('inline')){
			if(clicked.data('inline') === 'product'){
				updateProduct(el, true)
			}
			if(clicked.data('inline') === 'user'){
				updateUser(el, true)
			}
			if(clicked.data('inline') === 'sites'){
				updateSite(el, true)
			}			
		}
	}

	function category_input_field(el){
		var clicked = jQuery(el),
			field = clicked.find('span'),
			current = jQuery('.active-field'),
			modaltrigger = jQuery('#category_modal_selector');

			current.removeClass('active-field');

			clicked.addClass('active-field');

			modaltrigger.trigger('click');

			sort_list(jQuery('#category_select_baldrickModalBody'));
		
	}

	function set_category(el){

		var clicked = jQuery(el),
			field = jQuery('.active-field span');

		field.parent().removeClass('list-group-item-danger');

		field.attr('data-value', clicked.data('value')).html(clicked.html());
		field.data('value', clicked.data('value'));

		jQuery('#category_select_baldrickModal').modal('hide');
		// check for inline

		if(field.parent().data('inline')){
			if(field.parent().data('inline') === 'product'){
				updateProduct(field.parent()[0], true)
			}
		}		

	}

	function producttype_input_field(el){
		var clicked = jQuery(el),
			field = clicked.find('span'),
			current = jQuery('.active-field'),
			modaltrigger = jQuery('#producttype_modal_selector');

			current.removeClass('active-field');

			clicked.addClass('active-field');

			modaltrigger.trigger('click');
		
	}

	function set_producttype(el){

		var clicked = jQuery(el),
			field = jQuery('.active-field span');

		field.parent().removeClass('list-group-item-danger');

		field.attr('data-value', clicked.data('value')).html(clicked.html());
		field.data('value', clicked.data('value'));
		
		jQuery('#producttype_select_baldrickModal').modal('hide');
		// check for inline
		if(field.parent().data('inline')){
			if(field.parent().data('inline') === 'product'){
				updateProduct(field.parent()[0], true)
			}
			if(field.parent().data('inline') === 'user'){
				updateUser(field.parent()[0], true)
			}
		}
	}

	function toggle_field(el){
		var clicked = jQuery(el),
			field	= clicked.find('span'),
			val = 0;

		if(field.html().length){
			field.html('').attr('data-value', '0').data('value', '0');
			val = 0;
		}else{
			field.html('<i class="glyphicon glyphicon-ok"></i>').attr('data-value', '1').data('value', '1');
			val = 1;
		}
		if(clicked.hasClass('local-settings')){
			localStorage.setItem(clicked.data('storekey'), val);
		}
		// check for inline
		if(clicked.data('inline')){
			if(clicked.data('inline') === 'product'){
				updateProduct(el, true)
			}
			if(clicked.data('inline') === 'user'){
				updateUser(el, true)
			}
			if(clicked.data('inline') === 'sites'){
				updateSite(el, true)
			}			

		}
	}

	function check_user_setting(obj){

		if(obj.rawData.uguid === jQuery.cookie('user_guid')){
			jQuery('.allow-user-setting').remove();
		}

		

		verify_checks(obj);
	}

	function verify_checks(obj){		
		
		// set check icons
		obj.data.find('.remove_0').remove();
		count_eans(obj.rawData.guid);

		// count stuff if a product modal
		if(obj.data.hasClass('product-edit-form')){
			var cost_obj,
				p_cost 		= obj.data.find('[data-field="cost"]'),
				p_sellinc 	= obj.data.find('[data-field="sell"]'),
				mark_f 		= obj.data.find('.markup-field'),
				p_vat 		= obj.data.find('[data-field="vat"]'),
				gpv_f 		= obj.data.find('.gp-field-val'),
				gpp_f 		= obj.data.find('.gp-field-perc');

			
			//console.log(p_cost);
			//console.log(p_sellinc);
			//console.log(p_vat);

			// pull in clalc
			cost_obj = formMath(p_cost.html(), p_sellinc.html(), p_vat.html(), 'cost', p_cost.html());
			//console.log(cost_obj);
			gpv_f.html( isNaN( cost_obj.gp.toFixed(2) ) ? '0.00' : cost_obj.gp.toFixed(2) );
			gpp_f.html( isNaN( cost_obj.gpp.toFixed(2) ) ? '0.00' : cost_obj.gpp.toFixed(2) );
			mark_f.html( isNaN( cost_obj.mup.toFixed(2) ) ? '0.00' : cost_obj.mup.toFixed(2) );
			p_sellinc.html( isNaN( cost_obj.sellinc.toFixed(2) ) ? '0.00' : cost_obj.sellinc.toFixed(2) );	
		}	
	}

	function count_eans(guid){
		// count EANS
		var eans = JSON.parse( sessionStorage['eans'] ),eancount = 0, text;

		if(eans.ean){
			for( var i = 0; i< eans.ean.length; i++){
				if( eans.ean[i].productguid === guid && !eans.ean[i].disabled){
					eancount += 1;
				}
			}
		}
		
		if(eancount > 0){
			text = eancount + (eancount === 1 ? ' Barcode' : ' Barcodes');

		}else{
			text = 'No Barcodes';
		}
		
		jQuery('.ean-count').html(text);

	}

	function ean_input_field(el){

		//console.log(el);
		var modal = jQuery("#ean_modal_manager"),
			trigger = jQuery(el);

		modal.data('guid', trigger.data('guid')).trigger('click');

	}

	function ean_manager(){
		
		var guid = this.trigger.data('guid'),
			eans = JSON.parse( sessionStorage['eans'] ),
			body = jQuery('#ean_manage_baldrickModalBody'),
			template = Handlebars.compile( body.html() );

		list = {
			ean				:	[],
			"class"			:	'trigger',
			"productguid"	:	guid
		};

		// filter out the eans for this product
		if(eans.ean){
			for( var i = 0; i < eans.ean.length; i++){
				if( eans.ean[i].productguid === guid ){
					if(!eans.ean[i].disabled){
						console.log(eans.ean[i]);
						list.ean.push(eans.ean[i]);
					}
				}
			}
		}
		body.html( template(list) );

		appTrigger = baldrickTrigger();
	}

	function edit_ean(el, ev){
		var clicked = jQuery(el),
			acttarget = jQuery(ev.target),
			value;

		if(clicked.is('button')){
			value = prompt('Barcode');
		}else{
			if(!acttarget.hasClass('ean-delete')){
				value = prompt('Barcode', clicked.data('ean'));
			}else{
				clicked.data('live', 0);
				return true;
			}
		}


		if(!value){
			return false;
		}

		if(value.length){
			if(!clicked.is('button')){
				clicked.data('ean', value).find('.ean-text').html(value);
			}else{
				clicked.data('ean', value);
			}
		}else{
			return false;
		}		
	}

	function updateEan(obj){

		var eans = JSON.parse( sessionStorage['eans'] ),found = false;		


		for( var i = 0; i< eans.ean.length; i++){
			if( eans.ean[i].guid === obj.rawData.guid ){
				eans.ean[i] = obj.rawData;
				found = true;

				// kill off the line if disabled
				if(obj.rawData.disabled){
					obj.params.trigger.remove();
				}

				break;
			}
		}

		if(found === false){
			eans.ean[eans.ean.length] = obj.rawData;
			
		}

		sessionStorage['eans'] = JSON.stringify( eans );
				
		count_eans(obj.rawData.productguid);
	}

	function add_customer(el){



		var clicked = jQuery(el),
			value;

		if(clicked.is('button')){
			 value = prompt('Customer Description');
		}else{
			value = prompt('Customer Description', clicked.html());
		}

		if(!value){
			return false;
		}

		if(value.length < 1){
			return false;
		}

		clicked.data('descr', value);

		return true;

	}

	function add_supplier(el){

		var clicked = jQuery(el),
			value;

		if(clicked.is('button')){
			 value = prompt('Supplier Description');
		}else{
			value = prompt('Supplier Description', clicked.html());
		}

		if(!value){
			return false;
		}

		if(value.length < 1){
			return false;
		}

		clicked.data('descr', value);

		return true;

	}

	function edit_category(el){
		var clicked = jQuery(el),
			value;

		if(clicked.is('button')){
			value = prompt('Category Name');
		}else{
			value = prompt('Category Name', clicked.html());
		}


		if(!value){
			return false;
		}

		if(value.length){
			if(!clicked.is('button')){
				clicked.data('category', value).html(value);
			}else{
				clicked.data('category', value);
			}
		}else{
			return false;
		}		
	}

	function updateCategory(obj){

		var categories = JSON.parse( sessionStorage['categories'] ),found = false;

		for( var i = 0; i< categories.categories.length; i++){
			if( categories.categories[i].guid === obj.data.guid ){
				categories.categories[i].category = obj.data.updated.category;
				found = true;
				break;
			}
		}

		if(found === false){
			categories.categories[categories.categories.length] = obj.data.updated;
		}

		sessionStorage['categories'] = JSON.stringify( categories );

		jQuery(".settings-list-nav .list-group-item.active").trigger('click');
	}


	function init_panel(obj){
		var pin = gen_pin_code(),
			field = jQuery('.random-code');

		field.html( pin );

		if(!obj){
			updateUser(field.parent()[0], true);
			alert('New pin is: ' + pin);
		}

	}

	function set_printer(el){
		var clicked = jQuery(el),
			line	= jQuery('.printer-setup'),
			printer = clicked.data('value');

		localStorage.setItem('humble-printer', printer );

		line.html(printer);

		jQuery('#printer_baldrickModal').modal('hide');

	}

	function printer_offline(el){
		
		jQuery('#printer_baldrickModalBody').html('<div class="alert alert-warning">Printer driver is not installed or is not running.</div>');
	}

	function setup_pastel(obj){

		jQuery('#pastel_baldrickModal').modal('hide');
		jQuery('#pastel-wrap').html('<button id="pastel-setup-btn" class="btn btn-success trigger" data-modal="pastel" data-template="#pastel-companies-tmpl" data-modal-title="Setup to Pastel" data-call="pastel-companies" data-method="POST">Change Company</button>');

	}

	function gen_pin_code(){

		var users = JSON.parse( sessionStorage['users'] ), pin = 0, run = true;

		while(run){

			run = false;
			pin = Math.round( ( Math.random() * 10000 ) ).toString();

			for( var i = 0; i < users.users.length; i++){				
				if(users.users[i].cashier_pin === pin || pin.length < 4){

					run = true;
					break;
				}
			}

		}

		return pin;

		//return 
	}

	/// MANUAL PRINTER IP ADDRESS
	function has_printer_check(el){
		var field = jQuery('.printer-setup'),
			ip = field.html().split('.'),
			input = jQuery('#manual-printer-ip');

		if(ip.length === 4){
			input.val(ip.join('.')).focus();
		}else{
			input.focus();
		}

	}

	var ipcheck;
	jQuery('body').on('keyup', '#manual-printer-ip', function(e){

		var ip = this.value.split('.'),
			group = jQuery(this).closest('.form-group'),
			field = jQuery('.printer-setup');

			group.removeClass('has-error').removeClass('has-success');
		if(ip.length === 4){
			if(ipcheck){
				clearTimeout(ipcheck);
			}
			ipcheck = setTimeout(function(){

				var send = {
					status: ip.join('.')
				};

				jQuery.get("http://localhost:9200", send, function(r){
					if(r.status){
						if(r.status === true){

							// send test
							var test = {
								printer: send.status,
								slip: "\u001b@\u001ba0\n\u001ba1Printer Successfully Connected\n\u001ba0------------------------------------------------\n\u001ba0\n\n\n\n\n\n\n\n\n\n\n\n\n\u001dV1\u001b@"
							};
							jQuery.post("http://localhost:9200", test, function(o){
								console.log(o);
							});
							group.addClass('has-success');
							field.html(send.status);

							localStorage.setItem('humble-printer', send.status);
						}else{
							group.addClass('has-error');
							field.html('not setup');
							localStorage.removeItem('humble-printer');
						}
					}else{
						group.addClass('has-error');
						field.html('not setup');
						localStorage.removeItem('humble-printer');
					}
				});

			}, 1000);
			
		}

	});







	/// QUICK PRINTER CHECK
	if(localStorage.getItem('humble-printer')){
		jQuery('.printer-setup').html(localStorage.getItem('humble-printer'));
	}
	jQuery('.local-settings').each(function(k,v){
		var field 	= jQuery(v),
			view 	= field.find('span'), 
			value 	= parseInt( localStorage.getItem(field.data('storekey')) );

		if(value === 1){
			view.html('<i class="glyphicon glyphicon-ok"></i>');
		}else{
			view.html('');
		}
	});
	


	</script>














