<div id="f-live-scoring-app">
    <div class="f-column-12 f-clearfix">
        <div id="f-scoring-table-name"><h1><?=$league['name'];?></h1></div>
        <div id="f-current-table-status">
            <span class="f-final">FINAL</span>
            Start <?=$aPool['startDate'];?>
        </div>
    </div>
    <div class="f-column-12" id="f-scoring-table-info">
        <?php /*if($currUserScore['rank'] > 3):?>
        <div class="f-clearfix f-grey" id="f-final-table-status">
            <h4 class="f-loser">Unfortunately you didn't win this time.</h4>
            <p>
                Get back in the game and <a href="<?=FANVICTOR_URL_LOBBY;?>" class="f-button f-fancy f-primary">Enter another contest</a>
            </p>
        </div>
        <?php endif;*/?>
        <div class="f-clearfix" id="f-table-meta-information">
            <ul class="f-column-8">
                <li class="f-table-type"> Multiplayer league (<?=$league['entries'];?> entries)</li>
                <li class="f-entry">Entry: $<?=$league['entry_fee'];?></li>
            </ul>
            <ul class="f-prizes-breakdown">
                <li class="f-prize-row">
                    <span class="f-pos-name">1st:</span> $0
                </li>
            </ul>
        </div>
    </div>
    <?php if(!empty($aFights)):?>
    <div class="f-column-12" id="f-live-scoring-fixture-info">
        <section>
            <ul>
                <?php foreach($aFights as $aFight):?>
                <li class="f-fixture-card">
                    <ul class="f-fixture-card-live-status f-pending">
                        <li class="f-fixture-card-away"><?=$aFight['nickName1'];?> <?=$aFight['team1score'];?></li>
                        <li class="f-fixture-card-home"><?=$aFight['nickName2'];?> <?=$aFight['team2score'];?></li>
                        <li class="f-fixture-card-time">
                            <?php if($aFight['is_final']):?>
                            <?=__('FINAL');?>
                            <?php endif;?>&nbsp;
                        </li>
                    </ul>
                </li>
                <?php endforeach;?>
            </ul>
        </section>
    </div>
    <?php endif;?>
    <div class="f-column-12 f-small-screen-pane" id="f-live-scoring-leaderboard">
        <div>
            <table class="f-condensed" id="tableScores">
                <thead>
                    <tr>
                        <th>Pos</th>
                        <th>User</th>
                        <th class="f-text-align-right">Score</th>
                        <th class="f-text-align-right">Prizes</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div id="f-live-scoring-filter">
        <div class="f-button-group f-container" style="visibility:hidden;">
            <button class="f-button f-mini f-is-active">All</button>
            <button class="f-button f-mini">Me</button>
        </div>
    </div>
    <div id="f-live-scoring-entry-details">
        <div class="f-slot f-column-6 f-entry-component f-small-screen-pane f-odd" id="f-seat-1"></div>
        <div class="f-slot f-column-6 f-entry-component f-small-screen-pane f-even" id="f-seat-2">
            <div id="live-scoring-app" class="loading"></div>
            <p class="f-entry-placeholder-text">
                Select a user from the list above to see their lineup.
            </p>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(window).load(function(){
        var isLive = <?=$league['is_live'];?>;
        if(isLive)
        {
            jQuery.league.liveEntriesResult(<?=$league['poolID'];?>, <?=$league['leagueID'];?>);
            setInterval(function() { 
                jQuery.league.liveEntriesResult(<?=$league['poolID'];?>, <?=$league['leagueID'];?>)
            }, 60000);
        }
        else 
        {
            jQuery.playerdraft.loadContestScores(<?=$league['leagueID'];?>);
        }
    })
</script>