<?php getMessage();?>
<div class="f-contest-title-date">
    <h1 class="f-contest-title f-heading-styled"><?=$league['name'];?></h1>
    <div class="f-contest-date-container">
        <div class="f-contest-date-start-time">
            <?=__('Contest starts', FV_DOMAIN);?> <?=$aPool['startDate'];?>
        </div>
    </div>
</div>
<ul class="f-contest-information-bar">
    <li class="f-contest-entries-league"><?=__('Entries:', FV_DOMAIN);?> 
        <b>
            <a class="f-lightboxLeagueEntries_show" href="#" onclick="return jQuery.playerdraft.ruleScoring(<?=$league['leagueID'];?>, '<?=htmlentities($league['name']);?>', '<?=$league['entry_fee'];?>', '<?=$aPool['salary_remaining'];?>', 2)"><?=$league['entries'];?></a>
        </b> / <?=$league['size'];?>
        <span class="f-entries-player-league"> <?=__('player league', FV_DOMAIN);?></span>
    </li>
    <li class="f-contest-entry-fee-container">
    Entry fee:
        <span class="f-entryFee-value amount">$<?=$league['entry_fee'];?></span>
    </li>
    <li class="f-contest-prize-container  f-gameEntry-inner-entryFeeSelected">
    Prizes:
        <span class="f-content-prize-amount">
            <a class="f-lightboxPrizeList_show" href="#"  onclick="return jQuery.playerdraft.ruleScoring(<?=$league['leagueID'];?>, '<?=htmlentities($league['name']);?>', '<?=$league['entry_fee'];?>', '<?=$aPool['salary_remaining'];?>', 3)">
                $<?=$league['prizes'];?>
            </a>
        </span>
    </li>
    <li class="f-contest-rules-link-container">
        <a class="f-lightboxRulesAndScoring_show" onclick="return jQuery.playerdraft.ruleScoring(<?=$league['leagueID'];?>, '<?=htmlentities($league['name']);?>', '<?=$league['entry_fee'];?>', '<?=$aPool['salary_remaining'];?>')" href="#">
            <?=__('Rules &amp; Scoring', FV_DOMAIN);?>
        </a>
    </li>
</ul>
<div class="f-pick-your-team">
    <section data-role="fixture-picker" class="f-fixture-picker">
        <?php if(!empty($aFights)):?>
		<h1><?=__('Players available from (click to filter):', FV_DOMAIN);?></h1>
		<div class="f-fixture-picker-button-container">
			<a class="f-button f-mini f-is-active fixture-item" onclick="jQuery.playerdraft.setActiveFixture(this);return jQuery.playerdraft.loadPlayers();">All</a>
            <?php foreach($aFights as $aFight):?>
            <a data-team-id1="<?=$aFight['fighterID1'];?>" data-team-id2="<?=$aFight['fighterID2'];?>" onclick="jQuery.playerdraft.setActiveFixture(this);return jQuery.playerdraft.loadPlayers();" class="f-button f-mini fixture-item">
                <span class="f-fixture-team-home"><?=$aFight['nickName1'];?></span>
                @
                <span class="f-fixture-team-away"><?=$aFight['nickName2'];?></span>
                <span class="f-fixture-start-time"><?=$aFight['startDate'];?></span>
            </a>
			<?php endforeach;?>
        </div>
        <?php endif;?>
        <?php if(!empty($aRounds)):?>
		<h1><?=__('Players available from (click to filter):', FV_DOMAIN);?></h1>
		<div class="f-fixture-picker-button-container">
			<a class="f-button f-mini f-is-active fixture-item" onclick="jQuery.playerdraft.setActiveFixture(this);return jQuery.playerdraft.loadPlayers();">All</a>
            <?php foreach($aRounds as $aRound):?>
            <a class="f-button f-mini fixture-item">
                <span class="f-fixture-team-home"><?=$aRound['name'];?></span>
                <span class="f-fixture-start-time"><?=$aRound['startDate'];?></span>
            </a>
			<?php endforeach;?>
        </div>
        <?php endif;?>
	</section>
</div>
<div class="f-row">
    <section class="f-contest-player-list-container" data-role="player-list">
        <div class="f-row">
            <h1><?=__('Available Players', FV_DOMAIN);?></h1>
            <ul class="f-player-list-position-tabs f-tabs f-row">
				<li>
                    <a href="" data-id="" class="f-is-active" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();"><?=__('All', FV_DOMAIN)?></a>
                </li>
                <?php if($aPositions != null):?>
                <?php foreach($aPositions as $aPosition):?>
                <li>
                    <a href="" data-id="<?=$aPosition['id'];?>" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();">
                        <?=$aPosition['name'];?>
                    </a>
                </li>
                <?php endforeach;?>
                <?php endif;?>
                <li class="f-player-search">
					<label class="f-is-hidden" for="player-search"><?=__('Find a Player', FV_DOMAIN);?></label>
					<input type="search" id="player-search" placeholder="<?=__('Find a player...', FV_DOMAIN);?>" incremental="" autosave="fd-player-search" results="10">
				</li>
			</ul>
            <div data-role="scrollable-header">
				<table class="f-condensed f-player-list-table-header f-header-fields">
					<thead>
						<tr>
                            <th colspan="2" class="f-player-name table-sorting">
								<?=__('Name', FV_DOMAIN);?>
								<i class="f-icon f-sorted-asc">▴</i>
								<i class="f-icon f-sorted-desc">▾</i>
							</th>
                            <?php if(!$aPool['only_playerdraft']):?>
							<th class="f-player-played table-sorting">
								<i class="f-icon f-sorted-asc">▴</i>
								<i class="f-icon f-sorted-desc">▾</i>
								<?=__('Team', FV_DOMAIN);?>
							</th>
							<th class="f-player-fixture table-sorting">
								<?=__('Game', FV_DOMAIN);?>
								<i class="f-icon f-sorted-asc">▴</i>
								<i class="f-icon f-sorted-desc">▾</i>
							</th>
                            <?php endif;?>
							<th class="f-player-salary table-sorting">
								<i class="f-icon f-sorted-asc">▴</i>
								<i class="f-icon f-sorted-desc">▾</i>
								<?=__('Salary', FV_DOMAIN);?>
							</th>
							<th class="f-player-add"></th>
						</tr>
					</thead>
				</table>
			</div>
            <div class="f-errorMessage"></div>
            <div data-role="scrollable-body" id="listPlayers">
                <div class="f-player-list-empty"><?=__('No matching players. Try changing your filter settings.', FV_DOMAIN);?></div>
                <table class="f-condensed f-player-list-table">
                    <thead class="f-is-hidden">
                        <tr>
                            <th class="f-player-name">
                                <?=__('Pos', FV_DOMAIN);?>
                            </th>
                            <th class="f-player-name">
                                <?=__('Name', FV_DOMAIN);?>
                            </th>
                            <th class="f-player-fppg">
                                <?=__('FPPG', FV_DOMAIN);?>
                            </th>
                            <?php if(!$aPool['only_playerdraft']):?>
                            <th class="f-player-played">
                                <?=__('Team', FV_DOMAIN);?>
                            </th>
                            <th class="f-player-fixture">
                                <?=__('Game', FV_DOMAIN);?>
                            </th>
                            <?php endif;?>
                            <th class="f-player-salary">
                                <?=__('Salary', FV_DOMAIN);?>
                            </th>
                            <th class="f-player-add"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="f-row f-legend">
                <div class="f-draft-legend-key-title" data-role="expandable-heading" onclick="return jQuery.playerdraft.showIndicatorLegend()"><?=__('Indicator legend', FV_DOMAIN);?></div>
                <div class="f-clear"></div>
                <div class="f-draft-legend-key-content">
					<ul>
                        <?php foreach($aIndicators as $aIndicator):?>
                        <?php 
                            $indicatorClass = '';
                            switch($aIndicator['alias'])
                            {
                                case 'IR':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-out';
                                    break;
                                case 'O':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-out';
                                    break;
                                case 'D':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-possible';
                                    break;
                                case 'Q':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-possible';
                                    break;
                                case 'P':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-probable';
                                    break;
                                case 'NA':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-out';
                                    break;
                            }
                        ?>
                        <li>
                            <span class="<?=$indicatorClass;?>">
                                <?=$aIndicator['alias'];?>
                            </span> 
                            <?=$aIndicator['name'];?>
                        </li>
                        <?php endforeach;?>
					</ul>
                    <div class="f-clear"></div>
				</div>
			</div>
        </div>
    </section>
    <section class="f-roster-container" data-role="team">
        <header>
            <div class="f-lineup-text-container">
                <h1><?=__('Your lineup', FV_DOMAIN);?></h1>
                <p class="f-lineup-lock-message">
                    <i class="fa fa-lock"></i> <?=__('Locks @', FV_DOMAIN);?> <?=$aPool['startDate'];?>
                    <span class="f-game_status_open"></span>
                </p>
            </div>
            <div class="f-salary-remaining">
                <div class="f-salary-remaining-container">
                    <span class="f-salary-remaining-amount" id="salaryRemaining">
                        <?php if($aPool['salary_remaining'] > 0):?>
                            $<?=number_format($aPool['salary_remaining']);?>
                        <?php else:?>
                            <?=__('Unlimited', FV_DOMAIN);?>
                        <?php endif;?>
                    </span><?=__('Salary Remaining', FV_DOMAIN);?>
                </div>
                <div class="f-player-average-container">
                    <span class="f-player-average-amount" id="AvgPlayer"></span><?=__('Avg/Player', FV_DOMAIN);?>
                </div>
            </div>
        </header>
        <section class="f-roster">
            <ul>
                <?php if($aLineups != null && is_array($aLineups)):?>
                    <?php foreach($aLineups as $aLineup):?>
                        <?php for($i = 0; $i < $aLineup['total']; $i++):?>
                        <li class="f-roster-position f-count-0 player-position-<?=$aLineup['id'];?>" <?php if($aPool['is_round'] == 1):?>style="padding-left: 0;background: none;"<?php endif;?>>
                            <div class="f-player-image" <?php if($aPool['is_round'] == 1):?>style="display: none;"<?php endif;?>></div>
                            <div class="f-position"><?=$aLineup['name'];?>
                                <span class="f-empty-roster-slot-instruction"><?=__('Add player', FV_DOMAIN);?></span>
                            </div>
                            <div class="f-player"></div>
                            <div class="f-salary">$0</div>
                            <div class="f-fixture"></div>
                            <a class="f-button f-tiny f-text">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </li>
                        <?php endfor;?>
                    <?php endforeach;?>
                <?php else:?>  
                    <?php for($i = 0; $i < $aLineups; $i++):?> 
                        <li class="f-roster-position f-count-0 player-position-0" <?php if($aPool['is_round'] == 1):?>style="padding-left: 0;background: none;"<?php endif;?>>
                            <div class="f-player-image" <?php if($aPool['is_round'] == 1):?>style="display: none;"<?php endif;?>></div>
                            <div class="f-position">
                                <span class="f-empty-roster-slot-instruction"><?=__('Add player', FV_DOMAIN);?></span>
                            </div>
                            <div class="f-player"></div>
                            <div class="f-salary">$0</div>
                            <div class="f-fixture"></div>
                            <a class="f-button f-tiny f-text">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </li>
                    <?php endfor;?>
                <?php endif;?>
            </ul>
            <div class="f-row import-clear-button-container">
                <button class="f-button f-mini f-text f-right" id="playerPickerClearAllButton" onclick="jQuery.playerdraft.clearAllPlayer()" type="button">
                    <small><i class="fa fa-minus-circle"></i> <?=__('Clear all', FV_DOMAIN);?></small>
                </button>
            </div>
        </section>
        <footer class="f-">
            <div class="f-contest-entry-fee-container">
                <form id="formLineup" enctype="multipart/form-data" method="POST" action="<?=FANVICTOR_URL_GAME;?>">
                    <div id="enterForm.game_id.e" class="f-form_error"></div>
                    <input type="hidden" value="1" name="submitPicks">
                    <input type="hidden" value="<?=$league['leagueID'];?>" name="leagueID">
                    <input type="hidden" value="<?=$entry_number;?>" name="entry_number">
                    <input type="hidden" value="<?=session_id();?>" name="session_id">
                    <input type="hidden" value="1" name="submitPicks">
                </form>
            </div>
            <div class="f-contest-enter-button-container">
                <input type="submit" data-nav-warning="off" value="<?=__('Enter', FV_DOMAIN);?>" class="f-button f-jumbo f-primary" onclick="jQuery.playerdraft.submitData()">
            </div>
        </footer>
    </section>
</div>
<?php require_once('dlg_info.php');?>
<script type="text/javascript">
    jQuery.playerdraft.setData('<?=json_encode($aPlayers);?>', 
                               '<?=$aPool['salary_remaining'];?>', 
                               '<?=json_encode($playerIdPicks);?>', 
                               '<?=json_encode($league);?>', 
                               '<?=json_encode($aFights);?>',
                               '<?=json_encode($aPool);?>',
                               '<?=json_encode($aIndicators);?>');
</script>