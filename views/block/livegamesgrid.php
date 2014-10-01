<script type="text/javascript">
var isLive = "<?=$isLive;?>";
if(isLive)
{
    updateLiveContests();
    setInterval(function() { updateLiveContests() }, 60000);
}
</script>

<h3 class="widget-title">
    <?=$sHeader;?>
</h3>
<div class="content">
    <div id="popup_leagues_details" class="popup_block"></div>
    <form action="{url link='fanvictor.rankings'}" method="POST">
        <input type="hidden" name="live" value="1" />
        <input type="hidden" class="leagueID" name="leagueID" />
        <input type="hidden" class="poolID" name="poolID" />
        <div id="leagues_live_games_grid"></div>
    </form>
</div>