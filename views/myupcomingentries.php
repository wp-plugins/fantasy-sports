<div class="contentPlugin">
    <h3 class="widget-title">
        <?=__("My Upcoming Entries", FV_DOMAIN);?>
    </h3>
    <?php if($aLeagues != null):?>
        <div class="content">
            <div class="wrap_content">
                 <div >
                    <div class="tableLiveEntries">
                        <div class="tableTitle">
                            <div><?=__('ID', FV_DOMAIN)?></div>
                            <div><?=__('Date', FV_DOMAIN)?></div>
                            <div><?=__('Name', FV_DOMAIN)?></div>
                            <div><?=__('Type', FV_DOMAIN)?></div>
                            <div><?=__('Entries', FV_DOMAIN)?></div>
                            <div><?=__('Size', FV_DOMAIN)?></div>
                            <div><?=__('Entry Fee', FV_DOMAIN)?></div>
                            <div><?=__('Prizes', FV_DOMAIN)?></div><div>&nbsp;</div>
                        </div>
                    </div>
                    <?php if($aLeagues != null):?>
                    <div class="tableLiveEntries tableLiveEntriesContent">
                        <?php foreach($aLeagues as $aLeague):?>
                        <div>
                            <div><span><?=__('ID', FV_DOMAIN)?></span><?=$aLeague['leagueID'];?></div>
                            <div><span><?=__('Date', FV_DOMAIN)?></span><?=$aLeague['startDate'];?></div>
                            <div>
                                <span><?=__('Name', FV_DOMAIN)?></span><?=$aLeague['name'];?>
                            </div>
                            <div><span><?=__('Type', FV_DOMAIN)?></span><?=$aLeague['gameType'];?></div>
                            <div><span><?=__('Entries', FV_DOMAIN)?></span><?=$aLeague['entries'];?></div>
                            <div><span><?=__('Size', FV_DOMAIN)?></span><?=$aLeague['size'];?></div>
                            <div><span><?=__('Entry Fee', FV_DOMAIN)?></span>$<?=$aLeague['entry_fee'];?></div>
                            <div><span><?=__('Prizes', FV_DOMAIN)?></span>$<?=$aLeague['prizes'];?></div>
                            <div style="text-align: center">
                                <input type="button" class="btn btn-success btn-xs" value="<?=__('Edit', FV_DOMAIN)?>" onclick="window.location = '<?=FANVICTOR_URL_SUBMIT_PICKS.$aLeague['leagueID']."/?num=".$aLeague['entry_number'];?>'">
                            </div>
                        </div>
                        <?php endforeach;?>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    <?php else:?>
        <?=__("There are no upcoming entries", FV_DOMAIN);?>
    <?php endif; ?>
</div>