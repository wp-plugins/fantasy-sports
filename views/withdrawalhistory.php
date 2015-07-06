<div class="contentPlugin">
    <div class="contentPlugin">
        <h1><?=__('Withdrawal History', FV_DOMAIN);?></h1>
        <?php if($aWithdraws != null):?>
            <div>
                <div class="tableLiveEntries table6">
                    <div class="tableTitle">
                        <div style="width:90px"><?=__('Request date', FV_DOMAIN);?></div>
                        <div style="width:80px"><?=__('Credits', FV_DOMAIN);?></div>
                        <div style="width:100px"><?=__('Rate', FV_DOMAIN);?></div>
                        <div style="width:80px"><?=__('Real money', FV_DOMAIN);?></div>
                        <div style="width:200px"><?=__('Reason', FV_DOMAIN);?></div>
                        <div style="width:80px"><?=__('Status', FV_DOMAIN);?></div>
                        <div style="width:100px"><?=__('Response date', FV_DOMAIN);?></div>
                        <div><?=__('Response message', FV_DOMAIN);?></div>
                    </div>
                </div>
                <div class="tableLiveEntries tableLiveEntriesContent">
                <?php foreach($aWithdraws as $aWithdraw):?>
                    <div>
                        <div style="width:90px"><?=$aWithdraw['requestDate'];?></div>
                        <div style="width:80px"><?=$aWithdraw['amount'];?></div>
                        <div style="width:100px"><?=get_option('fanvictor_credit_to_cash');?> <?=__('credits equals', FV_DOMAIN);?> $1</div>
                        <div style="width:80px"><?=$aWithdraw['real_amount'];?></div>
                        <div style="width:200px"><?=$aWithdraw['reason'];?></div>
                        <div style="width:80px"><?=$aWithdraw['status'];?></div>
                        <div style="width:100px"><?=$aWithdraw['processedDate'];?></div>
                        <div><?=$aWithdraw['response_message'];?></div>
                    </div>
                <?php endforeach;?>
                </div>
            </div>
        <?php else:?>
            <?=__("There are no withdrawal histories", FV_DOMAIN);?>
        <?php endif; ?>
    </div>
</div>