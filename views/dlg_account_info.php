<div id="dlgAccountInfo" style="display: none">
    <div id="msgAccountInfo" class="public_message"></div>
    <form id="formAccountInfo">
        <?php if(get_option('fanvictor_payout_method') == 'paypal'):?>
            <p>
                <?=__('Gateway', FV_DOMAIN);?>:<br/>
                <select name="val[gateway]">
                    <?php foreach($aGateways as $aGateway):?>
                    <option value="<?=$aGateway;?>" <?php if(isset($aUserPayment['gateway']) && $aUserPayment['gateway'] == $aGateway):?>selected=true"<?php endif;?>><?=$aGateway;?></option>
                    <?php endforeach;?>
                </select>
            </p>
            <p>
                <?=__('Email', FV_DOMAIN);?>:<br/>
                <input type="text" name="val[email]" size="60" value="<?php if(isset($aUserPayment['email'])):?><?=$aUserPayment['email'];?><?php endif;?>" />
            </p>
        <?php else:?>
            <p>
                <?=__('Name');?> (<?=__('required', FV_DOMAIN);?>):<br/>
                <input type="text" name="val[name]" size="60" value="<?php if(isset($aUserPayment['name'])):?><?=$aUserPayment['name'];?><?php endif;?>" />
            </p>
            <p>
                <?=__('House/Deparment');?> (<?=__('required', FV_DOMAIN);?>):<br/>
                <input type="text" name="val[house]" size="60" value="<?php if(isset($aUserPayment['house'])):?><?=$aUserPayment['house'];?><?php endif;?>" />
            </p>
            <p>
                <?=__('Street');?> (<?=__('required', FV_DOMAIN);?>):<br/>
                <input type="text" name="val[street]" size="60" value="<?php if(isset($aUserPayment['street'])):?><?=$aUserPayment['street'];?><?php endif;?>" />
            </p>
            <p>
                <?=__('Unit number', FV_DOMAIN);?>:<br/>
                <input type="text" name="val[unit_number]" size="60" value="<?php if(isset($aUserPayment['unit_number'])):?><?=$aUserPayment['unit_number'];?><?php endif;?>" />
            </p>
            <p>
                <?=__('City');?> (<?=__('required', FV_DOMAIN);?>):<br/>
                <input type="text" name="val[city]" size="60" value="<?php if(isset($aUserPayment['city'])):?><?=$aUserPayment['city'];?><?php endif;?>" />
            </p>
            <p>
                <?=__('State/Provine');?> (<?=__('required', FV_DOMAIN);?>):<br/>
                <input type="text" name="val[state]" size="60" value="<?php if(isset($aUserPayment['state'])):?><?=$aUserPayment['state'];?><?php endif;?>" />
            </p>
            <p>
                <?=__('Country');?> (<?=__('required', FV_DOMAIN);?>):<br/>
                <input type="text" name="val[country]" size="60" value="<?php if(isset($aUserPayment['country'])):?><?=$aUserPayment['country'];?><?php endif;?>" />
            </p>
        <?php endif;?>
    </form>
</div>