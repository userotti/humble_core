<?php
/*

Caldoza Engine ------------------------

File	:	site/front-page.php
Created	: 	2013-12-04

*/

if(!isset($_COOKIE['deviceGUID'])){
	setcookie ("deviceGUID", gen_uuid(), time() + 3600 * 24 * 24 * 24);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>humble</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Bootstrap -->
		<link href="/static/site/css/bootstrap.css" rel="stylesheet">
		<link rel="stylesheet" href="/static/site/css/theme.css">
		<link rel="stylesheet" href="/static/site/css/theme-elements.css">
		<link rel="stylesheet" href="/static/site/css/theme-animate.css">
		<link rel="stylesheet" href="/static/site/css/skins/humble.css">		
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
		<style>
.form-signin
{
    max-width: 330px;
    padding: 15px;
    margin: 0 auto;
    padding-top: 5px;
}
.form-signin .form-signin-heading, .form-signin .checkbox
{
    margin-bottom: 10px;
}
.form-signin .checkbox
{
    font-weight: normal;
}
.form-signin .form-control
{
    position: relative;
    font-size: 16px;
    height: auto;
    padding: 10px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
.form-signin .form-control:focus
{
    z-index: 2;
}
.form-signin input[type="password"],
.form-signin input[type="text"]
{
    margin-bottom: 10px;
}
.account-wall
{
    margin-top: 80px;
    padding: 0 0px 20px 0px;
    background: #F7F7F7;
    -moz-box-shadow: 0 2px 2px rgba(0, 0, 0, 0.3), 0 80px 0 #98BF35 inset;
    -webkit-box-shadow: 0 2px 2px rgba(0, 0, 0, 0.3), 0 80px 0 #98BF35 inset;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.3), 0 80px 0 #98BF35 inset;
    border-radius: 5px;
}
.login-title
{
    color: #555;
    font-size: 18px;
    font-weight: 400;
    display: block;
}
.profile-img
{
    width: 96px;
    height: 96px;
    margin: 0 auto 10px;
    display: block;
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    border-radius: 50%;
}
.profile-name {
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    margin: 10px 0 0;
    height: 1em;
}
.profile-email {
    display: block;
    padding: 0 8px;
    font-size: 15px;
    color: #404040;
    line-height: 2;
    font-size: 14px;
    text-align: center;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
.need-help
{
    display: block;
    margin-top: 10px;
}
.new-account
{
    display: block;
    margin-top: 10px;
}
.login-body{
	background: url("/static/site/images/bluelady.jpg") top center;
	background-size: 100% auto;
	background-repeat: no-repeat;
}
/*#main-panel {padding-top: 10px;}*/
body { margin-top: 95px; }
.sloading > * {opacity: .4;}
.sloading{display:block; min-height:120px; background:url("/static/site/images/loader.gif") no-repeat center center transparent; background-size: 16px auto;}
.navbar-primary .navbar-brand {
  padding-left: 15px;
  padding-right: 15px;
}
.pagination-item {cursor: pointer;}
</style>
	</head>
	<body class="sticky-menu-active">
		<div class="body" id="main-panel">
		  <header class="single-menu flat-menu">
		    <div class="container">
		      <h1 class="logo logo-sticky-active">
		        <a href="index.html">
		          <img alt="humble" src="http://www.humble.co.za/images/humblelogo.png" style="height: 40px; display: inline-block; top: 28px;">
		        </a>
		      </h1>
		      <button class="btn btn-responsive-nav btn-inverse" data-toggle="collapse" data-target=".nav-main-collapse">
		        <i class="icon icon-bars"></i>
		      </button>
		    </div>
		    <div class="navbar-collapse nav-main-collapse collapse">
		      <div class="container">
		        <nav class="nav-main mega-menu">

		          <ul class="nav nav-pills nav-main" id="mainMenu">
		            <li data-animate="up" class="dropdown trigger" data-request="template/login-form" data-target="#content-panel"><a href="#">Log In</a></li>
		          </ul>
		        </nav>
		      </div>
		    </div>
		  </header>





		  <div role="main" class="main">
		    <div id="content" class="content full main-content">
		      <div class="home">
		        <div class="container" id="content-panel">


		        </div>
		      </div>

		    </div>
		  </div>
		</div>
		
		<span id="load-dashboard" data-animate="true" class="trigger" data-call="dashboard" data-target="#main-panel"></span>


		<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="/static/site/js/jquery.cookie.js"></script>
		<script src="/static/site/js/libs/bootstrap.min.js"></script>
		<script src="/static/site/js/paginator.min.js"></script>
		<script src="/static/site/js/libs/handlebars.js"></script>
		<script src="/static/site/js/jquery.baldrick.js"></script>
		<script src="/static/site/js/plugins/handlebars.baldrick.js"></script>
		<script src="/static/site/js/plugins/modal.baldrick.js"></script>
		<script src="/static/site/js/plugins/animate.baldrick.js"></script>
		<script type="text/javascript">
			var listLimit = 15;
			// helpers
			Handlebars.registerHelper('rowclass', function(idx){
				var page = Math.ceil((idx+1)/listLimit);
				return 'table-page-'+page;
			});

			
			function humble_get_login(obj){
				if(obj.data.message){
					if(obj.data.message === 'OK'){
						jQuery.cookie('token', obj.data.token_guid);
						jQuery('#load-dashboard').trigger('click');
						jQuery('body').removeClass('login-body');
					}else{
						jQuery('.error-line').html('<div class="alert alert-danger">'+obj.data.message+'</div>');
					}
				}
			}
			function humble_do_logout(){
				jQuery.removeCookie('token');
				jQuery('body').addClass('login-body');
				return true;
			}
			function set_pagination(obj){

				if( Math.ceil((obj.rawData.total)/listLimit) === 1){
					jQuery('#paginator').html('');
					return;
				}

				jQuery('#paginator').bootstrapPaginator({
					size: 'small',
					alignment: 'center',
					currentPage	:	1,
					bootstrapMajorVersion: 3,
					totalPages	:	Math.ceil((obj.rawData.total)/listLimit),
					numberOfPages: 10,
					onPageClicked: function(ev,oev,type,page){
						jQuery('.pagination-item').hide();
						jQuery('.table-page-'+page).show();
					}
				});
				jQuery('.pagination-item').hide();
				jQuery('.table-page-1').show();
			}

			jQuery(function($){

				$('.trigger').baldrick({
					helper	:	{
						event	:	function(obj){
							var $trigger = $(obj);
							if($trigger.data('call')){
								$trigger.attr('data-request', '/'+jQuery.cookie('token')+'/'+$trigger.data('call'));
							}
						}
					}
				});

				if(!jQuery.cookie('token')){
					//$('#get-login').trigger('click');
					//jQuery('body').addClass('login-body');
				}else{
					$('#load-dashboard').trigger('click');
				}

			});


		</script>
	</body>
</html>