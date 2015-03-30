<div class="wrap">    <h2>        <?=!$bIsEdit ? __("Add Contests", FV_DOMAIN) : __("Edit Contests", FV_DOMAIN);?>        <?php if($bIsEdit):?>        <a class="add-new-h2" href="<?=self::$urladdnew;?>"><?=__("Add New", FV_DOMAIN);?></a>        <?php endif;?>    </h2>        <?php if($aSports != null && $aPools != null && $aPools != '[]'):?>    <?=settings_errors();?>    <form method="post" action="" enctype="multipart/form-data">        <input type="hidden" id="leagueIDData" name="leagueID" value="<?=$aForms['leagueID'];?>" />        <input type="hidden" id="poolData" value='<?=$aPools;?>' />        <input type="hidden" id="fightData" value='<?=$aFights;?>' />                <input type="hidden" id="roundData" value='<?=$aRounds;?>' />        <input type="hidden" id="selectPool" value='<?=$aForms['poolID'];?>' />        <input type="hidden" id="selectFight" value='<?=json_encode(explode(',', $aForms['fixtures']));?>' />                <input type="hidden" id="selectRound" value='<?=json_encode(explode(',', $aForms['rounds']));?>' />        <input type="hidden" id="winnerPercent" value='<?=get_option('fanvictor_winner_percent');?>' />        <input type="hidden" id="firstPercent" value='<?=get_option('fanvictor_first_place_percent');?>' />        <input type="hidden" id="secondPercent" value='<?=get_option('fanvictor_second_place_percent');?>' />        <input type="hidden" id="thirdPercent" value='<?=get_option('fanvictor_third_place_percent');?>' />                <input type="hidden" id="positionData" value='<?=$aPositions;?>' />                <input type="hidden" id="lineupData" value='<?=$aForms['lineup'];?>' />        <table class="form-table">                        <tr valign="top">                <th scope="row"><?=__('Clone contest', FV_DOMAIN);?></th>                <td>                                        <input type="checkbox" name="clone" value="1" <?php if($aForms['clone']):?>checked="true" <?php endif;?> />                                    </td>            </tr>            <tr valign="top">                <th scope="row"><?=__('Pick your sport');?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>                <td>                    <?php if($aSports != null):?>                        <select id="sports" name="organizationID" onchange="jQuery.createcontest.loadPools(jQuery(this).val(), jQuery('option:selected', this).attr('playerdraft'), jQuery('option:selected', this).attr('only_playerdraft'), jQuery('option:selected', this).attr('is_round'), jQuery('option:selected', this).attr('is_team')); jQuery.createcontest.loadPosition();jQuery.createcontest.optionType();">                        <?php foreach($aSports as $aSport):?>                            <?php if(!empty($aSport['child']) && is_array($aSport['child']) && $aSport['child'] != null):?>                            <option disabled="true"><?=$aSport['name'];?></option>                            <?php foreach($aSport['child'] as $aOrg):?>                                <?php if($aOrg['is_active'] == 1):?>                                <option value="<?=$aOrg['id'];?>" only_playerdraft="<?=$aOrg['only_playerdraft'];?>" playerdraft="<?=$aOrg['is_playerdraft'];?>" is_team="<?=$aOrg['is_team'];?>" is_round="<?=$aOrg['is_round'];?>" style="padding-left: 20px" <?php if($aForms['organizationID'] == $aOrg['id']):?>selected="true"<?php endif;?>>                                    <?=$aOrg['name'];?>                                </option>                                <?php endif;?>                            <?php endforeach;?>                            <?php endif;?>                        <?php endforeach;?>                        </select>                    <?php endif;?>                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__('Events', FV_DOMAIN);?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>                <td id="poolDates"></td>            </tr>            <tr valign="top" class="for_team" style="display: none">                <th scope="row"><?=__('Fixture Selection', FV_DOMAIN);?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>                <td id="fixtureDiv"></td>            </tr>                        <tr valign="top" id="wrapRounds">                <th scope="row"><?=__('Rounds', FV_DOMAIN);?> <span class="description">(<?=__("at least two rounds", FV_DOMAIN);?>)</span></th>                <td id="roundDiv"></td>            </tr>                        <tr valign="top">                <th scope="row"><?=__("Game Type", FV_DOMAIN);?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>                <td>                    <select class="form-control" name="game_type" id="game_type" onchange="jQuery.createcontest.gameTypeAttr()">                        <?php foreach($aGameTypes as $aGameType):?>                            <option value="<?=$aGameType['value'];?>" id="<?=$aGameType['value'];?>Type" <?php if(strtolower($aForms['gameType']) == $aGameType['value']):?>selected="true"<?php endif;?>>                                <?=$aGameType['name'];?>                            </option>                        <?php endforeach;?>                    </select>                </td>            </tr>                        <tr valign="top" id="wrapOptionType" style="display: none">                <th scope="row"><?=__("Options", FV_DOMAIN);?> <span class="description">(<?=__("required", FV_DOMAIN);?>)</span></th>                <td>                    <select class="form-control" disabled="true" name="option_type" id="optionType" onchange="jQuery.createcontest.optionType()">                        <option value="salary" <?php if(strtolower($aForms['option_type']) == 'salary'):?>selected="true"<?php endif;?>>                            <?=__('Salary', FV_DOMAIN);?>                        </option>                        <option value="group" <?php if(strtolower($aForms['option_type']) == 'group'):?>selected="true"<?php endif;?>>                            <?=__('Group', FV_DOMAIN);?>                        </option>                                            </select>                </td>            </tr>                        <tr valign="top" class="for_playerdraft for_group">                <th scope="row"><?=__("Lineup", FV_DOMAIN);?></th>                <td>                    <div id="lineupResult"></div>                    <p><?=__('If all values are 0, it will get default lineup', FV_DOMAIN);?></p>                </td>            </tr>            <tr valign="top" class="for_playerdraft">                <th scope="row"><?=__('Salary Cap', FV_DOMAIN);?></th>                <td>                    <input type="text" name="salary_remaining" value="<?=number_format($aForms['salary_remaining']);?>"  onkeyup="this.value = accounting.formatNumber(this.value)">                    <p><?=__('If this value is not set, it will get default event \'s salary cap', FV_DOMAIN);?></p>                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__('Opponent', FV_DOMAIN);?></th>                <td>                    <div class="radio">                        <label>                            <input type="radio" name="opponent" id="oppoRadio1" value="public" checked="true">                            <?=__('Anyone', FV_DOMAIN);?>                          </label>                    </div>                    <div class="radio">                        <label>                            <input type="radio" name="opponent" id="oppoRadio1" value="private" <?php if(strtolower($aForms['opponent']) == "private"):?>checked="true"<?php endif;?>>                            <?=__('Friends Only', FV_DOMAIN);?>                        </label>                    </div>                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__('Contest Type', FV_DOMAIN);?></th>                <td>                    <div class="radio">                        <label>                            <input type="radio" name="type" value="head2head" checked="true">                            <?=__('Head to head', FV_DOMAIN);?> 	                        </label>                    </div>                    <div class="radio">                        <label>                            <input type="radio" name="type" value="league" <?php if($aForms['size'] > 2):?>checked="true"<?php endif;?>>                            <?=__('League', FV_DOMAIN);?>	                        </label>                    </div>                </td>            </tr>			            <tr valign="top" <?php if($aForms['size'] == '' || $aForms['size'] == 2):?>style="display: none"<?php endif;?> class="leagueDiv" onchange="jQuery.createcontest.calculatePrizes()">                <th scope="row"><?=__('League Size', FV_DOMAIN);?></th>                <td>                    <select class="form-control" name="leagueSize" id="leagueSize">                        <?php foreach($aLeagueSizes as $aLeagueSize):?>                            <option value="<?=$aLeagueSize;?>" <?php if($aForms['size'] == $aLeagueSize):?>selected="true"<?php endif;?>>                                <?=$aLeagueSize;?>                            </option>                        <?php endforeach;?>                    </select>                </td>            </tr>            <tr valign="top" <?php if($aForms['size'] == '' || $aForms['size'] == 2):?>style="display: none"<?php endif;?> class="leagueDiv">                <th scope="row"><?=__('Prize Structure', FV_DOMAIN);?></th>                <td>                    <div class="radio">                        <label>                            <input type="radio" name="structure" value="winnertakeall" checked="true">                            <?=__('Winner takes all', FV_DOMAIN);?>                         </label>                    </div>                    <div class="radio">                        <label>                            <input type="radio" name="structure" value="top3" <?php if(strtolower($aForms['prize_structure']) == "top_3"):?>checked="true"<?php endif;?>>                            <?=__('Top 3 get prizes', FV_DOMAIN);?>                         </label>                    </div>                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__('Entry Fee', FV_DOMAIN);?></th>                <td>                    <select class="form-control" id="entry_fee" name="entry_fee" onchange="jQuery.createcontest.calculatePrizes()">                        <option value="0"><?=__('Free Practice', FV_DOMAIN);?></option>                         <?php foreach($aEntryFees as $aEntryFee):?>                            <option value="<?=$aEntryFee;?>" <?php if($aForms['entry_fee'] == $aEntryFee):?>selected="true"<?php endif;?>>                                <?=$aEntryFee;?>                            </option>                        <?php endforeach;?>                    </select>                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__('Name your league', FV_DOMAIN);?></th>                <td>                    <input type="text" id="leaguename" name="leaguename" placeholder="Name your league" value="<?=$aForms['name'];?>">                </td>            </tr>            <tr valign="top">                <th scope="row"><?=__('Prizes', FV_DOMAIN);?></th>                <td>                    <div name="prizesum" id="prizesum"></div>                </td>            </tr>                        <tr valign="top">                <th scope="row"><?=__("Note", FV_DOMAIN);?></th>                <td>                    <textarea rows="5" class="large-text code" name="note"><?=$aForms['note'];?></textarea>                </td>            </tr>        </table>        <?php submit_button(); ?>    </form>    <?php else:?>    <?=__("There are no events.", FV_DOMAIN);?>    <?php endif;?></div>