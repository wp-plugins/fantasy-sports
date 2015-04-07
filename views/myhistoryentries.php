<div class="contentPlugin">
    <h3 class="widget-title">
        <?=__("My History Entries", FV_DOMAIN);?>
    </h3>
    <?php if($aLeagues != null):?>
        <div class="content">
            <div class="wrap_content">
                <div>
                    <div class="tableLiveEntries table11">
                        <div class="tableTitle">
                            <div style="width: 6%"><?=__('ID', FV_DOMAIN)?></div>
                            <div style="width: 15%"><?=__('Date', FV_DOMAIN)?></div>
                            <div style="width: 18%"><?=__('Name', FV_DOMAIN)?></div>
                            <div style="width: 12%"><?=__('Type', FV_DOMAIN)?></div>
                            <div style="width: 10%"><?=__('Entries', FV_DOMAIN)?></div>
                            <div style="width: 10%"><?=__('Entry Fee', FV_DOMAIN)?></div>
                            <div style="width: 7%"><?=__('Prizes', FV_DOMAIN)?></div>
                            <div style="width: 6%"><?=__('Rank', FV_DOMAIN)?></div>
                            <div style="width: 8%"><?=__('Winnings', FV_DOMAIN)?></div>
                            <div style="width: 8%">&nbsp;</div>
                        </div>
                    </div>
                    <?php if($aLeagues != null):?>
                    <div class="tableLiveEntries tableLiveEntriesContent table11">
                        <?php foreach($aLeagues as $aLeague):?>
                        <div>
                            <div style="width: 6%"><span><?=__('ID', FV_DOMAIN)?></span><?=$aLeague['leagueID'];?></div>
                            <div style="width: 15%"><span><?=__('Date', FV_DOMAIN)?></span><?=$aLeague['startDate'];?></div>
                            <div style="width: 18%">
                                <span><?=__('Name', FV_DOMAIN)?></span><?=$aLeague['name'];?>
                            </div>
                            <div style="width: 12%"><span><?=__('Type', FV_DOMAIN)?></span><?=$aLeague['gameType'];?></div>
                            <div style="width: 10%"><span><?=__('Entries', FV_DOMAIN)?></span><?=$aLeague['entries'];?> / <?=$aLeague['size'];?></div>
                            <div style="width: 10%"><span><?=__('Entry Fee', FV_DOMAIN)?></span>$<?=$aLeague['entry_fee'];?></div>
                            <div style="width: 7%"><span><?=__('Prizes', FV_DOMAIN)?></span>$<?=$aLeague['prizes'];?></div>
                            <div style="width: 6%"><span><?=__('Rank', FV_DOMAIN)?></span><?=$aLeague['rank'];?></div>
                            <div style="width: 8%"><span><?=__('Winnings', FV_DOMAIN)?></span>$<?=$aLeague['winnings'];?></div>
                            <div style="text-align: center;width: 8%">
                                <input type="button" class="btn btn-success btn-xs" value="<?=__('View', FV_DOMAIN)?>" onclick="window.location = '<?=FANVICTOR_URL_RANKINGS.$aLeague['leagueID']."/?num=".$aLeague['entry_number'];?>'">
                            </div>
                        </div>
                        <?php endforeach;?>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    <?php else:?>
        <?=__("There are no history entries", FV_DOMAIN);?>
    <?php endif; ?>
</div>