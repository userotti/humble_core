<?php
/*

Caldoza Engine ------------------------

File	:	templates/dashboard.php
Created	: 	2013-12-04

*/


$title = '<a class="navbar-brand trigger pull-right" data-autoload="true" data-call="sites" data-modal-title="Sites" data-target-insert="replace" data-modal="site-select" data-template-url="template/sites-list" href="#">humble</a>';
if(!empty($user->siteguid)){
  $site = $db->get_var($db->prepare("

  SELECT
    `sitename`
  FROM
    `sites`
  WHERE
    `coguid` = %s
  AND
    `guid` = %s
      
    ", $user->cguid, $user->siteguid));

  $title = '<span class="navbar-brand '.$user->siteguid.'">'.$site.'</span>';
}

?>
<style>
  .hide_<?php echo $user->siteguid; ?>{
    display: none;
  }
</style>
<nav class="navbar navbar-primary" role="navigation">
  <div class="activity-status"></div>
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header pull-right">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <?php echo $title; ?>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse <?php if(!empty($user->products)){ echo 'product-rights'; } ; ?>" id="dashboard-nav">
    <ul class="nav navbar-nav icon-navbar">
    <?php if(!empty($user->siteguid)){ ?>
    <li class="trigger icon-lock" data-group="nav-bar" data-callback="humble_do_lock" data-call="lock_till" data-target="#main-panel"><a id="lock-till" href="#">Lock</a></li>
    <li class="nav-item-main <?php if(!empty($user->basket)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?> icon-till" data-group="nav-bar" data-call="template/till" data-cache-session="till_tmpl" data-target-insert="tab" data-target="#app-window"><a id="nav-till" href="#">Till</a></li>
    <li class="nav-item-main <?php if(!empty($user->move)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?> icon-inv" data-group="nav-bar" data-call="template/inventory" data-cache-session="inventory_tmpl" data-target-insert="tab" data-target="#app-window" data-callback="set_pagination" data-summary="true"><a id="nav-inv" href="#">Inventory</a></li>
    <li class="nav-item-main <?php if(!empty($user->reports)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?> icon-reports" data-group="nav-bar" data-cache-session="reports_tmpl" data-call="template/reports" data-target-insert="tab" data-target="#app-window"><a id="nav-reports" href="#">Reports</a></li>
    <li class="nav-item-main <?php if(!empty($user->basket)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?> icon-cashup" data-group="nav-bar" data-call="template/cashup" data-cache-session="cashup_tmpl" data-target-insert="tab" data-target="#app-window"><a id="nav-cash-up" href="#">Cash Up</a></li>
    <li class="nav-item-main <?php if(!empty($user->settings)){ echo 'trigger'; }else{ echo 'hidden'; } ; ?> icon-settings" data-group="nav-bar" data-cache-session="settings_tmpl" data-call="template/settings" data-target-insert="tab" data-target="#app-window"><a id="nav-settings" href="#">Settings</a></li>
    <!-- <li class="trigger" data-call="pastel-products" data-callback="void_log"><a href="#">Sync Companies</a></li> -->
    <!-- <li class="trigger" data-call="suppliers" data-callback="void_log"><a href="#">Sync Suppliers</a></li> -->
    <?php }else{ ?>
    <li class="trigger" data-call="sites" data-cache-session="sites" data-modal-title="Sites" data-target-insert="replace" data-modal="site-select" data-template-url="http://humble.co.za/template/sites-list"><a href="#">Select Site</a></li>
    <?php } ?>
    </ul>

  </div><!-- /.navbar-collapse -->
</nav>
<span id="product-sync" class="trigger" data-call="products" data-cache-purge="true" data-cache-session="products"></span>
<div class="row main-content" id="app-window">
</div>
<script type="text/javascript">

  function void_log(obj){
    console.log(obj);
  }
  function product_sync(){
    jQuery('#product-sync').trigger('click');
  }
  jQuery('title').html('humble | <?php echo $user->fname.' '.$user->sname; ?>');
  jQuery('.nav-item-main.trigger').first().attr('data-autoload', 'true');

</script>



