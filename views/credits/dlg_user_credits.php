<div id="dlgUserCredits" style="display: none;">
    <div id="msgUserCredits" class="public_message"></div>
    <form id="formUserCredits">
        <table>
            <tr>
                <td style="width: 170px"><?=__("Name");?></td>
                <td class="full_name">User</td>
            </tr>
            <tr>
                <td><?=__("Total balance");?></td>
                <td class="total_balance">User</td>
            </tr>
            <tr>
                <td><?=__("Payment request pending");?></td>
                <td class="payment_request_pending">User</td>
            </tr>
        </table>
        <hr>
        <table>
            <tr>
                <td>
                    <?=__("Credits");?>
                    <input type="hidden" name="user_id" class="user_id" />
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="credits" size="50" />
                </td>
            </tr>
            <tr>
                <td><?=__("Reason");?></td>
            </tr>
            <tr>
                <td>
                    <textarea rows="5" cols="50" name="reason"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>