<form action="<?=FANVICTOR_URL_SUBMIT_PICKS;?>" method="POST" id="submitPicksForm" name="submitPicksForm">
    <input type="hidden" value="<?=$aLeague['poolID'];?>" name="poolID">
    <input type="hidden" value="<?=$aLeague['leagueID'];?>" name="leagueID">
    <table width="100%" border="0" class="table table-condensed table-responsive">
        <tr>
            <td valign="top">
                <h3>Contest ID: <?=$aLeague['leagueID'];?> - <?=$aLeague['name'];?></h3>
            </td>
            <td valign="top" align="right"></td>
        </tr>
        <tr class="info">
            <td colspan="2">
                <table width="100%" border="0" class="table table-responsive">
                    <tbody>
                        <tr class="info">
                            <td colspan="2">
                                <br>&nbsp;&nbsp;<b>Prize structure:</b> <?=$aLeague['prize_structure'];?>
                                <br>&nbsp;&nbsp;<b>Sport:</b> <?=$aPool['sport_name'];?>
                                <br>&nbsp;&nbsp;<b>Game Type:</b> <?=$aLeague['gameType'];?>
                                <br>&nbsp;&nbsp;<b>Start:</b> <?=$aLeague['startDate'];?>
                                <br>&nbsp;&nbsp;<b>Ends:</b> Prizes paid next day
                                <br>&nbsp;&nbsp;<b>Creator:</b> <?=$creator->data->user_login;?>
                                <br>&nbsp;&nbsp;<b>Players:</b> <?=$aLeague['size'];?> player game, <?=$aLeague['entries'];?> entries</td>
                            <td width="170" align="center">
                                <br>
                                <div style="height:40px;-moz-border-radius: 5px;-webkit-border-radius: 5px;border: 1px solid #000;padding: 10px;background-color: #E6E6E6;">
                                    <font size="4"><b>Entry</b> $<?=$aLeague['entry_fee'];?></font>
                                </div>
                            </td>
                            <td style="width:15px">&nbsp;</td>
                            <td width="170" align="center">
                                <br>
                                <div style=" height:40px;-moz-border-radius: 5px;-webkit-border-radius: 5px;border: 1px solid #000;padding: 10px;background-color: #E6E6E6;">
                                    <font size="4"><b>Prizes</b> $<?=$aLeague['prizes'];?></font>
                                </div>
                            </td>
                            <td style="width:15px">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <?php if($otherLeagues != null):?>
    <p>Below is a list of games you have already entered for this event. Simply click on 'Import Picks' to import your picks from that game.</p>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Contest ID</th>
                <th>Name</th>
                <th>Opponent</th>
                <th>Type</th>
                <th>Entry Fee</th>
                <th>Size</th>
                <th>Structure</th>
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
                        <input type="button" value="Import Picks" onclick="importPicks('<?=$otherLeague["winnerID"];?>', '<?=$otherLeague["methodID"];?>', '<?=$otherLeague["roundID"];?>', '<?=$otherLeague["minuteID"];?>')" class="btn btn-success">
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
                <input type="submit" class="btn btn-primary" value="Enter" name="SubmitPicks" onclick="return pickSelected(771)">
            </div>
            <div style="text-align:center;margin-top:10px;">
                The league will appear in the My Upcoming Entries table after you submit your picks.
            </div>
        </div>
    </div>
</form>