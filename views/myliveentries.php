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
                        <div style="width: 40px"><?=__('ID', FV_DOMAIN)?></div>
                        <div style="width: 150px"><?=__('Date', FV_DOMAIN)?></div>
                        <div style="width: 150px"><?=__('Name', FV_DOMAIN)?></div>
                        <div style="width: 40px"><?=__('Size', FV_DOMAIN)?></div>
                        <div><?=__('Entries', FV_DOMAIN)?></div>
                        <div><?=__('Entry Fee', FV_DOMAIN)?></div>
                        <div><?=__('Prizes', FV_DOMAIN)?></div>
                        <div><?=__('Rank', FV_DOMAIN)?></div>
                        <div>&nbsp;</div>
                    </div>
                </div>
                <div class="tableLiveEntries" id="tableLiveEntriesContent"></div>
            </div>
        </div>
    </div>
</div>