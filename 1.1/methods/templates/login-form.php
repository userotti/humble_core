<?php
/*

Caldoza Engine ------------------------

File	:	templates/login-form.php
Created	: 	2013-12-04

*/

?>
			<div class="row">
				<div class="col-sm-6 col-md-4 col-md-offset-4">
					<div class="account-wall">
						<div style="text-align: center; margin-bottom: 20px;"><img style="width:259px; height: 80px;" src="http://www.humble.co.za/images/humblelogo.png"></div>
						<form class="form-signin login_trigger" data-load-element=".account-wall" data-callback="humble_get_login" action="/login" method="POST">
							<span class="error-line"></span>
							<input type="text" name="email" class="form-control" placeholder="Email Address" required autofocus>
							<input type="password" name="password" class="form-control" placeholder="Password" required>
							<button class="btn btn-lg btn-primary btn-block" type="submit">
								Sign in</button>
							<a href="#" class="need-help">Need help? </a><span class="clearfix"></span>
						</form>
					</div>
					
				</div>
			</div>