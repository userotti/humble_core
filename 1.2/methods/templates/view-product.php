<?php
/*

Caldoza Engine ------------------------

File	:	templates/view-product.php
Created	: 	2013-12-06

*/




?>
<div class="panel panel-default">
	<div class="panel-heading">
		{{descr}}
	</div>
	
		<table class="table table-bordered">
			<tbody>
				<tr>
					<th style="width: 150px;">Code</th>
					<td>{{stockcode}}</td>
				</tr>
				<tr>
					<th>Product</th>
					<td>{{descr}}</td>
				</tr>
				<tr>
					<th>Category</th>
					<td>{{category}}</td>
				</tr>
				<tr>
					<th>Cost Excl.</th>
					<td>{{cost}}</td>
				</tr>
				<tr>
					<th>Sell Incl.</th>
					<td>{{sell}}</td>
				</tr>
			</tbody>
		</table>
	
	<div class="panel-footer">
		<button  data-call="product/{{guid}}" data-active-class="success" data-modal-animate="true" data-modal-title="{{descr}}" data-target-insert="replace" data-template-url="template/product-form" data-modal="true" class="trigger btn btn-primary">Edit Product</button>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		EAN
	</div>
	
		<table class="table">
			<tbody>
				<tr>
					{{#if ean}}
					{{#each ean}}
					<td>{{ean}}</td>
					<td style="width: 50px;">{{#if live}}Live{{/if}}</td>
					{{/each}}
					{{else}}
					<td>This product does not have an EAN</td>
					{{/if}}
				</tr>
			</tbody>
		</table>
	
	<div class="panel-footer">
		<button  data-call="product/{{guid}}" data-active-class="success" data-template-url="template/product-form" data-modal-buttons="Close|dismiss" data-modal="true" data-modal-title="{{descr}}" class="disabled-trigger btn btn-primary">Add EAN</button>
	</div>
</div>

