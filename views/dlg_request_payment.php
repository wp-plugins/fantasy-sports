<div id="dlgRequestPayment" style="display: none">
    <div id="msgRequestPayment" class="public_message"></div>
    <form id="formRequestPayment">
        <p>
            <?=__('Available balance');?>: <span class="balance"></span><br/>
            <?=__('Rate');?>: <?=get_option('fanvictor_credit_to_cash');?> <?=__('withdraw equals');?> $1
        </p>
        <p>
            <?=__('How many credits do you want to withdraw');?>:<br/>
            <input type="text" name="credits" />
        </p>
        <p>
            <?=__('Reason');?>:<br/>
            <textarea rows="5" cols="50" name="reason"></textarea>
        </p>
    </form>
</div>