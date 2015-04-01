<div id="dlgUserWithdrawls" style="display: none">
    <div id="msgUserWithdrawls" class="public_message"></div>
    <form id="formUserWithdrawls">
        <table>
            <tr>
                <td style="width: 170px"><?=__("Name");?></td>
                <td class="full_name"></td>
            </tr>
            <tr>
                <td><?=__("Amount");?></td>
                <td class="amount"></td>
            </tr>
            <tr>
                <td><?=__("Real Amount");?></td>
                <td class="real_amount"></td>
            </tr>
            <tr>
                <td><?=__("Request Date");?></td>
                <td class="request_date"></td>
            </tr>
            <tr>
                <td><?=__("Reason");?></td>
                <td class="reason"></td>
            </tr>
        </table>
        <hr>
        <table>
            <tr>
                <td>
                    <?=__("Action");?>
                    <input type="hidden" name="withdrawlID" class="withdrawlID" />
                </td>
            </tr>
            <tr>
                <td>
                    <select name="status" class="status">
                        <option value="APPROVED"><?=__("APPROVED");?></option>
                        <option value="DECLINED"><?=__("DECLINED");?></option>
                    </select>
                </td>
            </tr>
            <?php if(get_option('fanvictor_payout_method') == 'paypal'):?>
            <tr>
                <td>
                    <?=__("Gateway");?>
                    <input type="hidden" name="withdrawlID" class="withdrawlID" />
                </td>
            </tr>
            <tr>
                <td>
                    <select name="gateway">
                        <?php foreach($aGateways as $aGateway):?>
                        <option value="<?=$aGateway;?>"><?=$aGateway;?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <?php endif;?>
            <tr>
                <td><?=__("Response Message");?></td>
            </tr>
            <tr>
                <td>
                    <textarea rows="5" cols="50" name="response_message" class="response_message"></textarea>
                </td>
            </tr>
        </table>
    </form>
    <form id="paypalCheckout" action="" method="post">
        <input type="hidden" name="cmd" value="_xclick" />
        <input type="hidden" name="business" value="" />
        <input type="hidden" name="quantity" value="1" />
        <input type="hidden" name="item_name" value="" />
        <input type="hidden" name="item_number" value="1" />
        <input type="hidden" name="amount" value="" />
        <input type="hidden" name="currency_code" value="USD" />
        <input type="hidden" name="cancel_return" value="" />
		<input type="hidden" name="notify_url" value="" />
        <input type="hidden" name="return" value="" />
		<input type="hidden" name="custom" value="" />
    </form>
</div>