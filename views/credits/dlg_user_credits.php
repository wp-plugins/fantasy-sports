<div id="dlgUserCredits" style="display: none;">
    <div id="msgUserCredits" class="public_message"></div>
    <form id="formUserCredits">
        <table>
            <tr>
                <td style="width: 170px"><?=__("Name", FV_DOMAIN);?></td>
                <td class="full_name">User</td>
            </tr>
            <tr>
                <td><?=__("Total balance", FV_DOMAIN);?></td>
                <td class="total_balance">User</td>
            </tr>
            <tr>
                <td><?=__("Payment request pending", FV_DOMAIN);?></td>
                <td class="payment_request_pending">User</td>
            </tr>
        </table>
        <hr>
        <table>
            <tr>
                <td>
                    <?=__("Credits", FV_DOMAIN);?>
                    <input type="hidden" name="user_id" class="user_id" />
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="credits" size="50" />
                </td>
            </tr>
            <tr>
                <td><?=__("Reason", FV_DOMAIN);?></td>
            </tr>
            <tr>
                <td>
                    <textarea rows="5" cols="50" name="reason"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>