<?php
/*

Caldoza Engine ------------------------

File	:	templates/product-form.php
Created	: 	2013-12-06

*/

/*
{{guid}}
{{companyguid}}
{{stockcode}}
{{descr}}
{{cat}}
{{si}}
{{cost}}
{{sell}}
{{vat}}
{{brand}}
{{subtype}}
{{weight}}
{{parent}}
{{virtual}}
{{producttype}}
{{changed}}
{{insdate}}
{{live}}
{{message}}
*/
?>


	<form class="form-horizontal trigger" action="/" method="POST" data-modal-life="500" data-modals="true" data-target="#true_baldrickModalBody" data-callback="saveProductUpdate" data-template-null="#product-save-result-tmpl" data-modal-animate="true" data-call="product/{{guid}}">
		<div class="modal-body">
			<div class="form-group">
				<label class="col-md-2 col-lrg-2">Code</label>
				<div class="col-md-10 col-lrg-10">
					<input type="text" id="modal_product_stockcode" class="form-control" value="{{stockcode}}" name="stockcode">
				</div>
			</div>
			<div class="form-group">
				<label for="modal_product_descr" class="col-md-2 col-lrg-2">Product</label>
				<div class="col-md-10 col-lrg-10">
					<input type="text" data-guid="{{guid}}" id="modal_product_descr" class="form-control input-xlarge" value="{{descr}}" name="descr">
				</div>
			</div>
			<div class="form-group">
				<label for="modal_product_cat" class="col-md-2 col-lrg-2">Category</label>
				<div class="col-md-10 col-lrg-10">
					<select id="modal_product_cat" class="form-control" name="cat">
						<option value="{{cat}}">{{category}}</option>
						<option value="{{cat}}">--------------------</option>
						<option value="10">BATTERIES</option>
						<option value="11">CHARGERS</option>
						<option value="15">GAMING</option>
						<option value="0">HANDSET</option>
						<option value="3">LAPTOP</option>
						<option value="1">MODEM</option>
						<option value="14">OTHER</option>
						<option value="8">POUCHES</option>
						<option value="4">PREPAID SMARTDATA (SIM DEVICE)</option>
						<option value="5">PREPAID SMARTDATA (SIM ONLY)</option>
						<option value="2">ROUTER</option>
						<option value="13">SCREENGUARDS</option>
						<option value="12">SERVICES</option>
						<option value="6">STARTER PACK</option>
						<option value="9">TABLET</option>
						<option value="7">VOUCHER</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="modal_product_cost" class="col-md-2 col-lrg-2">Cost Excl</label>
				<div class="col-md-10 col-lrg-10">
					<input type="text" id="modal_product_cost" class="form-control" value="{{cost}}" name="cost">
				</div>
			</div>
			<div class="form-group">
				<label for="modal_product_sell" class="col-md-2 col-lrg-2">Sell Incl</label>
				<div class="col-md-10 col-lrg-10">
					<input type="text" id="modal_product_sell" class="form-control" value="{{sell}}" name="sell">
				</div>
			</div>
			<div class="form-group">
				<label for="modal_product_gpr" class="col-md-2 col-lrg-2">GP</label>
				<div class="col-md-10 col-lrg-10">
					<input type="text" readonly="" id="modal_product_gpr" class="form-control uneditable-input" name="">
				</div>
			</div>
			<div class="form-group">
				<label for="modal_product_gpp" class="col-md-2 col-lrg-2">GP %</label>
				<div class="col-md-10 col-lrg-10">
					<input type="text" readonly="" id="modal_product_gpp" class="form-control uneditable-input" name="">
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-offset-2 col-lrg-offset-2 col-md-10 col-lrg-10">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="si" id="modal_product_si" {{#if si}}checked="checked"{{/if}} > Serial Product
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="modal_product_vat" class="col-md-2 col-lrg-2">VAT Type</label>
				<div class="col-md-10 col-lrg-10">
					<select id="modal_product_vat" class="form-control" name="vat">
						<option value="0">0% VAT</option>
						<option selected="yes" value="14">14% VAT</option>
					</select>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-info">Save Changes</button>
		</div>
	</form>
	<script type="text/javascript">
		function saveProductUpdate(obj){
			$('tr.success').trigger('click');
			$('#true_baldrickModal').modal('hide');
		}
	</script>