<div class="contentPlugin">
    <h1><?=__('Transaction History', FV_DOMAIN);?></h1>
    <?php getMessage();?>
    <?php if($aFundHistorys != null):?>
        <div>
            <div class="tableLiveEntries ">
                <div class="tableTitle">
                    <div><?=__('Date', FV_DOMAIN);?></div>
                    <div><?=__('Operation', FV_DOMAIN);?></div>
                    <div><?=__('Type', FV_DOMAIN);?></div>
                    <div><?=__('Gateway', FV_DOMAIN);?></div>
                    <div><?=__('Status', FV_DOMAIN);?></div>
                    <div><?=__('Leagueid', FV_DOMAIN);?></div>
                    <div><?=__('Transactionid', FV_DOMAIN);?></div>
                    <div><?=__('Amount', FV_DOMAIN);?></div>
                    <div><?=__('New balance', FV_DOMAIN);?></div>
                    <div><?=__('Reason', FV_DOMAIN);?></div>
                </div>
            </div>
            <div class="tableLiveEntries tableLiveEntriesContent">
                <?php foreach($aFundHistorys as $aFundHistory):?>
                <div>
                    <div><span><?=__('Date', FV_DOMAIN);?></span><?=$aFundHistory['date'];?></div>
                    <div><span><?=__('Operation', FV_DOMAIN);?></span><?=$aFundHistory['operation'];?></div>
                    <div><span><?=__('Type', FV_DOMAIN);?></span><?=$aFundHistory['type'];?></div>
                    <div><span><?=__('Gateway', FV_DOMAIN);?></span><?=!empty($aFundHistory['gateway']) ? $aFundHistory['gateway'] : '&nbsp;';?></div>
                    <div><span><?=__('Status', FV_DOMAIN);?></span><?=!empty($aFundHistory['status']) ? $aFundHistory['status'] : '&nbsp;';?></div>
                    <div><span><?=__('Leagueid', FV_DOMAIN);?></span><?php if($aFundHistory['leagueID'] > 0):?><?=$aFundHistory['leagueID'];?><?php endif;?></div>
                    <div><span><?=__('Transactionid', FV_DOMAIN);?></span><?=!empty($aFundHistory['transactionID']) ? $aFundHistory['transactionID'] : '&nbsp;';?></div>
                    <div><span><?=__('Amount', FV_DOMAIN);?></span><?=$aFundHistory['amount'];?></div>
                    <div><span><?=__('New balance', FV_DOMAIN);?></span><?=$aFundHistory['new_balance'];?></div>
                    <div><span><?=__('Reason', FV_DOMAIN);?></span><?=!empty($aFundHistory['reason']) ? $aFundHistory['reason'] : '&nbsp;';?></div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    <?php else:?>
        <?=__("There are no transaction histories", FV_DOMAIN);?>
    <?php endif; ?>
</div>