<div class="contentPlugin">
    <?php getMessage();?>
    <?php if($canplay):?>
        <?php if($aGateways != null):?>
        <div id="msgAddCredits" class="public_message"></div>
        <form id="formAddCredits">
            <p>
                <?=__('Rate');?>: $1 <?=__('deposit equals', FV_DOMAIN);?> <?=get_option('fanvictor_cash_to_credit');?> <?=__('credits');?>
            </p>
            <p>
                <?=__('How many credits do you want to add', FV_DOMAIN);?> (<?=sprintf(__('minimum $%s'), get_option('fanvictor_minimum_deposit'));?>):<br/>
                <input type="text" name="credits" />
            </p>
            <?php if($isHasCoupon):?>
            <p>
                <?=__('Coupon code', FV_DOMAIN);?>:<br/>
                <input type="text" name="coupon_code" />
            </p>
            <?php endif;?>
            <p>
                <?=__('Gateway', FV_DOMAIN);?>:<br/>
                <select name="gateway">
                    <?php foreach($aGateways as $aGateway):?>
                    <option value="<?=$aGateway;?>"><?=$aGateway;?></option>
                    <?php endforeach;?>
                </select>
            </p>
            <br/>
            <div id="dp-data" style="display: none;border-top: 2px dotted #ddd">
                <p>
                    <?=__('Firstname', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[fname]" value=""/>
                </p>
                <p>
                    <?=__('Lastname', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[lname]" value=""/>
                </p>
                <p>
                    <?=__('Company', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[company]" value=""/>
                </p>
                <p>
                    <?=__('Address', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[address]" value=""/>
                </p>
                <p>
                    <?=__('City', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[city]" value=""/>
                </p>
                <p>
                    <?=__('State', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[state]" value=""/>
                </p>
                <p>
                    <?=__('Zip', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[zip]" value=""/>
                </p>
                <p>
                    <?=__('Country', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[country]" value=""/>
                </p>
                <p>
                    <?=__('Phone', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[phone]" value=""/>
                </p>
                <p>
                    <?=__('Fax', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[fax]" value=""/>
                </p>
                <p>
                    <?=__('Email', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[email]" value=""/>
                </p>
                <p>
                    <?=__('Credit card number', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[cc]" value=""/>
                </p>
                <p>
                    <?=__('Credit card expiration date', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[ccexp]" value="" maxlength="5"/>
                </p>
                <!--<p>
                    <?=__('Website', FV_DOMAIN);?>
                    <br/>
                    <input name="dp[website]"/>
                </p>-->
            </div>
            <input type="submit" class="button" value="<?=__('Add', FV_DOMAIN);?>" onclick="jQuery.payment.sendCredits()" />
            <span class="waiting" style="display: none"><?=__('Please wait...', FV_DOMAIN);?></span>
        </form>
        <script type="text/javascript">
        (function($){
            $('select[name="gateway"]').change(function(){
                if($(this).val()=="CHOICE")
                    $("#dp-data").show();
                })
        })(jQuery);
        </script>
        <?php else:?> 
            <?=__("There are no available gateways", FV_DOMAIN);?>
        <?php endif;?>
    <?php else:?> 
        <?=__("Due to your location you cannot play in paid games so that they cannot add funds", FV_DOMAIN);?>
    <?php endif;?>
</div>
