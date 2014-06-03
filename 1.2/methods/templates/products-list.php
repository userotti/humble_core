<?php
/*

Caldoza Engine ------------------------

File	:	templates/products-list.php
Created	: 	2013-12-04

*/




?>
<div class="col-md-6 col-lrg-6">
    <div id="left-panel">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lrg-6">
						Products
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lrg-6">
						<div class="input-group input-group-sm pull-right">
							<input type="text" class="form-control" data-event="keyup" data-before="filter_products">
							<span class="input-group-btn">
								<button class="btn btn-default"  type="button">Search</button>
							</span>
						</div>

					</div>
				</div>
			</div>
			
				<?php /*
				<div class="list-group">
					{{#each products}}
					<a href="#" data-call="product/{{guid}}" data-template-url="template/product-form" data-modal="true" data-modal-title="{{descr}}" sdata-target="#product-right-panel" class="trigger list-group-item {{rowclass @index}}" >
						<span class="badge">{{sell}}</span>
						{{descr}}
						<div><small>{{stockcode}}</small></div>
					</a>
					{{/each}}
				</div>
				<ul id="paginator"></ul>
				*/ ?>
				<table class="table table-condensed table-striped table-hover" id="products-table" style="">
					<thead>
						<tr>
							<th>Code</th>
							<th>Product</th>
							<th>Sell Inc.</th>
							<th>On Hand</th>
						</tr>
					</thead>
					<tbody id="table_product">
						{{#each products}}
						<tr data-call="product/{{guid}}" data-animate="true" data-active-class="success" data-group="products-list" data-template-url="template/view-product" data-modal-title="{{descr}}" data-target="#product-right-panel" class="trigger pagination-item {{rowclass @index}} live{{live}}">
							<td class="search-field">{{stockcode}}</td>
							<td class="search-field">{{descr}}</td>
							<td style="text-align:right">{{sell}}</td>
							<td style="text-align:center">{{on_hand}}</td>
						</tr>
						{{/each}}
					</tbody>
				</table>
			<div class="panel-body">
				<ul id="paginator"></ul>
			</div>
		</div>
    </div>
  </div>
  <div class="col-md-6 col-lrg-6">
    <div id="product-right-panel">
    </div>
  </div>