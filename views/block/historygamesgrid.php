<h3 class="widget-title">
    <?=$sHeader;?>
</h3>
<div class="content">
    <form action="<?=FANVICTOR_URL_RANKINGS;?>" method="POST">
        <input type="hidden" class="leagueID" name="leagueID" />
        <div id="leagues_history_grid"><?=$historyContests;?></div>
    </form>
</div>