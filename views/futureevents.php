<div class="contentPlugin">
    <h1><?=$sHeader;?></h1>
    <?php if($futureEvents != null):?>
        <form action="{url link='fanvictor.submitpicks'}" medivod="POST">
            <input type="hidden" class="leagueID" name="leagueID" />
            <input type="hidden" class="poolID" name="poolID" />
            <div id="leagues_future_events">

                <div class="tableLiveEntries table6">
                    <div class="tableTitle">
                        <div><?=__('ID', FV_DOMAIN);?></div>
                        <div><?=__('Name', FV_DOMAIN);?></div>
                        <div><?=__('Sport', FV_DOMAIN);?></div>
                        <div><?=__('Start Date', FV_DOMAIN);?></div>
                        <div><?=__('Cut Date', FV_DOMAIN);?></div>
                        <div><?=__('Fixture', FV_DOMAIN);?></div>
                    </div>
                </div>
                <div class="tableLiveEntries tableLiveEntriesContent  table6">
                    <?php foreach($futureEvents as $item):?>
                    <div >
                        <div><span><?=__('ID', FV_DOMAIN);?></span><?=$item['poolID']?></div>
                        <div><span><?=__('Name', FV_DOMAIN);?></span><?=$item['poolName']?></div>
                        <div><span><?=__('Sport', FV_DOMAIN);?></span><?=$item['organization']?></div>
                        <div><span><?=__('Start Date', FV_DOMAIN);?></span><?=$item['startDate']?></div>
                        <div><span><?=__('Cut Date', FV_DOMAIN);?></span><?=$item['cutDate']?></div>
                        <div>
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