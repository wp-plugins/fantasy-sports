<?php getMessage();?><?php if($aSports != null && $aPools != null):?>    <h3><?=__('Pick your sport');?></h3>    <form role="form" class="f-form-inline"  action="<?=FANVICTOR_URL_CREATE_CONTEST;?>" method="POST">        <input type="hidden" id="poolData" value='<?=$aPools;?>' />        <input type="hidden" id="fightData" value='<?=$aFights;?>' />                <input type="hidden" id="roundData" value='<?=$aRounds;?>' />        <input type="hidden" id="winnerPercent" value='<?=get_option('fanvictor_winner_percent');?>' />        <input type="hidden" id="firstPercent" value='<?=get_option('fanvictor_first_place_percent');?>' />        <input type="hidden" id="secondPercent" value='<?=get_option('fanvictor_second_place_percent');?>' />        <input type="hidden" id="thirdPercent" value='<?=get_option('fanvictor_third_place_percent');?>' />        <?php if($aSports != null):?>            <select id="sports" name="organizationID" onchange="jQuery.createcontest.loadPools(jQuery(this).val(), jQuery('option:selected', this).attr('playerdraft'), jQuery('option:selected', this).attr('only_playerdraft'), jQuery('option:selected', this).attr('is_round'))">            <?php foreach($aSports as $aSport):?>                <?php if(!empty($aSport['child']) && is_array($aSport['child'])):?>                <option disabled="true"><?=$aSport['name'];?></option>                <?php foreach($aSport['child'] as $aOrg):?>                    <?php if($aOrg['is_active'] == 1):?>                    <option value="<?=$aOrg['id'];?>" playerdraft="<?=$aOrg['is_playerdraft'];?>" only_playerdraft="<?=$aOrg['only_playerdraft'];?>" is_round="<?=$aOrg['is_round'];?>" style="padding-left: 20px">                        <?=$aOrg['name'];?>                    </option>                    <?php endif;?>                <?php endforeach;?>                <?php endif;?>            <?php endforeach;?>            </select>        <?php endif;?>        <h3 class="widget-title"><?=__('Events');?></h3>        <div id="poolDates"></div>        <div id="wrapFixtures">            <h3 class="widget-title"><?=__('Fixture Selection');?></h3>            <div id="fixtureDiv"></div>        </div>                <div id="wrapRounds">            <h3 class="widget-title"><?=__('Rounds');?></h3>            <div id="roundDiv"></div>        </div>        <h3 class="minutes widget-title"><?=__('Minutes');?></h3>        <select class="minutes" name="allow_minutes" id="allow_minutes">            <option value="off"><?=__('No');?></option>            <option value="on"><?=__('Yes');?></option>        </select>        <h3 class="widget-title"><?=__("Game Type");?></h3>        <select class="form-control" name="game_type" id="game_type">            <?php foreach($aGameTypes as $aGameType):?>                <option value="<?=$aGameType['value'];?>" id="<?=$aGameType['value'];?>Type"><?=$aGameType['name'];?></option>            <?php endforeach;?>        </select>        <h3 class="widget-title"><?=__('Opponent');?></h3>        <div class="radio">          <label>            <input type="radio" name="opponent" id="oppoRadio1" value="public" checked>                <?=__('Anyone');?>            </label>        </div>        <div class="radio">          <label>            <input type="radio" name="opponent" id="oppoRadio1" value="private">                <?=__('Friends Only');?>            </label>        </div>        <h3 class="widget-title"><?=__('Contest Type');?></h3>        <div class="radio">          <label>            <input type="radio" name="type" id="typeRadios7" value="head2head" checked>                <?=__('Head to head');?> 	            </label>        </div>        <div class="radio">          <label>            <input type="radio" name="type" id="typeRadios8" value="league">                <?=__('League');?>	            </label>        </div>        <div class="leagueDiv" name="leagueDiv" style="display: none">            <h3><?=__('League Size');?></h3>            <select class="form-control" name="leagueSize" id="leagueSize" onchange="jQuery.createcontest.calculatePrizes()">            <?php foreach($aLeagueSizes as $aLeagueSize):?>                <option value="<?=$aLeagueSize;?>"><?=$aLeagueSize;?></option>            <?php endforeach;?>        </select>        <h3 class="widget-title"><?=__('Prize Structure');?> </h3>        <div class="radio">            <label>                <input type="radio" name="structure" id="typeRadios9" value="winnertakeall" checked>                <?=__('Winner takes all');?>             </label>        </div>        <div class="radio">            <label>                <input type="radio" name="structure" id="typeRadios10" value="top3">                <?=__('Top 3 get prizes');?>             </label>        </div>        </div>        <h3 class="widget-title"><?=__('Entry Fee');?></h3>        <select class="form-control" id="entry_fee" name="entry_fee" onchange="jQuery.createcontest.calculatePrizes()">            <option value="0"><?=__('Free Practice');?></option>             <?php foreach($aEntryFees as $aEntryFee):?>                <option value="<?=$aEntryFee;?>"><?=$aEntryFee;?></option>            <?php endforeach;?>        </select>        <h3 class="widget-title"><?=__('Name your league');?></h3>            <input type="text" class="form-control" id="leaguename" name="leaguename" placeholder="Name your league">        <br/>        <h3 class="widget-title">Prizes<?=__('Gateway');?></h3>        <div name="prizesum" id="prizesum"></div>        <br/>        <br/>        <button type="submit" class="f-create-contest f-button f-primary f-right" name="submitContest"><?=__('Create Contest');?></button>    </form><?php else:?>    <?=__("There are no events.");?><?php endif;?>