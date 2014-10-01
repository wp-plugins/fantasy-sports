<?php get_header(); ?>
    <?php getMessage();?>
    <h3><?=__('Pick your sport');?></h3>
    <form role="form"  action="<?=FANVICTOR_URL_SUBMIT_PICKS;?>" method="POST">
        <?php foreach($aSports as $aSport):?>
            <?php if(is_array($aSport['orgs']) && $aSport['orgs'] != null):?>
            <div style="margin-top: 7px"><?=$aSport['sport'];?></div>
            <?php foreach($aSport['orgs'] as $aOrg):?>
                <?php if($aOrg['is_active'] == 1):?>
                <div class="radio">
                    <label>
                        <input type="radio" name="sportRadios" <?php if($aOrg['total_pools'] < 1):?>disabled="true"<?php endif;?> class="sportRadio" id="sportRadios<?=$aOrg['organizationID'];?>" value="<?=$aOrg['organizationID'];?>">
                        <?=$aOrg['description'];?>
                    </label>
                </div>
                <?php endif;?>
            <?php endforeach;?>
            <?php endif;?>
        <?php endforeach;?>

        <h3 class="widget-title"><?=__('Date');?></h3>
        <select class="form-control" id="poolDates" name="poolDates" onchange="loadFights()">
        </select>
        
        <h3 class="widget-title"><?=__('Fixture Selection');?></h3>
        <div  id="fixtureDiv" name="fixtureDiv"></div>

        <h3 class="minutes widget-title"><?=__('Minutes');?></h3>
        <select class="minutes" name="allow_minutes" id="allow_minutes">
            <option value="off"><?=__('No');?></option>
            <option value="on"><?=__('Yes');?></option>
        </select>

        <h3 class="widget-title"><?=__("Game Type");?></h3>
        <select class="form-control" name="game_type" id="game_type">
          <option value="pickem"><?=__("Pick 'em (No Spread)");?></option>
          <option value="pickspread"><?=__("Pick 'em (Against Spread)");?></option>
          <option value="pickmoney"><?=__("Pick 'em (Money Line values are points)");?></option>
        </select>

        <h3 class="widget-title"><?=__('Opponent');?></h3>
        <div class="radio">
          <label>
            <input type="radio" name="opponent" id="oppoRadio1" value="public" checked>
                <?=__('Anyone');?>
            </label>
        </div>
        <div class="radio">
          <label>
            <input type="radio" name="opponent" id="oppoRadio1" value="private">
                <?=__('Friends Only');?>
            </label>
        </div>

        <h3 class="widget-title"><?=__('Contest Type');?></h3>
        <div class="radio">
          <label>
            <input type="radio" name="type" id="typeRadios7" value="head2head" checked>
                <?=__('Head to head');?> 	
            </label>
        </div>
        <div class="radio">
          <label>
            <input type="radio" name="type" id="typeRadios8" value="league">
                <?=__('League');?>	
            </label>
        </div>

        <div id="leagueDiv" name="leagueDiv">
            <h3><?=__('League Size');?></h3>
            <select class="form-control" name="leagueSize" id="leagueSize" onchange="resumPrize()">
            <?php foreach($aLeagueSizes as $aLeagueSize):?>
                <option value="<?=$aLeagueSize;?>"><?=$aLeagueSize;?></option>
            <?php endforeach;?>
        </select>

        <h3 class="widget-title"><?=__('Prize Structure');?> </h3>
        <div class="radio">
            <label>
                <input type="radio" name="structure" id="typeRadios9" value="winnertakeall" checked>
                <?=__('Winner takes all');?> 
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="structure" id="typeRadios10" value="top3">
                <?=__('Top 3 get prizes');?> 
            </label>
        </div>
        </div>


        <h3 class="widget-title"><?=__('Entry Fee');?></h3>
        <select class="form-control" id="entry_fee" name="entry_fee" onchange="resumPrize()">
            <option value="0"><?=__('Free Practice');?></option> 
            <?php foreach($aEntryFees as $aEntryFee):?>
                <option value="<?=$aEntryFee;?>"><?=$aEntryFee;?></option>
            <?php endforeach;?>
        </select>

        <h3 class="widget-title"><?=__('Name your league');?></h3>
            <input type="text" class="form-control" id="leaguename" name="leaguename" placeholder="Name your league">
        <br/>
        <h3 class="widget-title">Prizes<?=__('Gateway');?></h3>
        <div name="prizesum" id="prizesum"></div>

        <br/>
        <br/>

        <button type="submit" class="btn btn-primary" name="submitContest" onclick="return validateContest()"><?=__('Create Contest');?></button>
    </form>
<?php get_footer(); ?>
