<div class="contentPlugin">
<form action="<?=FANVICTOR_URL_SUBMIT_PICKS;?>" method="POST" id="submitPicksForm" name="submitPicksForm">
    <h3 class="widget-title">Contest ID: <?=$aLeague['leagueID'];?> - <?=$aLeague['name'];?></h3>
    <input type="hidden" value="<?=$aLeague['poolID'];?>" name="poolID">
    <input type="hidden" value="<?=$aLeague['leagueID'];?>" name="leagueID">
    <div class="contestStructure">
        <div class="left">
            <div>
                <div class="label"><?=__('Prize structure', FV_DOMAIN);?>:</div>
                <?=$aLeague['prize_structure'];?>
            </div>
            <div>
                <div class="label"><?=__('Sport', FV_DOMAIN);?>:</div>
                <?=$aPool['sport_name'];?>
            </div>
            <div>
                <div class="label"><?=__('Game Type', FV_DOMAIN);?>:</div>
                <?=$aLeague['gameType'];?>
            </div>
            <div>
                <div class="label"><?=__('Start', FV_DOMAIN);?>:</div>
                <?=$aLeague['startDate'];?>
            </div>
            <div>
                <div class="label"><?=__('Ends', FV_DOMAIN);?>:</div>
                Prizes paid next day
            </div>
            <div>
                <div class="label"><?=__('Creator', FV_DOMAIN);?>:</div>
                <?=$creator->data->user_login;?>
            </div>
            <div>
                <div class="label"><?=__('Players', FV_DOMAIN);?>:</div>
                <?=$aLeague['size'];?> player game, <?=$aLeague['entries'];?> entries</td>
            </div>
        </div>
        <div class="right">
            <div class="boxEntry">
                <span><?=__('Entry', FV_DOMAIN);?></span> $<?=$aLeague['entry_fee'];?>
            </div>
            <div class="boxPrizes">
                <span><?=__('Prizes', FV_DOMAIN);?></span> $<?=$aLeague['prizes'];?>
            </div>
        </div>
    </div>
    
    <?php if($otherLeagues != null):?>
    <p><?=__('Below is a list of games you have already entered for this event. Simply click on \'Import Picks\' to import your picks from that game.', FV_DOMAIN)?></p>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th><?=__('Contest ID', FV_DOMAIN)?></th>
                <th><?=__('Name', FV_DOMAIN)?></th>
                <th><?=__('Opponent', FV_DOMAIN)?></th>
                <th><?=__('Type', FV_DOMAIN)?></th>
                <th><?=__('Entry Fee', FV_DOMAIN)?></th>
                <th><?=__('Size', FV_DOMAIN)?></th>
                <th><?=__('Structure', FV_DOMAIN)?></th>
                <th colspan="2">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($otherLeagues as $otherLeague):?>
            <tr>
                <td>
                    <div><?=$otherLeague['leagueID'];?></div>
                </td>
                <td>
                    <div><?=$otherLeague['name'];?></div>
                </td>
                <td>
                    <div><?=$otherLeague['opponent'];?></div>
                </td>
                <td>
                    <div><?=$otherLeague['gameType'];?></div>
                </td>
                <td>
                    <div><?=$otherLeague['entry_fee'];?></div>
                </td>
                <td>
                    <div><?=$otherLeague['size'];?></div>
                </td>
                <td>
                    <div><?=$otherLeague['prize_structure'];?></div>
                </td>
                <td colspan="2">
                    <div>
                        <input type="button" value="<?=__('Import Picks', FV_DOMAIN)?>" onclick="importPicks('<?=$otherLeague["winnerID"];?>', '<?=$otherLeague["methodID"];?>', '<?=$otherLeague["roundID"];?>', '<?=$otherLeague["minuteID"];?>')" class="btn btn-success">
                    </div>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <?php endif;?>
    <table border="0" class="table table-striped table-bordered table-responsive table-condensed">
        <tbody>
            <?php foreach($aFights as $aFight):?>
            <tr>
                <td style="text-align:center">
                    <?=$aFight['allow_spread'] ? $aFight['team1_spread_points'] : '';?>
                    <?=$aFight['allow_moneyline'] ? $aFight['team1_moneyline'] : '';?>
                    <br><?=$aFight['name1'];?>
                    <br>&nbsp;
                    <br>
                    <input type="radio" class="fightID" value="<?=$aFight['fighterID1'];?>" name="winner<?=$aFight['fightID'];?>" data-fightid="<?=$aFight['fightID'];?>" <?php if($aFight['winnerID'] == $aFight['fighterID1']):?>checked="checked"<?php endif;?>>
                    <br>
                </td>
                <td style="text-align:center">
                    <?=$aFight['allow_spread'] ? __('Spread') : '';?>
                    <?=$aFight['allow_moneyline'] ? __('Money Line') : '';?>
                    <br>
                    <br>
                    <br>VS</td>
                <td style="text-align:center">
                    <?=$aFight['allow_spread'] ? $aFight['team2_spread_points'] : '';?>
                    <?=$aFight['allow_moneyline'] ? $aFight['team2_moneyline'] : '';?>
                    <br><?=$aFight['name2'];?>
                    <br>&nbsp;
                    <br>
                    <input type="radio" class="fightID" value="<?=$aFight['fighterID2'];?>" name="winner<?=$aFight['fightID'];?>" data-fightid="<?=$aFight['fightID'];?>" <?php if($aFight['winnerID'] == $aFight['fighterID2']):?>checked="checked"<?php endif;?>>
                    <br>
                </td>
                <?php if($aMethods != null):?>
                <td align="center">
                    <select onchange="checkMethod(this.value,<?=$aFight['fightID'];?>)" class="method" data-id="<?=$aFight['fightID'];?>" id="method<?=$aFight['fightID'];?>" name="method<?=$aFight['fightID'];?>" style="width:205px">
                        <option value="-1">-- Select Method --</option>
                        <?php foreach($aMethods as $aMethod):?>
                        <option value="<?=$aMethod["methodID"];?>" <?php if($aFight['methodID'] == $aMethod["methodID"]):?>selected="true"<?php endif;?>>
                            <?=$aMethod["description"];?>
                        </option>
                        <?php endforeach;?>
                    </select>
                    <br>
                    <br>
                    <select id="round<?=$aFight['fightID'];?>" name="round<?=$aFight['fightID'];?>" style="width:205px">
                        <option value="-1">-- Select Round --</option>
                        <option value="1" <?php if($aFight['roundID'] == 1):?>selected="true"<?php endif;?>>1</option>
                        <option value="2" <?php if($aFight['roundID'] == 2):?>selected="true"<?php endif;?>>2</option>
                        <option value="3" <?php if($aFight['roundID'] == 3):?>selected="true"<?php endif;?>>3</option>
                    </select>
                </td>
                <?php endif;?>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12"><br><br>
            <div style="text-align:center">
                <input type="submit" class="btn btn-primary" value="<?=__('Enter', FV_DOMAIN)?>" name="SubmitPicks" onclick="return pickSelected(771)">
            </div>
            <div style="text-align:center;margin-top:10px;">
                <?=__('The league will appear in the My Upcoming Entries table after you submit your picks.', FV_DOMAIN)?>
            </div>
        </div>
    </div>
</form>
</div>