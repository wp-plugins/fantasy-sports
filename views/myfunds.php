<?php require_once('dlg_account_info.php');?>
<?php require_once('dlg_request_payment.php');?>
<h1><?=__('Account information');?></h1>
<div class="table">
    <div class="table_left"><?=__('Gateway');?></div>
    <div class="table_right"><?php if(isset($aUserPayment['gateway'])):?><?=$aUserPayment['gateway'];?><?php endif;?>&nbsp;</div>
</div>
<div class="table">
    <div class="table_left"><?=__('Email');?></div>
    <div class="table_right"><?php if(isset($aUserPayment['email'])):?><?=$aUserPayment['email'];?><?php endif;?>&nbsp;</div>
</div>
<div class="table">
    <div class="table_left"><?=__('Available balance');?></div>
    <div class="table_right"><?=$aUser['balance'];?></div>
</div>
<div class="table">
    <div class="table_left"><?=__('Pending request payment');?></div>
    <div class="table_right"><?=$withdrawPending;?>&nbsp;</div>
</div>
<a href="<?=FANVICTOR_URL_ADD_FUNDS;?>"><?=__('Add funds');?></a> | 
<a href="#" onclick="return jQuery.payment.requestPayment('Request payment')"><?=__('Request payment');?></a>