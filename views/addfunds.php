<?php getMessage();?>
<h1><?=__('Add Funds');?></h1>
<div id="msgAddCredits" class="public_message"></div>
<form id="formAddCredits">
    <p>
        <?=__('Rate');?>: $1 <?=__('deposit equals');?> <?=get_option('fanvictor_cash_to_credit');?> <?=__('credits');?>
    </p>
    <p>
        <?=__('How many credits do you want to add');?>:<br/>
        <input type="text" name="credits" />
    </p>
    <p>
        <?=__('Gateway');?>:<br/>
        <select name="gateway">
            <?php foreach($aGateways as $aGateway):?>
            <option value="<?=$aGateway;?>"><?=$aGateway;?></option>
            <?php endforeach;?>
        </select>
    </p>
    <br/>
    <input type="submit" class="button" value="Add" onclick="jQuery.payment.sendCredits()" />
    <span class="waiting" style="display: none"><?=__('Please wait...');?></span>
</form>