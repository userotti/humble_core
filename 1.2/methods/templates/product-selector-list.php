<?php
/*

Caldoza Engine ------------------------

File	:	templates/product-selector-list.php
Created	: 	2013-12-04

*/


?>
				<div class="input-group">
					<input type="text" class="form-control" data-event="keyup" data-before="filter_products" class="product_searcher">
				</div>
				<table class="table table-condensed table-hover" id="products-table" style="">
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
						<tr data-guid="{{guid}}" data-active-class="success" data-load-element="#addprod_baldrickModalBody" ndata-template="#product-line-tmpl" data-callback="addToBasket" class="trigger pagination-item {{rowclass @index}} live{{live}}">
							<td class="search-field">{{stockcode}}</td>
							<td class="search-field">{{descr}}</td>
							<td style="text-align:right">{{sell}}</td>
						</tr>
						{{/each}}
					</tbody>
				</table>
				<div class="panel-body">
					<ul id="paginator"></ul>
				</div>
