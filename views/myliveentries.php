<script type="text/javascript">
    jQuery.league.loadLiveEntries('<?=FANVICTOR_URL_RANKINGS;?>');
    setInterval(function() { jQuery.league.loadLiveEntries('<?=FANVICTOR_URL_RANKINGS;?>') }, 60000);
</script>

<div class="contentPlugin">
    <h3 class="widget-title">
        <?=__("My Live Entries", FV_DOMAIN);?>
    </h3>
    <div class="content">
        <div class="wrap_content">
            <div id="tableLiveEntries">
                <div class="tableLiveEntries">
                    <div class="tableTitle">
                        <div style="width: 6%"><?=__('ID', FV_DOMAIN)?></div>
                        <div style="width: 15%"><?=__('Date', FV_DOMAIN)?></div>
                        <div style="width: 38%"><?=__('Name', FV_DOMAIN)?></div>
                        <div style="width: 10%"><?=__('Entries', FV_DOMAIN)?></div>
                        <div style="width: 10%"><?=__('Entry Fee', FV_DOMAIN)?></div>
                        <div style="width: 7%"><?=__('Prizes', FV_DOMAIN)?></div>
                        <div style="width: 6%"><?=__('Rank', FV_DOMAIN)?></div>
                        <div style="width: 8%">&nbsp;</div>
                    </div>
                </div>
                <div class="tableLiveEntries" id="tableLiveEntriesContent"></div>
            </div>
        </div>
    </div>
</div>