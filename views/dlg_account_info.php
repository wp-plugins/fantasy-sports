<div id="dlgAccountInfo" style="display: none">
    <div id="msgAccountInfo" class="public_message"></div>
    <form id="formAccountInfo">
        <p>
            <?=__('Gateway');?>:<br/>
            <select name="val[gateway]">
                <?php foreach($aGateways as $aGateway):?>
                <option value="<?=$aGateway;?>" <?php if(isset($aUserPayment['gateway']) && $aUserPayment['gateway'] == $aGateway):?>selected=true"<?php endif;?>><?=$aGateway;?></option>
                <?php endforeach;?>
            </select>
        </p>
        <p>
            <?=__('Email');?>:<br/>
            <input type="text" name="val[email]" size="60" value="<?php if(isset($aUserPayment['email'])):?><?=$aUserPayment['email'];?><?php endif;?>" />
        </p>
    </form>
</div>