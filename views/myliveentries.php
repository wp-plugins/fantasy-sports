<script type="text/javascript">
    jQuery.league.loadLiveEntries('<?=FANVICTOR_URL_RANKINGS;?>');
    setInterval(function() { jQuery.league.loadLiveEntries('<?=FANVICTOR_URL_RANKINGS;?>') }, 60000);
</script>

<h3 class="widget-title">
    <?=__("My Live Entries");?>
</h3>
<div class="content">
    <div class="wrap_content">
        <table class="table table-striped table-bordered table-responsive table-condensed" id="tableLiveEntries">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Entries</th>
                    <th>Entry Fee</th>
                    <th>Prizes</th>
                    <th>Rank</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>