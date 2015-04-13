<div class="contentPlugin">
    <?php if($futureEvents != null):?>
        <form action="{url link='fanvictor.submitpicks'}" medivod="POST">
            <input type="hidden" class="leagueID" name="leagueID" />
            <input type="hidden" class="poolID" name="poolID" />
            <div id="leagues_future_events">

                <div class="tableLiveEntries table6">
                    <div class="tableTitle">
                        <div style="width: 6%"><?=__('ID', FV_DOMAIN);?></div>
                        <div style="width: 39%"><?=__('Name', FV_DOMAIN);?></div>
                        <div style="width: 15%"><?=__('Sport', FV_DOMAIN);?></div>
                        <div style="width: 15%"><?=__('Start Date', FV_DOMAIN);?></div>
                        <div style="width: 15%"><?=__('Cut Date', FV_DOMAIN);?></div>
                        <div style="width: 10%"><?=__('Fixture', FV_DOMAIN);?></div>
                    </div>
                </div>
                <div class="tableLiveEntries tableLiveEntriesContent  table6">
                    <?php foreach($futureEvents as $item):?>
                    <div >
                        <div style="width: 6%"><span><?=__('ID', FV_DOMAIN);?></span><?=$item['poolID']?></div>
                        <div style="width: 39%"><span><?=__('Name', FV_DOMAIN);?></span><?=$item['poolName']?></div>
                        <div style="width: 15%"><span><?=__('Sport', FV_DOMAIN);?></span><?=$item['organization']?></div>
                        <div style="width: 15%"><span><?=__('Start Date', FV_DOMAIN);?></span><?=$item['startDate']?></div>
                        <div style="width: 15%"><span><?=__('Cut Date', FV_DOMAIN);?></span><?=$item['cutDate']?></div>
                        <div style="width: 10%">
                            <?php if($item['only_playerdraft'] == 0):?>
                            <a href="#" onclick="return viewPoolFixture(<?=$item['poolID'];?>, '<?=__("fixtures", FV_DOMAIN);?>')">
                                <?=__("View fixtures", FV_DOMAIN);?>
                            </a>
                            <?php endif;?>
                        </div>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </form>
        <div id="dlgFixture" style="display: none"></div>
    <?php else:?>
        <?=__("There are no future events", FV_DOMAIN);?>
    <?php endif;?>
</div>