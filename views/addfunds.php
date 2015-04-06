<div class="contentPlugin">
    <?php getMessage();?>
    <h1><?=__('Add Funds', FV_DOMAIN);?></h1>
    <?php if($canplay):?>
        <div id="msgAddCredits" class="public_message"></div>
        <form id="formAddCredits">
            <p>
                <?=__('Rate');?>: $1 <?=__('deposit equals', FV_DOMAIN);?> <?=get_option('fanvictor_cash_to_credit');?> <?=__('credits');?>
            </p>
            <p>
                <?=__('How many credits do you want to add', FV_DOMAIN);?> (<?=sprintf(__('minimum $%s'), get_option('fanvictor_minimum_deposit'));?>):<br/>
                <input type="text" name="credits" />
            </p>
            <p>
                <?=__('Gateway', FV_DOMAIN);?>:<br/>
                <select name="gateway">
                    <?php foreach($aGateways as $aGateway):?>
                    <option value="<?=$aGateway;?>"><?=$aGateway;?></option>
                    <?php endforeach;?>
                </select>
            </p>
            <br/>
            <input type="submit" class="button" value="<?=__('Add', FV_DOMAIN);?>" onclick="jQuery.payment.sendCredits()" />
            <span class="waiting" style="display: none"><?=__('Please wait...', FV_DOMAIN);?></span>
        </form>
    <?php else:?> 
        <?=__("Due to your location you cannot play in paid games so that they cannot add funds", FV_DOMAIN);?>
    <?php endif;?>
</div>
