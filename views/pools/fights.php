<?php foreach($aFights as $aFight):?>
<div class="fight_container">
    <div class="title_area">
        <div class="fight_number_title">*<?=__("Fixture");?> <?=$aFight['count'];?></div>
        <a onclick="return jQuery.fight.removeFight(this);" class="fight_action fight_remove" href="#">
            <img src="<?=FANVICTOR__PLUGIN_URL_IMAGE.'delete.png';?>" alt="Delete" title="Delete" />
        </a>&nbsp;&nbsp;
        <a onclick="return jQuery.fight.addFight(this);" class="fight_action fight_add" href="#">
            <img src="<?=FANVICTOR__PLUGIN_URL_IMAGE.'add.png';?>" alt="Add" title="Add" />
        </a>
        <input type="hidden" name="val[fight][]" class="fight" value="" />
        <input type="hidden" data-name="fightID" value="<?=$aFight['fightID'];?>" />
    </div>
    <table>
        <tr>
            <th>
                <span class="for_fighter"><?=__("Fighter");?> 1</span>
                <span class="for_team"><?=__("Team");?> 1</span>
            </th>
            <th>
                <span class="for_fighter"><?=__("Fighter");?> 2</span>
                <span class="for_team"><?=__("Team");?> 2</span>
            </th>
        </tr>
        <tr>
            <td>
                <select data-name="fighterID1" data-sel="<?=$aFight['fighterID1'];?>" class="cbfighter for_fighter"></select>
                <select data-name="fighterID1" data-sel="<?=$aFight['fighterID1'];?>" style="display: none" class="cbteam for_team"></select>
            </td>
            <td>
                <select data-name="fighterID2" data-sel="<?=$aFight['fighterID2'];?>" class="cbfighter for_fighter"></select>
                <select data-name="fighterID2" data-sel="<?=$aFight['fighterID2'];?>" style="display: none" class="cbteam for_team"></select>
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <div class="table">
                    <div class="table_left">
                        <?=__("Fixture Name");?>  <span class="description">(<?=__("required");?>)</span>:
                    </div>
                    <div class="table_right">
                        <input type="text" data-name="fight_name" value="<?=$aFight['name'];?>" size="40"/>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <div class="table">
                    <div class="table_left">
                        <?=__("Start Date");?>  <span class="description">(<?=__("required");?>)</span>:
                    </div>
                    <div class="table_right">
                        <input type="text" class="fightDatePicker" data-name="fight_startDate" value="<?=$aFight['startDateOnly'];?>" size="40"/>
                        <?=__("Hour");?>:
                        <select data-name="fight_startHour">
                            <?php foreach($aPoolHours as $aPoolHour):?>
                            <option value="<?=$aPoolHour;?>" <?=$aFight['startHour'] == $aPoolHour ? 'selected="true"' : '';?>><?=$aPoolHour;?></option>
                            <?php endforeach;?>
                        </select>
                        <?=__("Minute");?>:
                        <select data-name="fight_startMinute">
                            <?php foreach($aPoolMinutes as $aPoolMinute):?>
                            <option value="<?=$aPoolMinute;?>" <?=$aFight['startMinute'] == $aPoolMinute ? 'selected="true"' : '';?>><?=$aPoolMinute;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="6">
                <div class="table">
                    <div class="table_left">
                        <?=__("Championship Fight");?>:
                    </div>
                    <div class="table_right">
                        <input type="checkbox" data-name="champFight" <?=isset($aFight['champFight']) && $aFight['champFight'] == 'YES' ? 'checked="true"' : '';?> value="1" id="champFight" />
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="6">
                <div class="table">
                    <div class="table_left">
                        <?=__("Amateur Fight");?>:
                    </div>
                    <div class="table_right">
                        <input type="checkbox" data-name="amateurFight" <?=isset($aFight['amateurFight']) && $aFight['amateurFight'] == 'YES' ? 'checked="true"' : '';?> value="1" id="amateurFight" />
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="6">
                <div class="table">
                    <div class="table_left">
                        <?=__("Main Card Fight");?>:
                    </div>
                    <div class="table_right">
                        <input type="checkbox" data-name="mainFight" <?=isset($aFight['mainFight']) && $aFight['mainFight'] == 'YES' ? 'checked="true"' : '';?> value="1" id="mainFight" />
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="6">
                <div class="table">
                    <div class="table_left">
                        <?=__("Preliminary Card Fight");?>:
                    </div>
                    <div class="table_right">
                        <input type="checkbox" data-name="prelimFight" <?=isset($aFight['prelimFight']) && $aFight['prelimFight'] == 'YES' ? 'checked="true"' : '';?> value="1" id="prelimFight" />
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="6">
                <div class="table">
                    <div class="table_left">
                        <?=__("Round");?>:
                    </div>
                    <div class="table_right">
                        <select data-name="rounds">
                            <option value="">--</option>
                            <?php foreach($aRounds as $aRound):?>
                            <option value="{$aRound}" <?=isset($aFight['rounds']) && $aFight['rounds'] == $aRound ? 'selected="true"' : '';?>><?=$aRound;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
    </table>
</div>
<?php endforeach;?>