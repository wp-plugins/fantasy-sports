<?php getMessage();?><?php if($aSports != null && $aPools != null):?>    <h3><?=__('Pick your sport', FV_DOMAIN);?></h3>    <form role="form" class="f-form-inline"  action="<?=FANVICTOR_URL_CREATE_CONTEST;?>" method="POST">        <input type="hidden" id="poolData" value='<?=$aPools;?>' />        <input type="hidden" id="fightData" value='<?=$aFights;?>' />                <input type="hidden" id="roundData" value='<?=$aRounds;?>' />        <input type="hidden" id="winnerPercent" value='<?=get_option('fanvictor_winner_percent');?>' />        <input type="hidden" id="firstPercent" value='<?=get_option('fanvictor_first_place_percent');?>' />        <input type="hidden" id="secondPercent" value='<?=get_option('fanvictor_second_place_percent');?>' />        <input type="hidden" id="thirdPercent" value='<?=get_option('fanvictor_third_place_percent');?>' />                <input type="hidden" id="plugin_url_image" value='<?=FANVICTOR__PLUGIN_URL_IMAGE?>' />        <?php if($aSports != null):?>            <select id="sports" name="organizationID" onchange="jQuery.createcontest.loadPools(jQuery(this).val(), jQuery('option:selected', this).attr('playerdraft'), jQuery('option:selected', this).attr('only_playerdraft'), jQuery('option:selected', this).attr('is_round'))">            <?php foreach($aSports as $aSport):?>                <?php if(!empty($aSport['child']) && is_array($aSport['child'])):?>                <option disabled="true"><?=$aSport['name'];?></option>                <?php foreach($aSport['child'] as $aOrg):?>                    <?php if($aOrg['is_active'] == 1):?>                    <option value="<?=$aOrg['id'];?>" playerdraft="<?=$aOrg['is_playerdraft'];?>" only_playerdraft="<?=$aOrg['only_playerdraft'];?>" is_round="<?=$aOrg['is_round'];?>" style="padding-left: 20px">                        <?=$aOrg['name'];?>                    </option>                    <?php endif;?>                <?php endforeach;?>                <?php endif;?>            <?php endforeach;?>            </select>        <?php endif;?>        <h3 class="widget-title"><?=__('Events', FV_DOMAIN);?></h3>        <div id="poolDates"></div>        <div id="wrapFixtures">            <h3 class="widget-title"><?=__('Fixture Selection', FV_DOMAIN);?></h3>            <div id="fixtureDiv"></div>        </div>                <div id="wrapRounds">            <h3 class="widget-title"><?=__('Rounds', FV_DOMAIN);?></h3>            <div id="roundDiv"></div>        </div>        <h3 class="minutes widget-title"><?=__('Minutes', FV_DOMAIN);?></h3>        <select class="minutes" name="allow_minutes" id="allow_minutes">            <option value="off"><?=__('No', FV_DOMAIN);?></option>            <option value="on"><?=__('Yes', FV_DOMAIN);?></option>        </select>        <h3 class="widget-title"><?=__("Game Type", FV_DOMAIN);?></h3>        <select class="form-control" name="game_type" id="game_type">            <?php foreach($aGameTypes as $aGameType):?>                <option value="<?=$aGameType['value'];?>" id="<?=$aGameType['value'];?>Type"><?=$aGameType['name'];?></option>            <?php endforeach;?>        </select>        <h3 class="widget-title"><?=__('Opponent', FV_DOMAIN);?></h3>        <div class="radio">          <label>            <input type="radio" name="opponent" id="oppoRadio1" value="public" checked>                <?=__('Anyone', FV_DOMAIN);?>            </label>        </div>        <div class="radio">          <label>            <input type="radio" name="opponent" id="oppoRadio1" value="private">                <?=__('Friends Only', FV_DOMAIN);?>            </label>        </div>        <h3 class="widget-title"><?=__('Contest Type', FV_DOMAIN);?></h3>        <div class="radio">          <label>            <input type="radio" name="type" id="typeRadios7" value="head2head" checked>                <?=__('Head to head', FV_DOMAIN);?> 	            </label>        </div>        <div class="radio">          <label>            <input type="radio" name="type" id="typeRadios8" value="league">                <?=__('League', FV_DOMAIN);?>	            </label>        </div>                <div class="leagueDiv" name="leagueDiv" style="display: none">                        <label>                <input type="checkbox" name="multi_entry" value="1" />                <?=__('Multi Entry', FV_DOMAIN);?>	            </label>            <h3><?=__('League Size', FV_DOMAIN);?></h3>            <select class="form-control" name="leagueSize" id="leagueSize" onchange="jQuery.createcontest.calculatePrizes()">                <?php foreach($aLeagueSizes as $aLeagueSize):?>                    <option value="<?=$aLeagueSize;?>"><?=$aLeagueSize;?></option>                <?php endforeach;?>            </select>            <h3 class="widget-title"><?=__('Prize Structure', FV_DOMAIN);?> </h3>            <div class="radio">                <label>                    <input type="radio" name="structure" id="typeRadios9" value="winnertakeall" checked>                    <?=__('Winner takes all', FV_DOMAIN);?>                 </label>            </div>            <div class="radio">                <label>                    <input type="radio" name="structure" id="typeRadios10" value="top3">                    <?=__('Top 3 get prizes', FV_DOMAIN);?>                 </label>            </div>            			<div class="radio">                 <label>                     <input type="radio" name="structure" id="typeRadios10" value="multi_payout">                     <?=__('Multi payout', FV_DOMAIN);?>                  </label>                <a id="addPayouts" onclick="return jQuery.createcontest.addPayouts();" href="#" style="display: none">                    <img title="<?=__("Add", FV_DOMAIN);?>" alt="<?=__("Add", FV_DOMAIN);?>" src="<?=FANVICTOR__PLUGIN_URL_IMAGE.'add.png';?>">                </a>                <div id="payoutExample" style="display: none;margin-left: 50px;">                    <?=__('Example', FV_DOMAIN);?>: <br/>                    1st: <?=__('From', FV_DOMAIN);?>  1 <?=__('to', FV_DOMAIN);?> 1: 40%<br/>                    2nd: <?=__('From', FV_DOMAIN);?>  2 <?=__('to', FV_DOMAIN);?> 2: 30%<br/>                    3rd: <?=__('From', FV_DOMAIN);?>  3 <?=__('to', FV_DOMAIN);?> 3: 20%<br/>                    4th - 5th: <?=__('From', FV_DOMAIN);?> 4 <?=__('to', FV_DOMAIN);?> 6: 10%<br/>                    <?=__('Total percent must be 100%', FV_DOMAIN);?>                </div>            </div>                        <div id="payouts" style="margin-left: 50px;"></div>        </div>        <h3 class="widget-title"><?=__('Entry Fee', FV_DOMAIN);?></h3>        <select class="form-control" id="entry_fee" name="entry_fee" onchange="jQuery.createcontest.calculatePrizes()">            <option value="0"><?=__('Free Practice', FV_DOMAIN);?></option>             <?php foreach($aEntryFees as $aEntryFee):?>                <option value="<?=$aEntryFee;?>"><?=$aEntryFee;?></option>            <?php endforeach;?>        </select>        <h3 class="widget-title"><?=__('Name your league', FV_DOMAIN);?></h3>            <input type="text" class="form-control" id="leaguename" name="leaguename" placeholder="<?=__('Name your league', FV_DOMAIN);?>">        <br/>        <h3 class="widget-title">Prizes<?=__('Gateway', FV_DOMAIN);?></h3>        <div name="prizesum" id="prizesum"></div>        <br/>        <br/>        <button type="submit" class="f-create-contest f-button f-primary f-right" name="submitContest"><?=__('Create Contest', FV_DOMAIN);?></button>    </form><?php else:?>    <?=__("There are no events.", FV_DOMAIN);?><?php endif;?>